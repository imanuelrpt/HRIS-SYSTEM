<?php

namespace Database\Factories;

use App\Enums\LeaveStatus;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-30 days', '+30 days');
        $endDate = (clone $startDate)->modify('+'.fake()->numberBetween(0, 3).' days');

        return [
            'user_id' => User::factory(),
            'leave_type_id' => LeaveType::factory(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_days' => fake()->numberBetween(1, 4),
            'reason' => fake()->sentence(),
            'attachment_path' => null,
            'status' => LeaveStatus::PendingHr,
        ];
    }

    public function withStatus(LeaveStatus $status): static
    {
        return $this->state(fn (array $attributes) => ['status' => $status]);
    }
}
