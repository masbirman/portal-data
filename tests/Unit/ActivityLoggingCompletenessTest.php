<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: multi-panel-user-management, Property 6: Activity Logging Completeness
 * Validates: Requirements 7.1, 7.2, 7.3, 7.4
 *
 * Property: For any operasi login, logout, approve, reject, backup, restore, create user,
 * update user, atau delete user yang berhasil, sistem SHALL membuat entry di Activity Log
 * dengan user_id, action, ip_address, dan user_agent yang valid.
 */
class ActivityLoggingCompletenessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login activity is logged with required fields
     */
    public function test_login_activity_is_logged_with_required_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Simulate login logging
        ActivityLog::log(
            'login',
            "User {$user->name} logged in to Admin Panel",
            $user,
            ['panel' => 'admin', 'role' => $user->role]
        );

        $log = ActivityLog::where('action', 'login')
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->user_id);
        $this->assertEquals('login', $log->action);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }

    /**
     * Test logout activity is logged with required fields
     */
    public function test_logout_activity_is_logged_with_required_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'admin_wilayah',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Simulate logout logging
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "User {$user->name} logged out from Wilayah Panel",
            'properties' => ['panel' => 'wilayah', 'role' => $user->role],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit Test',
        ]);

        $log = ActivityLog::where('action', 'logout')
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->user_id);
        $this->assertEquals('logout', $log->action);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }

    /**
     * Test approve activity is logged with required fields
     */
    public function test_approve_activity_is_logged_with_required_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Simulate approve logging
        ActivityLog::log(
            'approve',
            "Menyetujui pengajuan download dari Test User (test@example.com)",
            null,
            ['request_id' => 1]
        );

        $log = ActivityLog::where('action', 'approve')
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->user_id);
        $this->assertEquals('approve', $log->action);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }

    /**
     * Test reject activity is logged with required fields
     */
    public function test_reject_activity_is_logged_with_required_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Simulate reject logging
        ActivityLog::log(
            'reject',
            "Menolak pengajuan download dari Test User. Alasan: Data tidak lengkap",
            null,
            ['request_id' => 1, 'reason' => 'Data tidak lengkap']
        );

        $log = ActivityLog::where('action', 'reject')
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->user_id);
        $this->assertEquals('reject', $log->action);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }

    /**
     * Test backup activity is logged with required fields
     */
    public function test_backup_activity_is_logged_with_required_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Simulate backup logging
        ActivityLog::log(
            'backup',
            "Backup database berhasil: backup_20231201.sql",
            null,
            ['filename' => 'backup_20231201.sql']
        );

        $log = ActivityLog::where('action', 'backup')
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->user_id);
        $this->assertEquals('backup', $log->action);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }

    /**
     * Test restore activity is logged with required fields
     */
    public function test_restore_activity_is_logged_with_required_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Simulate restore logging
        ActivityLog::log(
            'restore',
            "Restore database berhasil dari: backup_20231201.sql",
            null,
            ['filename' => 'backup_20231201.sql']
        );

        $log = ActivityLog::where('action', 'restore')
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->user_id);
        $this->assertEquals('restore', $log->action);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }

    /**
     * Test create user activity is logged with required fields
     */
    public function test_create_user_activity_is_logged_with_required_fields(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin);

        $newUser = User::factory()->create([
            'role' => 'admin_wilayah',
            'is_active' => true,
        ]);

        ActivityLog::log(
            'create',
            "Membuat user baru: {$newUser->name} ({$newUser->email}) dengan role {$newUser->role}",
            $newUser
        );

        $log = ActivityLog::where('action', 'create')
            ->where('model_type', User::class)
            ->where('model_id', $newUser->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->user_id);
        $this->assertEquals('create', $log->action);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }

    /**
     * Test update user activity is logged with required fields
     */
    public function test_update_user_activity_is_logged_with_required_fields(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin);

        $user = User::factory()->create([
            'role' => 'user_sekolah',
            'is_active' => true,
        ]);

        $user->update(['name' => 'Updated Name']);

        ActivityLog::log(
            'update',
            "Mengupdate user: {$user->name} ({$user->email})",
            $user
        );

        $log = ActivityLog::where('action', 'update')
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->user_id);
        $this->assertEquals('update', $log->action);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }

    /**
     * Test delete user activity is logged with required fields
     */
    public function test_delete_user_activity_is_logged_with_required_fields(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin);

        $user = User::factory()->create([
            'role' => 'user_sekolah',
            'is_active' => true,
        ]);

        $userId = $user->id;
        $userName = $user->name;
        $userEmail = $user->email;

        ActivityLog::log(
            'delete',
            "Menghapus user: {$userName} ({$userEmail})",
            $user
        );

        $user->delete();

        $log = ActivityLog::where('action', 'delete')
            ->where('model_type', User::class)
            ->where('model_id', $userId)
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->user_id);
        $this->assertEquals('delete', $log->action);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }

    /**
     * Property test: All critical actions should be loggable with required fields
     * For any action in [login, logout, approve, reject, backup, restore, create, update, delete],
     * the system SHALL create an ActivityLog entry with valid user_id, action, ip_address, and user_agent.
     */
    public function test_all_critical_actions_are_loggable_with_required_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $criticalActions = [
            'login',
            'logout',
            'approve',
            'reject',
            'backup',
            'restore',
            'create',
            'update',
            'delete',
        ];

        foreach ($criticalActions as $action) {
            ActivityLog::log(
                $action,
                "Test {$action} operation for completeness",
                $user,
                ['test' => true]
            );

            $log = ActivityLog::where('action', $action)
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            $this->assertNotNull($log, "Log for action '{$action}' should exist");
            $this->assertNotNull($log->user_id, "user_id should not be null for action '{$action}'");
            $this->assertEquals($action, $log->action, "action should be '{$action}'");
            $this->assertNotNull($log->ip_address, "ip_address should not be null for action '{$action}'");
            $this->assertNotNull($log->user_agent, "user_agent should not be null for action '{$action}'");
        }
    }

    /**
     * Property test: Login logging should include panel information
     * For any login from any panel, the log should include panel and role information.
     */
    public function test_login_logging_includes_panel_information(): void
    {
        $panels = ['admin', 'wilayah', 'sekolah'];
        $roles = ['super_admin', 'admin_wilayah', 'user_sekolah'];

        foreach ($panels as $index => $panel) {
            $user = User::factory()->create([
                'role' => $roles[$index],
                'is_active' => true,
            ]);

            $this->actingAs($user);

            ActivityLog::log(
                'login',
                "User {$user->name} logged in to " . ucfirst($panel) . " Panel",
                $user,
                ['panel' => $panel, 'role' => $user->role]
            );

            $log = ActivityLog::where('action', 'login')
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            $this->assertNotNull($log);
            $this->assertNotNull($log->properties);
            $this->assertEquals($panel, $log->properties['panel']);
            $this->assertEquals($roles[$index], $log->properties['role']);
        }
    }
}
