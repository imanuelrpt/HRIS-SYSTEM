<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement(['login', 'update', 'create', 'delete', 'approval']),
            'description' => fake()->sentence(),
            'subject_type' => null,
            'subject_id' => null,
            'ip' => fake()->localIp(),
        ];
    }
}
