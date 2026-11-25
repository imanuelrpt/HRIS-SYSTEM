<?php

namespace Tests\Feature\Services;

use App\Enums\LeaveStatus;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\WorkSetting;
use App\Services\LeaveApprovalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveApprovalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_level_approval_deducts_balance_only_after_hr_approval(): void
    {
        $this->travelTo(Carbon::parse('2026-11-25 08:00:00'));
        WorkSetting::factory()->create(['working_days' => [1, 2, 3, 4, 5]]);

        $manager = User::factory()->manager()->create();
        $admin = User::factory()->admin()->create();
        $employee = User::factory()->create(['manager_id' => $manager->id]);
        $leaveType = LeaveType::factory()->create([
            'deducts_balance' => true,
            'requires_attachment' => false,
        ]);
        $balance = LeaveBalance::factory()->create([
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'year' => 2026,
            'quota' => 12,
            'used' => 0,
        ]);
        $leaveRequest = LeaveRequest::factory()->create([
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-02',
            'total_days' => 2,
            'status' => LeaveStatus::PendingManager,
        ]);

        $service = $this->app->make(LeaveApprovalService::class);

        $service->approve($leaveRequest, $manager, 'Disetujui atasan.');

        $this->assertSame(LeaveStatus::PendingHr, $leaveRequest->fresh()->status);
        $this->assertSame(0, $balance->fresh()->used);

        $service->approve($leaveRequest->fresh(), $admin, 'Disetujui HR.');

        $this->assertSame(LeaveStatus::Approved, $leaveRequest->fresh()->status);
        $this->assertSame(2, $balance->fresh()->used);
        $this->assertDatabaseCount('leave_approvals', 2);
        $this->assertDatabaseHas('leave_approvals', [
            'leave_request_id' => $leaveRequest->id,
            'level' => 1,
            'approver_id' => $manager->id,
        ]);
        $this->assertDatabaseHas('leave_approvals', [
            'leave_request_id' => $leaveRequest->id,
            'level' => 2,
            'approver_id' => $admin->id,
        ]);
    }
}
