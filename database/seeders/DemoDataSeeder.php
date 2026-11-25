<?php

namespace Database\Seeders;

use App\Enums\ApprovalAction;
use App\Enums\AttendanceStatus;
use App\Enums\LeaveStatus;
use App\Enums\Role;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\LeaveApproval;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\WorkCalendarService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(WorkCalendarService $workCalendar): void
    {
        $users = User::query()->where('role', '!=', Role::Admin->value)->get();
        $admin = User::query()->where('role', Role::Admin->value)->firstOrFail();
        $manager = User::query()->where('email', 'manager@hris.test')->firstOrFail();
        $employee = User::query()->where('email', 'karyawan@hris.test')->firstOrFail();

        $leaveTypes = LeaveType::query()->pluck('id', 'name');
        $onLeave = [];

        $leaveData = [
            [
                'user' => $employee,
                'type' => 'Cuti Tahunan',
                'start' => now()->subMonth()->startOfMonth()->addDays(7),
                'days' => 2,
                'reason' => 'Liburan keluarga ke Yogyakarta.',
                'status' => LeaveStatus::Approved,
                'approvals' => [$manager, $admin],
                'deduct' => true,
            ],
            [
                'user' => $employee,
                'type' => 'Izin',
                'start' => now()->subDays(10),
                'days' => 1,
                'reason' => 'Mengurus dokumen administrasi.',
                'status' => LeaveStatus::Rejected,
                'approvals' => [],
                'deduct' => false,
            ],
            [
                'user' => $employee,
                'type' => 'Cuti Tahunan',
                'start' => now()->addDays(3),
                'days' => 1,
                'reason' => 'Keperluan keluarga.',
                'status' => LeaveStatus::PendingManager,
                'approvals' => [],
                'deduct' => false,
            ],
            [
                'user' => User::query()->where('email', 'maya.sari@hris.test')->firstOrFail(),
                'type' => 'Cuti Tahunan',
                'start' => now()->addDays(5),
                'days' => 3,
                'reason' => 'Cuti tahunan akhir kuartal.',
                'status' => LeaveStatus::PendingHr,
                'approvals' => [$manager],
                'deduct' => false,
            ],
            [
                'user' => User::query()->where('email', 'rizky.ramadhan@hris.test')->firstOrFail(),
                'type' => 'Cuti Sakit',
                'start' => now()->subDays(15),
                'days' => 1,
                'reason' => 'Demam dan perlu istirahat.',
                'status' => LeaveStatus::Approved,
                'approvals' => [$manager, $admin],
                'deduct' => false,
                'attachment' => 'attachments/contoh-surat-dokter.pdf',
            ],
            [
                'user' => User::query()->where('email', 'nurul.aini@hris.test')->firstOrFail(),
                'type' => 'Cuti Tahunan',
                'start' => now()->subDays(20),
                'days' => 1,
                'reason' => 'Acara keluarga.',
                'status' => LeaveStatus::Cancelled,
                'approvals' => [],
                'deduct' => false,
            ],
        ];

        foreach ($leaveData as $data) {
            $start = $data['start']->copy()->startOfDay();
            $end = $start->copy()->addDays(max(0, ($data['days'] ?? 1) - 1));
            $totalDays = max(1, $workCalendar->countWorkingDays($start, $end));

            $leaveRequest = LeaveRequest::query()->firstOrCreate(
                [
                    'user_id' => $data['user']->id,
                    'leave_type_id' => $leaveTypes[$data['type']],
                    'start_date' => $start->toDateString(),
                ],
                [
                    'end_date' => $end->toDateString(),
                    'total_days' => $totalDays,
                    'reason' => $data['reason'],
                    'attachment_path' => $data['attachment'] ?? null,
                    'status' => $data['status'],
                ],
            );

            foreach ($data['approvals'] ?? [] as $level => $approver) {
                $level++;
                LeaveApproval::query()->firstOrCreate(
                    [
                        'leave_request_id' => $leaveRequest->id,
                        'level' => $level,
                    ],
                    [
                        'approver_id' => $approver->id,
                        'action' => ApprovalAction::Approve,
                        'note' => 'Disetujui untuk keperluan yang diajukan.',
                        'acted_at' => $start->copy()->subDays($level),
                    ],
                );
            }

            if ($data['status'] === LeaveStatus::Rejected) {
                LeaveApproval::query()->firstOrCreate(
                    [
                        'leave_request_id' => $leaveRequest->id,
                        'level' => 1,
                    ],
                    [
                        'approver_id' => $manager->id,
                        'action' => ApprovalAction::Reject,
                        'note' => 'Alasan belum lengkap, silakan ajukan kembali.',
                        'acted_at' => $start->copy()->subDay(),
                    ],
                );
            }

            if (($data['deduct'] ?? false) && $leaveRequest->wasRecentlyCreated) {
                LeaveBalance::query()
                    ->where('user_id', $data['user']->id)
                    ->where('leave_type_id', $leaveTypes[$data['type']])
                    ->where('year', $start->year)
                    ->increment('used', $totalDays);
            }

            $cursor = $start->copy();
            while ($cursor->lessThanOrEqualTo($end)) {
                $onLeave[$data['user']->id][$cursor->toDateString()] = true;
                $cursor->addDay();
            }
        }

        $cursor = now()->subMonths(2)->startOfMonth();
        $today = now()->toDateString();

        while ($cursor->lessThanOrEqualTo($today)) {
            /** @var Carbon $date */
            $date = $cursor->copy();

            if ($workCalendar->isWorkingDay($date)) {
                foreach ($users as $user) {
                    if (isset($onLeave[$user->id][$date->toDateString()])) {
                        continue;
                    }

                    // Akun demo sengaja tidak diberi absen hari ini agar tombol clock in dapat dicoba.
                    if ($date->isToday() && in_array($user->email, ['manager@hris.test', 'karyawan@hris.test'], true)) {
                        continue;
                    }

                    $random = fake()->numberBetween(1, 100);

                    if ($random <= 5) {
                        continue;
                    }

                    $late = $random <= 18;
                    $earlyLeave = ! $late && $random >= 95;
                    $clockIn = $late
                        ? sprintf('08:%02d', fake()->numberBetween(15, 45))
                        : sprintf('07:%02d', fake()->numberBetween(40, 59));

                    Attendance::query()->firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'date' => $date->toDateString(),
                        ],
                        [
                            'clock_in' => $clockIn,
                            'clock_out' => $earlyLeave ? '16:10' : '17:'.sprintf('%02d', fake()->numberBetween(0, 25)),
                            'ip_address' => fake()->ipv4(),
                            'status' => match (true) {
                                $late => AttendanceStatus::Terlambat,
                                $earlyLeave => AttendanceStatus::PulangCepat,
                                default => AttendanceStatus::Hadir,
                            },
                        ],
                    );
                }
            }

            $cursor->addDay();
        }

        foreach ([$admin, $manager, $employee] as $user) {
            ActivityLog::query()->create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => sprintf('%s melakukan login.', $user->name),
                'ip' => fake()->ipv4(),
                'created_at' => now()->subHours(fake()->numberBetween(1, 8)),
            ]);
        }

        $this->command?->info('Seeder data demo selesai: absensi 2 bulan terakhir dan pengajuan cuti.');
    }
}
