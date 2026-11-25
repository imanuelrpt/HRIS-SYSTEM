<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'activeEmployees' => User::query()->active()->count(),
            'departments' => Department::query()->count(),
            'positions' => Position::query()->count(),
            'holidays' => Holiday::query()->count(),
            'todayAttendance' => Attendance::query()->whereDate('date', today())->count(),
            'pendingLeaves' => $user->isAdmin() || $user->isManager()
                ? LeaveRequest::query()->pending()->count()
                : $user->leaveRequests()->pending()->count(),
        ];

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }
}
