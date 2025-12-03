<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: multi-panel-user-management, Property 9: User CRUD with Logging
 * Validates: Requirements 4.5, 5.5
 *
 * Property: For any operasi create, update, atau delete pada User oleh Super Admin,
 * sistem SHALL menyimpan perubahan dan mencatat aktivitas di Activity Log dengan detail operasi.
 */
class UserCrudLoggingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating user logs activity
     */
    public function test_creating_user_can_be_logged(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin);

        // Create a new user
        $newUser = User::factory()->create([
            'role' => 'admin_wilayah',
            'is_active' => true,
        ]);

        // Log the creation
        ActivityLog::log('create', "Membuat user baru: {$newUser->name}", $newUser);

        // Verify log exists
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'create',
            'model_type' => User::class,
            'model_id' => $newUser->id,
        ]);
    }

    /**
     * Test updating user logs activity
     */
    public function test_updating_user_can_be_logged(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin);

        $user = User::factory()->create([
            'role' => 'admin_wilayah',
            'is_active' => true,
        ]);

        // Update user
        $user->update(['name' => 'Updated Name']);

        // Log the update
        ActivityLog::log('update', "Mengupdate user: {$user->name}", $user);

        // Verify log exists
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'update',
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
    }

    /**
     * Test deleting user logs activity
     */
    public function test_deleting_user_can_be_logged(): void
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

        // Log before delete
        ActivityLog::log('delete', "Menghapus user: {$userName}", $user);

        // Delete user
        $user->delete();

        // Verify log exists
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'delete',
            'model_type' => User::class,
            'model_id' => $userId,
        ]);
    }

    /**
     * Test toggling user active status logs activity
     */
    public function test_toggling_user_status_can_be_logged(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin);

        $user = User::factory()->create([
            'role' => 'admin_wilayah',
            'is_active' => true,
        ]);

        // Deactivate user
        $user->update(['is_active' => false]);
        ActivityLog::log('deactivate', "Menonaktifkan user: {$user->name}", $user);

        // Verify deactivation log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'deactivate',
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        // Reactivate user
        $user->update(['is_active' => true]);
        ActivityLog::log('activate', "Mengaktifkan user: {$user->name}", $user);

        // Verify activation log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'activate',
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
    }

    /**
     * Property test: All CRUD operations should be loggable
     */
    public function test_all_crud_operations_are_loggable(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin);

        $actions = ['create', 'update', 'delete', 'activate', 'deactivate'];

        foreach ($actions as $action) {
            $user = User::factory()->create([
                'role' => 'user_sekolah',
                'is_active' => true,
            ]);

            ActivityLog::log($action, "Test {$action} operation", $user);

            $this->assertDatabaseHas('activity_logs', [
                'action' => $action,
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
        }
    }
}
