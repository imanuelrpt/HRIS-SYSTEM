<?php

namespace Tests\Feature\Services;

use App\Enums\LeaveStatus;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\WorkSetting;
use App\Services\LeaveRequestService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_leave_when_remaining_balance_is_insufficient(): void
    {
        $this->travelTo(Carbon::parse('2026-11-25 08:00:00'));
        WorkSetting::factory()->create(['working_days' => [1, 2, 3, 4, 5]]);

        $employee = User::factory()->create();
        $leaveType = LeaveType::factory()->create([
            'deducts_balance' => true,
            'requires_attachment' => false,
        ]);
        LeaveBalance::factory()->create([
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'year' => 2026,
            'quota' => 1,
            'used' => 0,
        ]);

        $service = $this->app->make(LeaveRequestService::class);

        try {
            $service->create($employee, [
                'leave_type_id' => $leaveType->id,
                'start_date' => '2026-12-01',
                'end_date' => '2026-12-02',
                'reason' => 'Liburan dua hari kerja.',
            ]);
            $this->fail('Pengajuan dengan kuota kurang seharusnya ditolak.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('Sisa kuota', $exception->getMessage());
        }

        $this->assertDatabaseCount('leave_requests', 0);
    }

    public function test_rejects_leave_when_dates_overlap_an_active_request(): void
    {
        $this->travelTo(Carbon::parse('2026-11-25 08:00:00'));
        WorkSetting::factory()->create(['working_days' => [1, 2, 3, 4, 5]]);

        $employee = User::factory()->create();
        $leaveType = LeaveType::factory()->create([
            'deducts_balance' => false,
            'requires_attachment' => false,
        ]);
        LeaveRequest::factory()->create([
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-02',
            'total_days' => 2,
            'status' => LeaveStatus::Approved,
        ]);

        $service = $this->app->make(LeaveRequestService::class);

        try {
            $service->create($employee, [
                'leave_type_id' => $leaveType->id,
                'start_date' => '2026-12-02',
                'end_date' => '2026-12-03',
                'reason' => 'Pengajuan yang bertabrakan.',
            ]);
            $this->fail('Pengajuan dengan tanggal bertabrakan seharusnya ditolak.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('Tanggal cuti bertabrakan', $exception->getMessage());
        }

        $this->assertDatabaseCount('leave_requests', 1);
    }
}
