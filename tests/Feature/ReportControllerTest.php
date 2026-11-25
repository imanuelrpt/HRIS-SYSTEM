<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createWorkSetting(): void
    {
        WorkSetting::factory()->create(['working_days' => [1, 2, 3, 4, 5]]);
    }

    public function test_admin_can_open_monthly_report_with_valid_filters(): void
    {
        $this->createWorkSetting();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/reports?month=11&year=2026');

        $response
            ->assertOk()
            ->assertSee('Rekap Bulanan')
            ->assertSee('Distribusi Status Absensi');
    }

    public function test_employee_is_forbidden_from_monthly_report(): void
    {
        $this->createWorkSetting();
        $employee = User::factory()->create();

        $response = $this->actingAs($employee)->get('/reports?month=11&year=2026');

        $response->assertForbidden();
    }

    public function test_admin_can_download_excel_report(): void
    {
        $this->createWorkSetting();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/reports/export/excel?month=11&year=2026');

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_download_pdf_report(): void
    {
        $this->createWorkSetting();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/reports/export/pdf?month=11&year=2026');

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_invalid_month_shows_validation_message(): void
    {
        $this->createWorkSetting();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->from('/reports')
            ->get('/reports?month=13&year=2026');

        $response
            ->assertRedirect('/reports')
            ->assertSessionHasErrors(['month' => 'Bulan harus antara 1 sampai 12.']);
    }
}
