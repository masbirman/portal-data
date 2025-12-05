<?php

namespace Database\Factories;

use App\Models\PelaksanaanAsesmen;
use App\Models\Sekolah;
use App\Models\SiklusAsesmen;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PelaksanaanAsesmen>
 */
class PelaksanaanAsesmenFactory extends Factory
{
    protected $model = PelaksanaanAsesmen::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'siklus_asesmen_id' => SiklusAsesmen::factory(),
            'sekolah_id' => Sekolah::factory(),
            'wilayah_id' => Wilayah::factory(),
            'jumlah_peserta' => fake()->numberBetween(10, 500),
            'status_pelaksanaan' => fake()->randomElement(['Mandiri', 'Menumpang']),
            'moda_pelaksanaan' => fake()->randomElement(['Online', 'Semi Online']),
            'partisipasi_literasi' => fake()->randomFloat(2, 0, 100),
            'partisipasi_numerasi' => fake()->randomFloat(2, 0, 100),
            'tempat_pelaksanaan' => fake()->address(),
            'nama_penanggung_jawab' => fake()->name(),
            'nama_proktor' => fake()->name(),
            'keterangan' => fake()->optional()->sentence(),
        ];
    }
}
