<?php

namespace Database\Seeders;

use App\Models\JenjangPendidikan;
use Illuminate\Database\Seeder;

class JenjangPendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenjangs = [
            'SMA',
            'SMK',
            'SMP',
            'SD',
            'SMALB',
            'SMPLB',
            'SDLB',
            'PAKET C',
            'PAKET B',
            'PAKET A',
        ];

        foreach ($jenjangs as $jenjang) {
            JenjangPendidikan::create([
                'kode' => $jenjang,
                'nama' => $jenjang,
            ]);
        }
    }
}
