<?php

namespace Tests\Feature\Http;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{Role}>
     */
    public static function nonAdminRoles(): array
    {
        return [
            'employee' => [Role::Employee],
            'manager' => [Role::Manager],
        ];
    }

    #[DataProvider('nonAdminRoles')]
    public function test_non_admin_role_is_forbidden_from_admin_pages(Role $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $response = $this->actingAs($user)->get('/users');

        $response->assertForbidden();
    }

    public function test_admin_can_open_admin_pages(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/users');

        $response->assertOk();
    }
}
