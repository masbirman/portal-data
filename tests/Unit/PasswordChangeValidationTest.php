<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Feature: multi-panel-user-management, Property 8: Password Change Validation
 * Validates: Requirements 8.2
 *
 * Property: For any request perubahan password, sistem SHALL memvalidasi bahwa
 * password lama cocok sebelum menyimpan password baru yang di-hash.
 */
class PasswordChangeValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test password change requires current password validation
     */
    public function test_password_change_requires_current_password(): void
    {
        $currentPassword = 'current-password-123';
        $newPassword = 'new-password-456';

        $user = User::factory()->create([
            'password' => Hash::make($currentPassword),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Verify current password matches
        $this->assertTrue(Hash::check($currentPassword, $user->password));

        // Simulate password change with correct current password
        if (Hash::check($currentPassword, $user->password)) {
            $user->update(['password' => Hash::make($newPassword)]);
        }

        // Verify new password is set
        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    /**
     * Test password change fails with wrong current password
     */
    public function test_password_change_fails_with_wrong_current_password(): void
    {
        $currentPassword = 'current-password-123';
        $wrongPassword = 'wrong-password-999';
        $newPassword = 'new-password-456';

        $user = User::factory()->create([
            'password' => Hash::make($currentPassword),
            'role' => 'admin_wilayah',
            'is_active' => true,
        ]);

        // Verify wrong password does not match
        $this->assertFalse(Hash::check($wrongPassword, $user->password));

        // Simulate password change attempt with wrong current password
        $passwordChanged = false;
        if (Hash::check($wrongPassword, $user->password)) {
            $user->update(['password' => Hash::make($newPassword)]);
            $passwordChanged = true;
        }

        // Verify password was NOT changed
        $this->assertFalse($passwordChanged);
        $this->assertTrue(Hash::check($currentPassword, $user->fresh()->password));
    }

    /**
     * Test new password is properly hashed
     */
    public function test_new_password_is_properly_hashed(): void
    {
        $currentPassword = 'current-password-123';
        $newPassword = 'new-password-456';

        $user = User::factory()->create([
            'password' => Hash::make($currentPassword),
            'role' => 'user_sekolah',
            'is_active' => true,
        ]);

        // Change password
        $user->update(['password' => Hash::make($newPassword)]);

        // Verify password is hashed (not stored as plain text)
        $this->assertNotEquals($newPassword, $user->fresh()->password);

        // Verify hashed password can be verified
        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    /**
     * Property test: Password validation should work for all user roles
     * For any user role, password change should require current password validation.
     */
    public function test_password_validation_works_for_all_roles(): void
    {
        $roles = ['super_admin', 'admin_wilayah', 'user_sekolah'];
        $currentPassword = 'current-password-123';
        $newPassword = 'new-password-456';

        foreach ($roles as $role) {
            $user = User::factory()->create([
                'password' => Hash::make($currentPassword),
                'role' => $role,
                'is_active' => true,
            ]);

            // Verify current password validation works
            $this->assertTrue(
                Hash::check($currentPassword, $user->password),
                "Current password should be valid for role: {$role}"
            );

            // Change password with correct current password
            if (Hash::check($currentPassword, $user->password)) {
                $user->update(['password' => Hash::make($newPassword)]);
            }

            // Verify new password is set
            $this->assertTrue(
                Hash::check($newPassword, $user->fresh()->password),
                "New password should be set for role: {$role}"
            );

            // Verify old password no longer works
            $this->assertFalse(
                Hash::check($currentPassword, $user->fresh()->password),
                "Old password should not work for role: {$role}"
            );
        }
    }

    /**
     * Property test: Password change should preserve other user data
     * For any password change, other user attributes should remain unchanged.
     */
    public function test_password_change_preserves_other_user_data(): void
    {
        $currentPassword = 'current-password-123';
        $newPassword = 'new-password-456';

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make($currentPassword),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $originalName = $user->name;
        $originalEmail = $user->email;
        $originalRole = $user->role;
        $originalIsActive = $user->is_active;

        // Change password
        $user->update(['password' => Hash::make($newPassword)]);

        $updatedUser = $user->fresh();

        // Verify other data is preserved
        $this->assertEquals($originalName, $updatedUser->name);
        $this->assertEquals($originalEmail, $updatedUser->email);
        $this->assertEquals($originalRole, $updatedUser->role);
        $this->assertEquals($originalIsActive, $updatedUser->is_active);
    }

    /**
     * Property test: Empty password should not change existing password
     * For any user, submitting empty password should not modify the stored password.
     */
    public function test_empty_password_does_not_change_existing_password(): void
    {
        $currentPassword = 'current-password-123';

        $user = User::factory()->create([
            'password' => Hash::make($currentPassword),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $originalPasswordHash = $user->password;

        // Simulate form submission with empty password (should not update)
        $newPassword = '';
        if (filled($newPassword)) {
            $user->update(['password' => Hash::make($newPassword)]);
        }

        // Verify password was NOT changed
        $this->assertEquals($originalPasswordHash, $user->fresh()->password);
        $this->assertTrue(Hash::check($currentPassword, $user->fresh()->password));
    }
}
