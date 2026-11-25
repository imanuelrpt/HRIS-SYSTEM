<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkSetting;
use App\Policies\ActivityLogPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\HolidayPolicy;
use App\Policies\LeaveRequestPolicy;
use App\Policies\LeaveTypePolicy;
use App\Policies\PositionPolicy;
use App\Policies\UserPolicy;
use App\Policies\WorkSettingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ActivityLog::class => ActivityLogPolicy::class,
        Attendance::class => AttendancePolicy::class,
        Department::class => DepartmentPolicy::class,
        Holiday::class => HolidayPolicy::class,
        LeaveRequest::class => LeaveRequestPolicy::class,
        LeaveType::class => LeaveTypePolicy::class,
        Position::class => PositionPolicy::class,
        User::class => UserPolicy::class,
        WorkSetting::class => WorkSettingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
