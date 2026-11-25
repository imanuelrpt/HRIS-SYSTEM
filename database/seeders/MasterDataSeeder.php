<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Holiday;
use App\Models\LeaveType;
use App\Models\Position;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Teknologi Informasi', 'code' => 'IT', 'description' => 'Pengembangan dan infrastruktur sistem.'],
            ['name' => 'Sumber Daya Manusia', 'code' => 'HR', 'description' => 'Rekrutmen, karyawan, dan kesejahteraan.'],
            ['name' => 'Keuangan', 'code' => 'FIN', 'description' => 'Akuntansi, anggaran, dan pembayaran.'],
        ];

        foreach ($departments as $department) {
            Department::query()->firstOrCreate(['name' => $department['name']], $department);
        }

        $positions = [
            ['department' => 'Teknologi Informasi', 'name' => 'Web Developer'],
            ['department' => 'Teknologi Informasi', 'name' => 'IT Support'],
            ['department' => 'Sumber Daya Manusia', 'name' => 'HR Staff'],
            ['department' => 'Keuangan', 'name' => 'Finance Staff'],
            ['department' => 'Teknologi Informasi', 'name' => 'Manager'],
        ];

        foreach ($positions as $position) {
            $department = Department::query()->where('name', $position['department'])->firstOrFail();

            Position::query()->firstOrCreate([
                'department_id' => $department->id,
                'name' => $position['name'],
            ]);
        }

        $leaveTypes = [
            ['name' => 'Cuti Tahunan', 'quota_days' => 12, 'deducts_balance' => true, 'requires_attachment' => false],
            ['name' => 'Cuti Sakit', 'quota_days' => 0, 'deducts_balance' => false, 'requires_attachment' => true],
            ['name' => 'Izin', 'quota_days' => 0, 'deducts_balance' => false, 'requires_attachment' => false],
            ['name' => 'Cuti Melahirkan', 'quota_days' => 90, 'deducts_balance' => false, 'requires_attachment' => true],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::query()->firstOrCreate(['name' => $leaveType['name']], $leaveType);
        }

        $year = now()->year;
        $holidays = [
            ["$year-01-01", 'Tahun Baru Masehi'],
            ["$year-03-20", 'Hari Raya Idul Fitri'],
            ["$year-03-21", 'Hari Raya Idul Fitri'],
            ["$year-05-01", 'Hari Buruh Internasional'],
            ["$year-05-27", 'Hari Raya Idul Adha'],
            ["$year-06-13", 'Hari Kenaikan Isa Almasih'],
            ["$year-08-17", 'Hari Kemerdekaan Republik Indonesia'],
            ["$year-08-26", 'Maulid Nabi Muhammad SAW'],
            ["$year-12-25", 'Hari Natal'],
        ];

        foreach ($holidays as [$date, $description]) {
            Holiday::query()->firstOrCreate(['date' => $date], ['description' => $description]);
        }
    }
}
