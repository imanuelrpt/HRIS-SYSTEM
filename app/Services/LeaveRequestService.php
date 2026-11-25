<?php

namespace App\Services;

use App\Enums\LeaveStatus;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Notifications\LeaveNotification;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class LeaveRequestService
{
    public function __construct(
        private readonly WorkCalendarService $workCalendar,
        private readonly LeaveBalanceService $balances,
        private readonly LeaveApprovalService $approvals,
        private readonly ActivityLogService $activityLog,
    ) {}

    /**
     * @param  array{
     *     leave_type_id: int,
     *     start_date: string,
     *     end_date: string,
     *     reason: string,
     *     attachment?: UploadedFile|null
     * }  $data
     */
    public function create(User $user, array $data): LeaveRequest
    {
        $leaveType = LeaveType::query()->findOrFail($data['leave_type_id']);
        $attachment = $data['attachment'] ?? null;
        $reason = $data['reason'];

        if ($leaveType->requires_attachment && $attachment === null) {
            throw new DomainException('Lampiran wajib diunggah untuk jenis cuti ini.');
        }

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end = Carbon::parse($data['end_date'])->startOfDay();

        if ($end->lessThan($start)) {
            throw new DomainException('Tanggal selesai tidak boleh sebelum tanggal mulai.');
        }

        $totalDays = $this->workCalendar->countWorkingDays($start, $end);

        if ($totalDays < 1) {
            throw new DomainException('Rentang tanggal tersebut tidak memiliki hari kerja.');
        }

        $this->assertBalance($user, $leaveType, $start, $totalDays);
        $this->assertNoConflict($user->id, $start, $end);

        return DB::transaction(function () use ($user, $leaveType, $attachment, $reason, $start, $end, $totalDays): LeaveRequest {
            $this->assertBalance($user, $leaveType, $start, $totalDays);
            $this->assertNoConflict($user->id, $start, $end, true);

            $leaveRequest = $user->leaveRequests()->create([
                'leave_type_id' => $leaveType->id,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'total_days' => $totalDays,
                'reason' => $reason,
                'attachment_path' => $attachment?->store('attachments/leave-requests', 'public'),
                'status' => LeaveStatus::PendingManager,
            ]);

            $approver = $this->approvals->expectedApprover($leaveRequest);

            if ($approver !== null) {
                $approver->notify(new LeaveNotification(
                    $leaveRequest,
                    'Pengajuan cuti baru',
                    "{$user->name} mengajukan cuti {$leaveType->name} selama {$totalDays} hari kerja.",
                    LeaveStatus::PendingManager->value,
                ));
            }

            $this->activityLog->log(
                'leave.requested',
                "{$user->name} mengajukan cuti {$leaveType->name} selama {$totalDays} hari kerja.",
                $leaveRequest,
                $user,
            );

            return $leaveRequest;
        });
    }

    public function cancel(User $user, LeaveRequest $leaveRequest): LeaveRequest
    {
        return DB::transaction(function () use ($user, $leaveRequest): LeaveRequest {
            $locked = LeaveRequest::query()
                ->whereKey($leaveRequest->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->user_id !== $user->id || ! $locked->isPending()) {
                throw new DomainException('Pengajuan ini tidak dapat dibatalkan.');
            }

            $approver = $this->approvals->expectedApprover($locked);

            $locked->update(['status' => LeaveStatus::Cancelled]);

            if ($approver !== null) {
                $approver->notify(new LeaveNotification(
                    $locked,
                    'Pengajuan cuti dibatalkan',
                    "{$user->name} membatalkan pengajuan cuti yang menunggu persetujuan Anda.",
                    LeaveStatus::Cancelled->value,
                ));
            }

            $this->activityLog->log(
                'leave.cancelled',
                "{$user->name} membatalkan pengajuan cuti.",
                $locked,
                $user,
            );

            return $locked->refresh();
        });
    }

    private function assertBalance(User $user, LeaveType $leaveType, Carbon $start, int $totalDays): void
    {
        if (! $leaveType->deducts_balance) {
            return;
        }

        $remaining = $this->balances->remaining($user, $leaveType, $start->year);

        if ($remaining < $totalDays) {
            throw new DomainException(
                "Sisa kuota {$leaveType->name} hanya {$remaining} hari, sedangkan pengajuan ini membutuhkan {$totalDays} hari kerja.",
            );
        }
    }

    private function assertNoConflict(int $userId, Carbon $start, Carbon $end, bool $lockForUpdate = false): void
    {
        $query = LeaveRequest::query()
            ->where('user_id', $userId)
            ->whereIn('status', [
                LeaveStatus::PendingManager->value,
                LeaveStatus::PendingHr->value,
                LeaveStatus::Approved->value,
            ])
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString());

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        if ($query->exists()) {
            throw new DomainException('Tanggal cuti bertabrakan dengan pengajuan lain yang masih menunggu atau sudah disetujui.');
        }
    }
}
