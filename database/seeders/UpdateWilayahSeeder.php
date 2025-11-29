<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateWilayahSeeder extends Seeder
{
    public function run(): void
    {
        $wilayahData = [
            1 => ['nama' => 'Kota Palu', 'urutan' => 1],
            2 => ['nama' => 'Donggala', 'urutan' => 2],
            3 => ['nama' => 'Sigi', 'urutan' => 11],
            4 => ['nama' => 'Parigi Moutong', 'urutan' => 9],
            5 => ['nama' => 'Tojo Una-Una', 'urutan' => 10],
            6 => ['nama' => 'Poso', 'urutan' => 3],
            7 => ['nama' => 'Morowali', 'urutan' => 4],
            8 => ['nama' => 'Morowali Utara', 'urutan' => 13],
            9 => ['nama' => 'Banggai', 'urutan' => 5],
            10 => ['nama' => 'Banggai Kepulauan', 'urutan' => 6],
            11 => ['nama' => 'Banggai Laut', 'urutan' => 12],
            12 => ['nama' => 'Toli-Toli', 'urutan' => 7],
            13 => ['nama' => 'Buol', 'urutan' => 8],
        ];

        foreach ($wilayahData as $id => $data) {
            DB::table('wilayah')
                ->where('id', $id)
                ->update($data);
        }

        $this->command->info('Wilayah names and order updated successfully!');
    }
}
