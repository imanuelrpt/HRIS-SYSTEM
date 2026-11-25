<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Holiday>
 */
class HolidayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date' => fake()->unique()->dateTimeBetween('-1 year', '+1 year')->format('Y-m-d'),
            'description' => fake()->randomElement([
                'Hari Raya Idul Fitri',
                'Hari Kemerdekaan Republik Indonesia',
                'Hari Raya Idul Adha',
                'Tahun Baru Masehi',
                'Hari Buruh Internasional',
            ]),
        ];
    }
}
