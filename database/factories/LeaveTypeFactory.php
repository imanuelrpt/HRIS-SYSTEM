<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true).' Leave',
            'quota_days' => fake()->numberBetween(0, 12),
            'deducts_balance' => false,
            'requires_attachment' => false,
        ];
    }
}
