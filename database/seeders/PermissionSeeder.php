<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => Permission::VIEW_SEKOLAH_DATA,
                'label' => 'Lihat Data Sekolah',
                'description' => 'Izin untuk melihat data sekolah sendiri',
            ],
            [
                'name' => Permission::VIEW_ASESMEN_DATA,
                'label' => 'Lihat Data Asesmen',
                'description' => 'Izin untuk melihat data pelaksanaan asesmen',
            ],
            [
                'name' => Permission::DOWNLOAD_REPORT,
                'label' => 'Download Laporan',
                'description' => 'Izin untuk mengunduh laporan',
            ],
            [
                'name' => Permission::VIEW_STATISTICS,
                'label' => 'Lihat Statistik',
                'description' => 'Izin untuk melihat statistik dan grafik',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
