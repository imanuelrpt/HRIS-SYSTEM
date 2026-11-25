<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\User;
use App\Models\WorkSetting;
use App\Services\AttendanceService;
use App\Services\WorkCalendarService;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AttendanceController extends Controller
{
    public function index(Request $request, WorkCalendarService $workCalendar): View
    {
        Gate::authorize('viewAny', Attendance::class);

        /** @var User $user */
        $user = $request->user();
        $canViewAll = Gate::allows('viewAll', Attendance::class);

        $month = $request->integer('month');
        $year = $request->integer('year');

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $statusFilter = $request->string('status')->toString();
        $statusOptions = collect(AttendanceStatus::cases())
            ->mapWithKeys(fn (AttendanceStatus $status) => [$status->value => $status->label()]);

        $selectedUserId = $canViewAll ? $request->integer('user_id') : $user->id;

        $attendances = Attendance::query()
            ->with('user:id,name')
            ->forMonth($month, $year)
            ->when($canViewAll && $selectedUserId > 0, fn ($query) => $query->where('user_id', $selectedUserId))
            ->when(! $canViewAll, fn ($query) => $query->where('user_id', $user->id))
            ->when($statusOptions->has($statusFilter), fn ($query) => $query->where('status', $statusFilter))
            ->latest('date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $todayAttendance = $user->attendances()
            ->whereDate('date', today()->toDateString())
            ->first();

        $users = $canViewAll
            ? User::query()->active()->orderBy('name')->pluck('name', 'id')
            : collect();

        return view('attendances.index', [
            'attendances' => $attendances,
            'todayAttendance' => $todayAttendance,
            'workSetting' => WorkSetting::current(),
            'isWorkingDay' => $workCalendar->isWorkingDay(today()),
            'canViewAll' => $canViewAll,
            'users' => $users,
            'selectedUserId' => $selectedUserId,
            'month' => $month,
            'year' => $year,
            'statusOptions' => $statusOptions,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function checkIn(Request $request, AttendanceService $attendanceService): RedirectResponse
    {
        Gate::authorize('clockIn', Attendance::class);

        /** @var User $user */
        $user = $request->user();

        try {
            $attendance = $attendanceService->clockIn($user);
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Clock-in berhasil pukul '.format_waktu($attendance->clock_in).'.');
    }

    public function checkOut(Request $request, AttendanceService $attendanceService): RedirectResponse
    {
        Gate::authorize('clockOut', Attendance::class);

        /** @var User $user */
        $user = $request->user();

        try {
            $attendance = $attendanceService->clockOut($user);
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Clock-out berhasil pukul '.format_waktu($attendance->clock_out).'.');
    }
}
