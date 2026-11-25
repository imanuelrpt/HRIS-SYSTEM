<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveStatus;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MonthlyReportService
{
    private const MONTH_NAMES = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    public function __construct(private readonly WorkCalendarService $workCalendar) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters, bool $canViewAll, int $currentUserId): array
    {
        $month = (int) ($filters['month'] ?? now()->month);
        $year = (int) ($filters['year'] ?? now()->year);
        $departmentId = $canViewAll ? (int) ($filters['department_id'] ?? 0) : 0;
        $selectedUserId = $canViewAll ? (int) ($filters['user_id'] ?? 0) : $currentUserId;

        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $periodEnd = $monthEnd->lessThan(now()) ? $monthEnd : now()->copy()->endOfDay();

        $employees = User::query()
            ->select(['id', 'nik', 'name', 'department_id'])
            ->with('department:id,name')
            ->active()
            ->when($departmentId > 0, fn ($query) => $query->where('department_id', $departmentId))
            ->when($selectedUserId > 0, fn ($query) => $query->whereKey($selectedUserId))
            ->orderBy('name')
            ->get();

        $employeeIds = $employees->modelKeys();
        $workingDays = $this->workCalendar->countWorkingDays($monthStart, $periodEnd);
        $attendanceCounts = $this->attendanceCounts($month, $year, $employeeIds);
        $leaveDaysByUser = $this->approvedLeaveDaysByUser($employeeIds, $monthStart, $periodEnd);

        $rows = $employees->map(function (User $employee) use (
            $attendanceCounts,
            $leaveDaysByUser,
            $workingDays,
        ): array {
            $counts = $attendanceCounts->get($employee->id);
            $hadir = (int) ($counts?->hadir ?? 0);
            $terlambat = (int) ($counts?->terlambat ?? 0);
            $pulangCepat = (int) ($counts?->pulang_cepat ?? 0);
            $alpha = (int) ($counts?->alpha ?? 0);
            $attendedDays = $hadir + $terlambat + $pulangCepat;
            $leaveDays = (int) $leaveDaysByUser->get($employee->id, 0);

            return [
                'user_id' => $employee->id,
                'nik' => $employee->nik ?: '-',
                'name' => $employee->name,
                'department' => $employee->department?->name ?? '-',
                'working_days' => $workingDays,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'pulang_cepat' => $pulangCepat,
                'alpha' => $alpha,
                'total_records' => (int) ($counts?->total_records ?? 0),
                'attended_days' => $attendedDays,
                'leave_days' => $leaveDays,
                'incomplete' => (int) ($counts?->incomplete ?? 0),
                'attendance_rate' => $workingDays > 0
                    ? min(100, round($attendedDays / $workingDays * 100, 1))
                    : 0,
            ];
        });

        $summary = $this->summarize($rows, $workingDays);

        return [
            'rows' => $rows,
            'summary' => $summary,
            'chartData' => $this->chartData($summary),
            'period' => [
                'month' => $month,
                'year' => $year,
                'label' => $monthStart->translatedFormat('F Y'),
                'start' => $monthStart,
                'end' => $periodEnd,
                'month_end' => $monthEnd,
            ],
            'filters' => [
                'month' => $month,
                'year' => $year,
                'department_id' => $departmentId,
                'user_id' => $selectedUserId,
            ],
            'monthNames' => self::MONTH_NAMES,
            'departments' => Department::query()->orderBy('name')->pluck('name', 'id'),
            'filterUsers' => User::query()->active()->orderBy('name')->pluck('name', 'id'),
            'canViewAll' => $canViewAll,
        ];
    }

    /**
     * @param  array<int, int>  $employeeIds
     */
    private function attendanceCounts(int $month, int $year, array $employeeIds): Collection
    {
        if ($employeeIds === []) {
            return collect();
        }

        return Attendance::query()
            ->forMonth($month, $year)
            ->select('user_id')
            ->selectRaw('COUNT(*) as total_records')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as hadir', [AttendanceStatus::Hadir->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as terlambat', [AttendanceStatus::Terlambat->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pulang_cepat', [AttendanceStatus::PulangCepat->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as alpha', [AttendanceStatus::Alpha->value])
            ->selectRaw('SUM(CASE WHEN clock_out IS NULL THEN 1 ELSE 0 END) as incomplete')
            ->whereIn('user_id', $employeeIds)
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');
    }

    /**
     * @param  array<int, int>  $employeeIds
     * @return Collection<int, int>
     */
    private function approvedLeaveDaysByUser(array $employeeIds, Carbon $start, Carbon $end): Collection
    {
        if ($employeeIds === []) {
            return collect();
        }

        return LeaveRequest::query()
            ->select(['user_id', 'start_date', 'end_date'])
            ->where('status', LeaveStatus::Approved->value)
            ->where('start_date', '<=', $end->toDateString())
            ->where('end_date', '>=', $start->toDateString())
            ->whereIn('user_id', $employeeIds)
            ->get()
            ->groupBy('user_id')
            ->map(function (Collection $leaveRequests) use ($start, $end): int {
                return (int) $leaveRequests->sum(function (LeaveRequest $leaveRequest) use ($start, $end): int {
                    $overlapStart = $leaveRequest->start_date->greaterThan($start)
                        ? $leaveRequest->start_date
                        : $start;
                    $overlapEnd = $leaveRequest->end_date->lessThan($end)
                        ? $leaveRequest->end_date
                        : $end;

                    return $this->workCalendar->countWorkingDays($overlapStart, $overlapEnd);
                });
            });
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return array<string, float|int>
     */
    private function summarize(Collection $rows, int $workingDays): array
    {
        $employees = $rows->count();
        $attendedDays = (int) $rows->sum('attended_days');
        $availableDays = $employees * $workingDays;

        return [
            'employees' => $employees,
            'working_days' => $workingDays,
            'records' => (int) $rows->sum('total_records'),
            'hadir' => (int) $rows->sum('hadir'),
            'terlambat' => (int) $rows->sum('terlambat'),
            'pulang_cepat' => (int) $rows->sum('pulang_cepat'),
            'alpha' => (int) $rows->sum('alpha'),
            'attended_days' => $attendedDays,
            'leave_days' => (int) $rows->sum('leave_days'),
            'incomplete' => (int) $rows->sum('incomplete'),
            'attendance_rate' => $availableDays > 0
                ? round($attendedDays / $availableDays * 100, 1)
                : 0,
        ];
    }

    /**
     * @param  array<string, float|int>  $summary
     * @return array<string, mixed>
     */
    private function chartData(array $summary): array
    {
        return [
            'labels' => ['Hadir', 'Terlambat', 'Pulang Cepat', 'Alpha'],
            'datasets' => [[
                'label' => 'Jumlah Absensi',
                'data' => [
                    $summary['hadir'],
                    $summary['terlambat'],
                    $summary['pulang_cepat'],
                    $summary['alpha'],
                ],
                'backgroundColor' => ['#198754', '#ffc107', '#0dcaf0', '#dc3545'],
                'borderColor' => ['#146c43', '#cc9a06', '#087ea4', '#b02a37'],
                'borderWidth' => 1,
                'borderRadius' => 4,
            ]],
        ];
    }
}
