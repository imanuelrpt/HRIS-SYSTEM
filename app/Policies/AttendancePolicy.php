<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $this->isOwn($user, $attendance) || $this->viewAll($user);
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function clockIn(User $user): bool
    {
        return $user->is_active;
    }

    public function clockOut(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $this->isOwn($user, $attendance)
            && $attendance->clock_in !== null
            && $attendance->clock_out === null;
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return false;
    }

    public function viewAll(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function viewReports(User $user): bool
    {
        return $this->viewAll($user);
    }

    private function isOwn(User $user, Attendance $attendance): bool
    {
        return $user->id === $attendance->user_id;
    }
}
