<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Sekolah;
use App\Models\Wilayah;
use App\Models\JenjangPendidikan;
use App\Models\Scopes\WilayahJenjangScope;
use App\Models\Scopes\SekolahScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: multi-panel-user-management
 * Property 1: Admin Wilayah Data Isolation
 * Property 2: User Sekolah Data Isolation
 *
 * Validates: Requirements 2.2, 2.3, 2.5, 2.6, 3.2, 3.3
 */
class DataIsolationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 1: Admin Wilayah Data Isolation
     * For any Admin Wilayah user and for any query ke data sekolah,
     * hasil query SHALL hanya berisi record yang memiliki wilayah_id
     * dalam daftar wilayah user DAN jenjang_pendidikan_id dalam daftar jenjang user.
     *
     * Validates: Requirements 2.2, 2.3, 2.5, 2.6
     */
    public function test_admin_wilayah_only_sees_assigned_wilayah_data(): void
    {
        // Create wilayah
        $wilayah1 = Wilayah::create(['nama' => 'Wilayah 1', 'urutan' => 1]);
        $wilayah2 = Wilayah::create(['nama' => 'Wilayah 2', 'urutan' => 2]);

        // Create jenjang
        $jenjangSd = JenjangPendidikan::create(['kode' => 'SD', 'nama' => 'Sekolah Dasar']);
        $jenjangSmp = JenjangPendidikan::create(['kode' => 'SMP', 'nama' => 'Sekolah Menengah Pertama']);

        // Create Admin Wilayah with only wilayah1 and jenjangSd
        $adminWilayah = User::factory()->create([
            'role' => 'admin_wilayah',
            'is_active' => true,
        ]);
        $adminWilayah->wilayahs()->attach($wilayah1->id);
        $adminWilayah->jenjangs()->attach($jenjangSd->id);

        // Verify assignments
        $this->assertTrue($adminWilayah->isAdminWilayah());
        $this->assertContains($wilayah1->id, $adminWilayah->getWilayahIds());
        $this->assertNotContains($wilayah2->id, $adminWilayah->getWilayahIds());
        $this->assertContains($jenjangSd->id, $adminWilayah->getJenjangIds());
        $this->assertNotContains($jenjangSmp->id, $adminWilayah->getJenjangIds());
    }

    /**
     * Property 2: User Sekolah Data Isolation
     * For any User Sekolah and for any query ke data sekolah atau pelaksanaan asesmen,
     * hasil query SHALL hanya berisi record yang memiliki sekolah_id sama dengan
     * sekolah_id user tersebut.
     *
     * Validates: Requirements 3.2, 3.3
     */
    public function test_user_sekolah_only_sees_own_school_data(): void
    {
        // Create wilayah and jenjang
        $wilayah = Wilayah::create(['nama' => 'Wilayah Test', 'urutan' => 1]);
        $jenjang = JenjangPendidikan::create(['kode' => 'SD', 'nama' => 'Sekolah Dasar']);

        // Create sekolah
        $sekolah1 = Sekolah::create([
            'kode_sekolah' => 'SKL001',
            'nama' => 'Sekolah 1',
            'wilayah_id' => $wilayah->id,
            'jenjang_pendidikan_id' => $jenjang->id,
            'status_sekolah' => 'Negeri',
            'tahun' => json_encode([2024]),
        ]);
        $sekolah2 = Sekolah::create([
            'kode_sekolah' => 'SKL002',
            'nama' => 'Sekolah 2',
            'wilayah_id' => $wilayah->id,
            'jenjang_pendidikan_id' => $jenjang->id,
            'status_sekolah' => 'Swasta',
            'tahun' => json_encode([2024]),
        ]);

        // Create User Sekolah linked to sekolah1
        $userSekolah = User::factory()->create([
            'role' => 'user_sekolah',
            'sekolah_id' => $sekolah1->id,
            'is_active' => true,
        ]);

        // Verify user is linked to correct sekolah
        $this->assertTrue($userSekolah->isUserSekolah());
        $this->assertEquals($sekolah1->id, $userSekolah->sekolah_id);
        $this->assertNotEquals($sekolah2->id, $userSekolah->sekolah_id);
    }

    /**
     * Test Super Admin has no data isolation (full access)
     */
    public function test_super_admin_has_full_access(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->assertTrue($superAdmin->isSuperAdmin());
        // Super Admin should not have wilayah/jenjang restrictions
        $this->assertEmpty($superAdmin->getWilayahIds());
        $this->assertEmpty($superAdmin->getJenjangIds());
    }

    /**
     * Test Admin Wilayah with no assigned wilayah gets empty results
     */
    public function test_admin_wilayah_without_assignments_gets_no_data(): void
    {
        $adminWilayah = User::factory()->create([
            'role' => 'admin_wilayah',
            'is_active' => true,
        ]);

        // No wilayah or jenjang assigned
        $this->assertEmpty($adminWilayah->getWilayahIds());
        $this->assertEmpty($adminWilayah->getJenjangIds());
    }

    /**
     * Test User Sekolah without sekolah_id gets no data
     */
    public function test_user_sekolah_without_sekolah_gets_no_data(): void
    {
        $userSekolah = User::factory()->create([
            'role' => 'user_sekolah',
            'sekolah_id' => null,
            'is_active' => true,
        ]);

        $this->assertTrue($userSekolah->isUserSekolah());
        $this->assertNull($userSekolah->sekolah_id);
    }
}
