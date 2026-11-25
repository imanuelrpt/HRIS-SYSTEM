<?php

namespace App\Services;

use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;

class LeaveBalanceService
{
    public function generateForUser(User $user, int $year): void
    {
        foreach (LeaveType::query()->get() as $leaveType) {
            LeaveBalance::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $year,
                ],
                [
                    'quota' => $leaveType->quota_days,
                    'used' => 0,
                ],
            );
        }
    }

    public function generateForYear(int $year): int
    {
        $count = 0;

        User::query()->active()->chunkById(200, function ($users) use ($year, &$count) {
            foreach ($users as $user) {
                $this->generateForUser($user, $year);
                $count++;
            }
        });

        return $count;
    }

    public function remaining(User $user, LeaveType $leaveType, int $year): int
    {
        $balance = LeaveBalance::query()
            ->whereBelongsTo($user)
            ->whereBelongsTo($leaveType)
            ->where('year', $year)
            ->first();

        return $balance?->remaining() ?? 0;
    }

    public function increaseUsed(int $userId, int $leaveTypeId, int $year, int $days): void
    {
        LeaveBalance::query()
            ->where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->increment('used', $days);
    }

    public function decreaseUsed(int $userId, int $leaveTypeId, int $year, int $days): void
    {
        LeaveBalance::query()
            ->where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->where('used', '>=', $days)
            ->decrement('used', $days);
    }
}
