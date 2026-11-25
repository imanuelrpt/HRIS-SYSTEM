<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => fake()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
            'clock_in' => '08:0'.fake()->numberBetween(0, 9),
            'clock_out' => '17:0'.fake()->numberBetween(0, 9),
            'ip_address' => fake()->localIp(),
            'notes' => null,
            'status' => AttendanceStatus::Hadir,
            'is_manual' => false,
        ];
    }

    public function withStatus(AttendanceStatus $status): static
    {
        return $this->state(fn (array $attributes) => ['status' => $status]);
    }
}
