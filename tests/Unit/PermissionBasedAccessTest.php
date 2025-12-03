<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: multi-panel-user-management, Property 5: Permission-Based Feature Access
 * Validates: Requirements 3.5, 5.2
 *
 * Property: For any User Sekolah and for any fitur yang memerlukan permission,
 * user SHALL hanya dapat mengakses fitur jika memiliki permission yang sesuai
 * dalam tabel user_permission.
 */
class PermissionBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed permissions
        $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);
    }

    /**
     * Test User Sekolah without permission cannot access feature
     */
    public function test_user_sekolah_without_permission_cannot_access(): void
    {
        $userSekolah = User::factory()->create([
            'role' => 'user_sekolah',
            'is_active' => true,
        ]);

        // User has no permissions assigned
        $this->assertFalse($userSekolah->hasPermission(Permission::VIEW_SEKOLAH_DATA));
        $this->assertFalse($userSekolah->hasPermission(Permission::VIEW_ASESMEN_DATA));
        $this->assertFalse($userSekolah->hasPermission(Permission::DOWNLOAD_REPORT));
        $this->assertFalse($userSekolah->hasPermission(Permission::VIEW_STATISTICS));
    }

    /**
     * Test User Sekolah with permission can access feature
     */
    public function test_user_sekolah_with_permission_can_access(): void
    {
        $userSekolah = User::factory()->create([
            'role' => 'user_sekolah',
            'is_active' => true,
        ]);

        // Assign permission
        $permission = Permission::where('name', Permission::VIEW_SEKOLAH_DATA)->first();
        $userSekolah->permissions()->attach($permission->id);

        // User should have the assigned permission
        $this->assertTrue($userSekolah->hasPermission(Permission::VIEW_SEKOLAH_DATA));

        // But not other permissions
        $this->assertFalse($userSekolah->hasPermission(Permission::VIEW_ASESMEN_DATA));
    }

    /**
     * Test Super Admin has all permissions automatically
     */
    public function test_super_admin_has_all_permissions(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Super Admin should have all permissions without explicit assignment
        $this->assertTrue($superAdmin->hasPermission(Permission::VIEW_SEKOLAH_DATA));
        $this->assertTrue($superAdmin->hasPermission(Permission::VIEW_ASESMEN_DATA));
        $this->assertTrue($superAdmin->hasPermission(Permission::DOWNLOAD_REPORT));
        $this->assertTrue($superAdmin->hasPermission(Permission::VIEW_STATISTICS));
        $this->assertTrue($superAdmin->hasPermission('any_random_permission'));
    }

    /**
     * Test Admin Wilayah has all permissions within scope
     */
    public function test_admin_wilayah_has_all_permissions(): void
    {
        $adminWilayah = User::factory()->create([
            'role' => 'admin_wilayah',
            'is_active' => true,
        ]);

        // Admin Wilayah should have all permissions within their scope
        $this->assertTrue($adminWilayah->hasPermission(Permission::VIEW_SEKOLAH_DATA));
        $this->assertTrue($adminWilayah->hasPermission(Permission::VIEW_ASESMEN_DATA));
    }

    /**
     * Property test: Permission assignment is specific to user
     */
    public function test_permission_assignment_is_user_specific(): void
    {
        $user1 = User::factory()->create([
            'role' => 'user_sekolah',
            'is_active' => true,
        ]);

        $user2 = User::factory()->create([
            'role' => 'user_sekolah',
            'is_active' => true,
        ]);

        // Assign permission only to user1
        $permission = Permission::where('name', Permission::VIEW_SEKOLAH_DATA)->first();
        $user1->permissions()->attach($permission->id);

        // User1 has permission, User2 does not
        $this->assertTrue($user1->hasPermission(Permission::VIEW_SEKOLAH_DATA));
        $this->assertFalse($user2->hasPermission(Permission::VIEW_SEKOLAH_DATA));
    }

    /**
     * Test multiple permissions can be assigned
     */
    public function test_multiple_permissions_can_be_assigned(): void
    {
        $userSekolah = User::factory()->create([
            'role' => 'user_sekolah',
            'is_active' => true,
        ]);

        // Assign multiple permissions
        $permissions = Permission::whereIn('name', [
            Permission::VIEW_SEKOLAH_DATA,
            Permission::VIEW_STATISTICS,
        ])->get();

        $userSekolah->permissions()->attach($permissions->pluck('id'));

        // User should have both permissions
        $this->assertTrue($userSekolah->hasPermission(Permission::VIEW_SEKOLAH_DATA));
        $this->assertTrue($userSekolah->hasPermission(Permission::VIEW_STATISTICS));

        // But not others
        $this->assertFalse($userSekolah->hasPermission(Permission::VIEW_ASESMEN_DATA));
        $this->assertFalse($userSekolah->hasPermission(Permission::DOWNLOAD_REPORT));
    }
}
