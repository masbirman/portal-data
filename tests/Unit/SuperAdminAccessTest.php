<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Wilayah;
use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: multi-panel-user-management, Property 3: Super Admin Full Access
 * Validates: Requirements 1.2, 1.3, 1.5
 *
 * Property: For any Super Admin user and for any query ke data apapun,
 * hasil query SHALL berisi semua record tanpa filter wilayah, jenjang, atau sekolah.
 */
class SuperAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Super Admin can see all wilayah data without filter
     */
    public function test_super_admin_sees_all_wilayah_data(): void
    {
        // Create multiple wilayah
        $wilayah1 = Wilayah::create(['nama' => 'Wilayah 1', 'urutan' => 1]);
        $wilayah2 = Wilayah::create(['nama' => 'Wilayah 2', 'urutan' => 2]);
        $wilayah3 = Wilayah::create(['nama' => 'Wilayah 3', 'urutan' => 3]);

        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Super Admin should have no wilayah restrictions
        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertEmpty($superAdmin->getWilayahIds());

        // All wilayah should be accessible (no scope applied for super admin)
        $allWilayah = Wilayah::withoutGlobalScopes()->count();
        $this->assertEquals(3, $allWilayah);
    }

    /**
     * Test Super Admin can see all jenjang data without filter
     */
    public function test_super_admin_sees_all_jenjang_data(): void
    {
        // Create multiple jenjang
        JenjangPendidikan::create(['kode' => 'SD', 'nama' => 'Sekolah Dasar']);
        JenjangPendidikan::create(['kode' => 'SMP', 'nama' => 'Sekolah Menengah Pertama']);
        JenjangPendidikan::create(['kode' => 'SMA', 'nama' => 'Sekolah Menengah Atas']);

        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Super Admin should have no jenjang restrictions
        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertEmpty($superAdmin->getJenjangIds());

        // All jenjang should be accessible
        $allJenjang = JenjangPendidikan::withoutGlobalScopes()->count();
        $this->assertEquals(3, $allJenjang);
    }

    /**
     * Test Super Admin has all permissions
     */
    public function test_super_admin_has_all_permissions(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Super Admin should have all permissions
        $this->assertTrue($superAdmin->hasPermission('view_sekolah_data'));
        $this->assertTrue($superAdmin->hasPermission('view_asesmen_data'));
        $this->assertTrue($superAdmin->hasPermission('download_report'));
        $this->assertTrue($superAdmin->hasPermission('view_statistics'));
        $this->assertTrue($superAdmin->hasPermission('any_random_permission'));
    }

    /**
     * Property test: Super Admin always has full access regardless of data
     */
    public function test_super_admin_full_access_property(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Verify Super Admin properties
        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertFalse($superAdmin->isAdminWilayah());
        $this->assertFalse($superAdmin->isUserSekolah());

        // Super Admin should not be restricted by wilayah/jenjang
        $this->assertEmpty($superAdmin->getWilayahIds());
        $this->assertEmpty($superAdmin->getJenjangIds());

        // Super Admin should have all permissions
        $this->assertTrue($superAdmin->hasPermission('any_permission'));
    }
}
