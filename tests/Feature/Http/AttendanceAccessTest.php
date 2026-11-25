<?php

namespace Tests\Feature\Http;

use App\Models\User;
use App\Models\WorkSetting;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_second_clock_in_on_the_same_day_is_rejected(): void
    {
        $this->travelTo(Carbon::parse('2026-11-25 08:05:00'));
        WorkSetting::factory()->create([
            'check_in' => '08:00',
            'check_out' => '17:00',
            'late_tolerance_minutes' => 10,
            'working_days' => [1, 2, 3, 4, 5],
        ]);

        $employee = User::factory()->create();

        $this->actingAs($employee)
            ->from('/attendances')
            ->post('/attendances/check-in')
            ->assertRedirect('/attendances')
            ->assertSessionHas('success');

        $response = $this->actingAs($employee)
            ->from('/attendances')
            ->post('/attendances/check-in');

        $response
            ->assertRedirect('/attendances')
            ->assertSessionHas('error', 'Anda sudah melakukan clock-in hari ini.');

        $this->assertDatabaseCount('attendances', 1);
    }
}
