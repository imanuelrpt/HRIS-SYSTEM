<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\User;
use App\Models\WorkSetting;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function __construct(
        private readonly WorkCalendarService $workCalendar,
        private readonly ActivityLogService $activityLog,
        private readonly Request $request,
    ) {}

    /**
     * Catat clock-in untuk hari ini. Satu karyawan hanya bisa clock-in sekali per hari.
     */
    public function clockIn(User $user): Attendance
    {
        $today = today();

        if (! $this->workCalendar->isWorkingDay($today)) {
            throw new DomainException('Hari ini bukan hari kerja, sehingga absensi tidak dapat dilakukan.');
        }

        if ($this->hasAttendanceToday($user)) {
            throw new DomainException('Anda sudah melakukan clock-in hari ini.');
        }

        $workSetting = WorkSetting::current();

        return DB::transaction(function () use ($user, $today, $workSetting): Attendance {
            if ($this->hasAttendanceToday($user)) {
                throw new DomainException('Anda sudah melakukan clock-in hari ini.');
            }

            $attendance = $user->attendances()->create([
                'date' => $today->toDateString(),
                'clock_in' => now()->format('H:i:s'),
                'ip_address' => $this->request->ip(),
                'status' => $this->clockInStatus($workSetting),
            ]);

            $this->activityLog->log(
                'attendance.clock_in',
                "{$user->name} melakukan clock-in pukul {$attendance->clock_in}.",
                $attendance,
                $user,
            );

            return $attendance;
        });
    }

    /**
     * Catat clock-out untuk absensi hari ini.
     */
    public function clockOut(User $user): Attendance
    {
        $attendance = $user->attendances()
            ->whereDate('date', today()->toDateString())
            ->first();

        if ($attendance === null) {
            throw new DomainException('Anda belum melakukan clock-in hari ini.');
        }

        if ($attendance->clock_out !== null) {
            throw new DomainException('Anda sudah melakukan clock-out hari ini.');
        }

        if (now()->lessThan(Carbon::parse($attendance->clock_in))) {
            throw new DomainException('Waktu clock-out tidak boleh sebelum waktu clock-in.');
        }

        $workSetting = WorkSetting::current();

        return DB::transaction(function () use ($attendance, $workSetting, $user): Attendance {
            $attendance->update([
                'clock_out' => now()->format('H:i:s'),
                'status' => $this->clockOutStatus($attendance, $workSetting),
            ]);

            $this->activityLog->log(
                'attendance.clock_out',
                "{$user->name} melakukan clock-out pukul {$attendance->clock_out}.",
                $attendance,
                $user,
            );

            return $attendance;
        });
    }

    public function hasAttendanceToday(User $user): bool
    {
        return $user->attendances()
            ->whereDate('date', today()->toDateString())
            ->exists();
    }

    private function clockInStatus(WorkSetting $workSetting): AttendanceStatus
    {
        $lateThreshold = Carbon::parse($workSetting->check_in)
            ->addMinutes($workSetting->late_tolerance_minutes);

        return now()->greaterThan($lateThreshold)
            ? AttendanceStatus::Terlambat
            : AttendanceStatus::Hadir;
    }

    private function clockOutStatus(Attendance $attendance, WorkSetting $workSetting): AttendanceStatus
    {
        if ($attendance->status === AttendanceStatus::Terlambat) {
            return AttendanceStatus::Terlambat;
        }

        return now()->lessThan(Carbon::parse($workSetting->check_out))
            ? AttendanceStatus::PulangCepat
            : AttendanceStatus::Hadir;
    }
}
