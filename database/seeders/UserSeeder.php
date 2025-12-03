<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use App\Models\Wilayah;
use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createSuperAdmin();
        $this->createSampleAdminWilayah();
        $this->createSampleUserSekolah();
    }

    /**
     * Create default Super Admin user
     */
    protected function createSuperAdmin(): void
    {
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@birman.app',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        $this->command->info('Super Admin created: superadmin / password');
    }

    /**
     * Create sample Admin Wilayah users
     */
    protected function createSampleAdminWilayah(): void
    {
        // Get some wilayah and jenjang for assignment
        $wilayahs = Wilayah::take(3)->get();
        $jenjangs = JenjangPendidikan::take(2)->get();

        if ($wilayahs->isEmpty() || $jenjangs->isEmpty()) {
            $this->command->warn('Skipping Admin Wilayah seeder: No wilayah or jenjang data found');
            return;
        }

        // Create Admin Wilayah 1
        $adminWilayah1 = User::updateOrCreate(
            ['username' => 'admin.wilayah1'],
            [
                'name' => 'Admin Wilayah 1',
                'email' => 'admin.wilayah1@birman.app',
                'password' => Hash::make('password'),
                'role' => 'admin_wilayah',
                'is_active' => true,
            ]
        );

        // Assign first wilayah and first jenjang
        if ($wilayahs->count() > 0) {
            $adminWilayah1->wilayahs()->sync([$wilayahs->first()->id]);
        }
        if ($jenjangs->count() > 0) {
            $adminWilayah1->jenjangs()->sync([$jenjangs->first()->id]);
        }

        $this->command->info('Admin Wilayah 1 created: admin.wilayah1 / password');

        // Create Admin Wilayah 2 with multiple wilayah and jenjang
        if ($wilayahs->count() >= 2) {
            $adminWilayah2 = User::updateOrCreate(
                ['username' => 'admin.wilayah2'],
                [
                    'name' => 'Admin Wilayah 2',
                    'email' => 'admin.wilayah2@birman.app',
                    'password' => Hash::make('password'),
                    'role' => 'admin_wilayah',
                    'is_active' => true,
                ]
            );

            // Assign multiple wilayah and jenjang
            $adminWilayah2->wilayahs()->sync($wilayahs->take(2)->pluck('id')->toArray());
            $adminWilayah2->jenjangs()->sync($jenjangs->pluck('id')->toArray());

            $this->command->info('Admin Wilayah 2 created: admin.wilayah2 / password');
        }
    }

    /**
     * Create sample User Sekolah users
     */
    protected function createSampleUserSekolah(): void
    {
        // Get some sekolah for assignment
        $sekolahs = Sekolah::take(3)->get();
        $permissions = Permission::all();

        if ($sekolahs->isEmpty()) {
            $this->command->warn('Skipping User Sekolah seeder: No sekolah data found');
            return;
        }

        foreach ($sekolahs->take(2) as $index => $sekolah) {
            $userSekolah = User::updateOrCreate(
                ['username' => $sekolah->kode_sekolah],
                [
                    'name' => 'User ' . $sekolah->nama,
                    'email' => strtolower(str_replace(' ', '.', $sekolah->kode_sekolah)) . '@birman.app',
                    'password' => Hash::make('password'),
                    'role' => 'user_sekolah',
                    'sekolah_id' => $sekolah->id,
                    'is_active' => true,
                ]
            );

            // Assign all permissions to first user, limited to second
            if ($index === 0) {
                $userSekolah->permissions()->sync($permissions->pluck('id')->toArray());
            } else {
                // Only view permissions for second user
                $viewPermissions = $permissions->filter(function ($p) {
                    return str_starts_with($p->name, 'view_');
                });
                $userSekolah->permissions()->sync($viewPermissions->pluck('id')->toArray());
            }

            $this->command->info("User Sekolah created: {$sekolah->kode_sekolah} / password");
        }
    }
}
