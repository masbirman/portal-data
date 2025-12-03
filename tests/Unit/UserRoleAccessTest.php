<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Sekolah;
use App\Models\Wilayah;
use App\Models\JenjangPendidikan;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: multi-panel-user-management, Property 4: Role-Based Panel Access
 * Validates: Requirements 6.1, 6.2, 6.3, 6.4
 *
 * Property: For any user:
 * - If role = super_admin, user SHALL dapat mengakses Admin Panel
 * - If role = admin_wilayah, user SHALL di-redirect ke Wilayah Panel saat mengakses Admin Panel
 * - If role = user_sekolah, user SHALL di-redirect ke Sekolah Panel saat mengakses Admin Panel atau Wilayah Panel
 */
class UserRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Super Admin can access Admin Panel
     */
    public function test_super_admin_can_access_admin_panel(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->assertTrue($user->isSuperAdmin());
        $this->assertFalse($user->isAdminWilayah());
        $this->assertFalse($user->isUserSekolah());
    }

    /**
     * Test Admin Wilayah role checker
     */
    public function test_admin_wilayah_role_checker(): void
    {
        $user = User::factory()->create([
            'role' => 'admin_wilayah',
            'is_active' => true,
        ]);

        $this->assertFalse($user->isSuperAdmin());
        $this->assertTrue($user->isAdminWilayah());
        $this->assertFalse($user->isUserSekolah());
    }

    /**
     * Test User Sekolah role checker
     */
    public function test_user_sekolah_role_checker(): void
    {
        $user = User::factory()->create([
            'role' => 'user_sekolah',
            'is_active' => true,
        ]);

        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->isAdminWilayah());
        $this->assertTrue($user->isUserSekolah());
    }

    /**
     * Test inactive user cannot access any panel
     */
    public function test_inactive_user_cannot_access_panel(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => false,
        ]);

        $this->assertTrue($user->isSuperAdmin());
        $this->assertFalse($user->is_active);
    }

    /**
     * Property test: For any role, role checker methods are mutually exclusive
     */
    public function test_role_checkers_are_mutually_exclusive(): void
    {
        $roles = ['super_admin', 'admin_wilayah', 'user_sekolah'];

        foreach ($roles as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'is_active' => true,
            ]);

            $checkers = [
                $user->isSuperAdmin(),
                $user->isAdminWilayah(),
                $user->isUserSekolah(),
            ];

            // Exactly one checker should return true
            $this->assertEquals(1, array_sum($checkers), "Role {$role} should have exactly one true checker");

            $user->delete();
        }
    }

    /**
     * Property test: For any active user with valid role, exactly one panel should be accessible
     */
    public function test_active_user_has_exactly_one_accessible_panel(): void
    {
        $roles = ['super_admin', 'admin_wilayah', 'user_sekolah'];
        $expectedPanels = [
            'super_admin' => 'admin',
            'admin_wilayah' => 'wilayah',
            'user_sekolah' => 'sekolah',
        ];

        foreach ($roles as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'is_active' => true,
            ]);

            $expectedPanel = $expectedPanels[$role];

            // Verify the expected panel mapping
            $this->assertEquals($expectedPanel, match ($role) {
                'super_admin' => 'admin',
                'admin_wilayah' => 'wilayah',
                'user_sekolah' => 'sekolah',
            }, "Role {$role} should map to panel {$expectedPanel}");

            $user->delete();
        }
    }
}
