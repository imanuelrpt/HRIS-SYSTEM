<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */
class PositionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => fake()->randomElement([
                'Staff',
                'Supervisor',
                'Manager',
                'Asisten Manager',
                'Kepala Bagian',
            ]),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
