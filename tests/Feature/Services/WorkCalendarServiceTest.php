<?php

namespace Tests\Feature\Services;

use App\Models\Holiday;
use App\Models\WorkSetting;
use App\Services\WorkCalendarService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkCalendarServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_counts_working_days_and_excludes_configured_holidays(): void
    {
        WorkSetting::factory()->create([
            'working_days' => [1, 2, 3, 4, 5],
        ]);
        Holiday::factory()->create([
            'date' => '2026-11-04',
            'description' => 'Libur uji',
        ]);

        $service = $this->app->make(WorkCalendarService::class);

        $workingDays = $service->countWorkingDays(
            Carbon::parse('2026-11-02'),
            Carbon::parse('2026-11-08'),
        );

        $this->assertSame(4, $workingDays);
    }
}
