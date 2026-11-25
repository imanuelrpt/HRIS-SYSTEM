<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkSetting>
 */
class WorkSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'check_in' => '08:00',
            'check_out' => '17:00',
            'late_tolerance_minutes' => 10,
            'working_days' => [1, 2, 3, 4, 5],
        ];
    }
}
