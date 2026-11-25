<?php

namespace App\Services;

use App\Enums\ApprovalAction;
use App\Enums\LeaveStatus;
use App\Enums\Role;
use App\Models\LeaveApproval;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Notifications\LeaveNotification;
use DomainException;
use Illuminate\Support\Facades\DB;

class LeaveApprovalService
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
        private readonly LeaveBalanceService $balances,
    ) {}

    public function currentLevel(LeaveRequest $leaveRequest): ?int
    {
        return match ($leaveRequest->status) {
            LeaveStatus::PendingManager => 1,
            LeaveStatus::PendingHr => 2,
            default => null,
        };
    }

    public function expectedApprover(LeaveRequest $leaveRequest): ?User
    {
        $level = $this->currentLevel($leaveRequest);

        if ($level === null) {
            return null;
        }

        if ($level === 1) {
            $employee = User::query()->find($leaveRequest->user_id);
            $manager = $employee === null
                ? null
                : User::query()
                    ->whereKey($employee->manager_id)
                    ->where('role', Role::Manager->value)
                    ->where('is_active', true)
                    ->first();

            if ($manager !== null) {
                return $manager;
            }
        }

        return User::query()
            ->where('role', Role::Admin->value)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();
    }

    public function canApprove(LeaveRequest $leaveRequest, User $approver): bool
    {
        $level = $this->currentLevel($leaveRequest);

        if ($level === null || ! $approver->is_active || $this->hasActedAtLevel($leaveRequest->id, $level)) {
            return false;
        }

        if ($level === 2) {
            return $approver->isAdmin() && $this->hasApprovedLevel($leaveRequest->id, 1);
        }

        if ($approver->isAdmin()) {
            return true;
        }

        if (! $approver->isManager()) {
            return false;
        }

        $employee = User::query()->find($leaveRequest->user_id);

        return $employee !== null && $employee->manager_id === $approver->id;
    }

    public function approve(LeaveRequest $leaveRequest, User $approver, ?string $note = null): LeaveRequest
    {
        return DB::transaction(function () use ($leaveRequest, $approver, $note): LeaveRequest {
            $locked = $this->lockRequest($leaveRequest);

            if (! $this->canApprove($locked, $approver)) {
                throw new DomainException('Pengajuan ini tidak dapat disetujui oleh akun Anda.');
            }

            $level = $this->currentLevel($locked);

            $locked->approvals()->create([
                'approver_id' => $approver->id,
                'level' => $level,
                'action' => ApprovalAction::Approve,
                'note' => $note,
                'acted_at' => now(),
            ]);

            $nextStatus = $level === 1 ? LeaveStatus::PendingHr : LeaveStatus::Approved;
            $locked->update(['status' => $nextStatus]);

            $employee = User::query()->findOrFail($locked->user_id);
            $leaveType = LeaveType::query()->findOrFail($locked->leave_type_id);

            if ($level === 2 && $leaveType->deducts_balance) {
                $this->balances->increaseUsed(
                    $employee->id,
                    $leaveType->id,
                    $locked->start_date->year,
                    $locked->total_days,
                );
            }

            $message = $level === 1
                ? "Pengajuan cuti {$leaveType->name} {$employee->name} telah disetujui atasan dan menunggu persetujuan HR."
                : "Pengajuan cuti {$leaveType->name} {$employee->name} telah disetujui HR.";

            $this->notify(
                [$employee, $this->expectedApprover($locked)],
                new LeaveNotification(
                    $locked,
                    $level === 1 ? 'Cuti disetujui atasan' : 'Cuti disetujui HR',
                    $message,
                    $nextStatus->value,
                ),
            );

            $description = $level === 1
                ? "{$approver->name} menyetujui level 1 pengajuan cuti {$employee->name}."
                : "{$approver->name} menyetujui level 2 pengajuan cuti {$employee->name}.";

            $this->activityLog->log(
                $level === 1 ? 'leave.approved_level_1' : 'leave.approved',
                $description,
                $locked,
                $approver,
            );

            return $locked->refresh();
        });
    }

    public function reject(LeaveRequest $leaveRequest, User $approver, ?string $note = null): LeaveRequest
    {
        return DB::transaction(function () use ($leaveRequest, $approver, $note): LeaveRequest {
            $locked = $this->lockRequest($leaveRequest);

            if (! $this->canApprove($locked, $approver)) {
                throw new DomainException('Pengajuan ini tidak dapat ditolak oleh akun Anda.');
            }

            $level = $this->currentLevel($locked);

            $locked->approvals()->create([
                'approver_id' => $approver->id,
                'level' => $level,
                'action' => ApprovalAction::Reject,
                'note' => $note,
                'acted_at' => now(),
            ]);

            $locked->update(['status' => LeaveStatus::Rejected]);

            $employee = User::query()->findOrFail($locked->user_id);
            $leaveType = LeaveType::query()->findOrFail($locked->leave_type_id);

            $this->notify(
                [$employee],
                new LeaveNotification(
                    $locked,
                    'Pengajuan cuti ditolak',
                    "Pengajuan cuti {$leaveType->name} {$employee->name} ditolak pada level {$level}.",
                    LeaveStatus::Rejected->value,
                ),
            );

            $this->activityLog->log(
                'leave.rejected',
                "{$approver->name} menolak pengajuan cuti {$employee->name} pada level {$level}.",
                $locked,
                $approver,
            );

            return $locked->refresh();
        });
    }

    private function lockRequest(LeaveRequest $leaveRequest): LeaveRequest
    {
        return LeaveRequest::query()
            ->whereKey($leaveRequest->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function hasActedAtLevel(int $leaveRequestId, int $level): bool
    {
        return LeaveApproval::query()
            ->where('leave_request_id', $leaveRequestId)
            ->where('level', $level)
            ->exists();
    }

    private function hasApprovedLevel(int $leaveRequestId, int $level): bool
    {
        return LeaveApproval::query()
            ->where('leave_request_id', $leaveRequestId)
            ->where('level', $level)
            ->where('action', ApprovalAction::Approve->value)
            ->exists();
    }

    /**
     * @param  array<int, User|null>  $recipients
     */
    private function notify(array $recipients, LeaveNotification $notification): void
    {
        $notified = [];

        foreach ($recipients as $recipient) {
            if ($recipient === null || isset($notified[$recipient->id])) {
                continue;
            }

            $notified[$recipient->id] = true;
            $recipient->notify($notification);
        }
    }
}
