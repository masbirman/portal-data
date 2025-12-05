<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wilayah;

class WilayahCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Menambahkan koordinat untuk kabupaten/kota di Sulawesi Tengah
     */
    public function run(): void
    {
        // Data koordinat untuk kabupaten/kota di Sulawesi Tengah
        // Koordinat berdasarkan pusat kabupaten/kota yang akurat
        $coordinates = [
            'Kota Palu' => [
                'latitude' => -0.8917,
                'longitude' => 119.8707,
            ],
            'Donggala' => [
                'latitude' => -0.6761,
                'longitude' => 119.7442,
            ],
            'Poso' => [
                'latitude' => -1.3958,
                'longitude' => 120.7525,
            ],
            'Morowali' => [
                'latitude' => -2.8600,
                'longitude' => 122.0800,
            ],
            'Banggai' => [
                'latitude' => -1.0500,
                'longitude' => 122.8000,
            ],
            'Banggai Kepulauan' => [
                'latitude' => -1.5000,
                'longitude' => 123.5000,
            ],
            'Toli-Toli' => [
                'latitude' => 1.0500,
                'longitude' => 120.8000,
            ],
            'Buol' => [
                'latitude' => 0.9500,
                'longitude' => 121.4500,
            ],
            'Parigi Moutong' => [
                'latitude' => -0.3700,
                'longitude' => 120.0500,
            ],
            'Tojo Una-Una' => [
                'latitude' => -0.9100,
                'longitude' => 121.6200,
            ],
            'Sigi' => [
                'latitude' => -1.2500,
                'longitude' => 119.9500,
            ],
            'Banggai Laut' => [
                'latitude' => -1.6000,
                'longitude' => 123.4500,
            ],
            'Morowali Utara' => [
                'latitude' => -1.9800,
                'longitude' => 121.3400,
            ],
        ];

        foreach ($coordinates as $nama => $coords) {
            Wilayah::where('nama', $nama)->update([
                'latitude' => $coords['latitude'],
                'longitude' => $coords['longitude'],
            ]);
        }

        $this->command->info('Koordinat wilayah berhasil ditambahkan!');
    }
}
