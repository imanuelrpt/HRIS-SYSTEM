<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\LeaveApprovalService;

class LeaveRequestPolicy
{
    public function __construct(private readonly LeaveApprovalService $approvals) {}

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        return $this->isOwn($user, $leaveRequest) || $this->viewAll($user);
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function cancel(User $user, LeaveRequest $leaveRequest): bool
    {
        return $this->isOwn($user, $leaveRequest) && $leaveRequest->isPending();
    }

    public function approve(User $user, LeaveRequest $leaveRequest): bool
    {
        return $this->approvals->canApprove($leaveRequest, $user);
    }

    public function reject(User $user, LeaveRequest $leaveRequest): bool
    {
        return $this->approvals->canApprove($leaveRequest, $user);
    }

    public function viewAll(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        return false;
    }

    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        return false;
    }

    public function restore(User $user, LeaveRequest $leaveRequest): bool
    {
        return false;
    }

    public function forceDelete(User $user, LeaveRequest $leaveRequest): bool
    {
        return false;
    }

    private function isOwn(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->id === $leaveRequest->user_id;
    }
}
