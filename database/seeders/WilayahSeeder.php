<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $urutanMapping = [
            'Kota Palu' => 1,
            'Donggala' => 2,
            'Poso' => 3,
            'Morowali' => 4,
            'Banggai' => 5,
            'Banggai Kepulauan' => 6,
            'Toli-Toli' => 7,
            'Buol' => 8,
            'Parigi Moutong' => 9,
            'Tojo Una-Una' => 10,
            'Sigi' => 11,
            'Banggai Laut' => 12,
            'Morowali Utara' => 13,
        ];

        foreach ($urutanMapping as $nama => $urutan) {
            Wilayah::where('nama', $nama)->update(['urutan' => $urutan]);
            $this->command->info("Updated: {$nama} -> urutan {$urutan}");
        }

        $this->command->info('Wilayah urutan updated successfully!');
    }
}
