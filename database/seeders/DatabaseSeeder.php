<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WorkSettingSeeder::class,
            MasterDataSeeder::class,
            UserSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
