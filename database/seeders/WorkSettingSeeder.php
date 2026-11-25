<?php

namespace Database\Seeders;

use App\Models\WorkSetting;
use Illuminate\Database\Seeder;

class WorkSettingSeeder extends Seeder
{
    public function run(): void
    {
        WorkSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'check_in' => '08:00',
                'check_out' => '17:00',
                'late_tolerance_minutes' => 10,
                'working_days' => [1, 2, 3, 4, 5],
            ],
        );
    }
}
