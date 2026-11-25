<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Teknologi Informasi',
                'Sumber Daya Manusia',
                'Keuangan',
                'Marketing',
                'Operasional',
                'Administrasi',
            ]).'-'.fake()->unique()->randomNumber(3),
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
