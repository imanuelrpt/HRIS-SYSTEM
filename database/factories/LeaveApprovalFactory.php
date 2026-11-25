<?php

namespace Database\Factories;

use App\Enums\ApprovalAction;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveApproval>
 */
class LeaveApprovalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'leave_request_id' => LeaveRequest::factory(),
            'approver_id' => User::factory(),
            'level' => 1,
            'action' => ApprovalAction::Approve,
            'note' => null,
            'acted_at' => now(),
        ];
    }
}
