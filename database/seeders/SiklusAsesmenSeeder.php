<?php

namespace Database\Seeders;

use App\Models\SiklusAsesmen;
use Illuminate\Database\Seeder;

class SiklusAsesmenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $siklusData = [
            ['tahun' => 2023, 'nama' => 'Asesmen Nasional 2023'],
            ['tahun' => 2024, 'nama' => 'Asesmen Nasional 2024'],
            ['tahun' => 2025, 'nama' => 'Asesmen Nasional 2025'],
        ];

        foreach ($siklusData as $data) {
            SiklusAsesmen::firstOrCreate(
                ['tahun' => $data['tahun']],
                ['nama' => $data['nama']]
            );
        }

        $this->command->info('Siklus Asesmen seeded successfully!');
    }
}
