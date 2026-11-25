<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(LeaveBalanceService $leaveBalanceService): void
    {
        $departments = Department::query()->pluck('id', 'name');
        $positions = Position::query()->pluck('id', 'name');

        $admin = $this->createUser([
            'name' => 'Siti Aminah',
            'email' => 'admin@hris.test',
            'nik' => 'HR000001',
            'role' => Role::Admin,
            'phone' => '081234567890',
            'department_id' => $departments['Sumber Daya Manusia'],
            'position_id' => $positions['HR Staff'],
        ]);

        $managerIt = $this->createUser([
            'name' => 'Budi Santoso',
            'email' => 'manager@hris.test',
            'nik' => 'HR000002',
            'role' => Role::Manager,
            'phone' => '081234567891',
            'department_id' => $departments['Teknologi Informasi'],
            'position_id' => $positions['Manager'],
        ]);

        $managerFinance = $this->createUser([
            'name' => 'Rina Wijaya',
            'email' => 'rina.wijaya@hris.test',
            'nik' => 'HR000003',
            'role' => Role::Manager,
            'phone' => '081234567892',
            'department_id' => $departments['Keuangan'],
            'position_id' => $positions['Manager'],
        ]);

        $employees = [
            ['Andi Pratama', 'karyawan@hris.test', 'Teknologi Informasi', 'Web Developer', $managerIt->id],
            ['Maya Sari', 'maya.sari@hris.test', 'Teknologi Informasi', 'IT Support', $managerIt->id],
            ['Rizky Ramadhan', 'rizky.ramadhan@hris.test', 'Teknologi Informasi', 'Web Developer', $managerIt->id],
            ['Putri Maharani', 'putri.maharani@hris.test', 'Teknologi Informasi', 'Web Developer', $managerIt->id],
            ['Dia Putri', 'dia.putri@hris.test', 'Sumber Daya Manusia', 'HR Staff', $managerFinance->id],
            ['Fajar Nugroho', 'fajar.nugroho@hris.test', 'Sumber Daya Manusia', 'HR Staff', $managerFinance->id],
            ['Latifah Handayani', 'latifah.handayani@hris.test', 'Sumber Daya Manusia', 'HR Staff', $managerFinance->id],
            ['Nurul Aini', 'nurul.aini@hris.test', 'Keuangan', 'Finance Staff', $managerFinance->id],
            ['Dimas Setiawan', 'dimas.setiawan@hris.test', 'Keuangan', 'Finance Staff', $managerFinance->id],
            ['Bagas Prakoso', 'bagas.prakoso@hris.test', 'Keuangan', 'Finance Staff', $managerFinance->id],
        ];

        foreach ($employees as $index => [$name, $email, $departmentName, $positionName, $managerId]) {
            $this->createUser([
                'name' => $name,
                'email' => $email,
                'nik' => sprintf('HR%06d', $index + 4),
                'role' => Role::Employee,
                'phone' => '08123'.sprintf('%06d', 567893 + $index),
                'department_id' => $departments[$departmentName],
                'position_id' => $positions[$positionName],
                'manager_id' => $managerId,
            ]);
        }

        User::query()->active()->chunkById(200, function ($users) use ($leaveBalanceService) {
            foreach ($users as $user) {
                $leaveBalanceService->generateForUser($user, now()->year);
            }
        });

        $this->command?->info('Seeder pengguna selesai: 1 admin, 2 manager, 10 karyawan.');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createUser(array $attributes): User
    {
        return User::query()->firstOrCreate(
            ['email' => $attributes['email']],
            array_merge([
                'password' => 'password',
                'join_date' => now()->subMonths(random_int(6, 30))->toDateString(),
                'is_active' => true,
                'email_verified_at' => now(),
            ], $attributes),
        );
    }
}
