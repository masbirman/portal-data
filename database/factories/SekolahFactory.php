<?php

namespace Database\Factories;

use App\Models\Sekolah;
use App\Models\Wilayah;
use App\Models\JenjangPendidikan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sekolah>
 */
class SekolahFactory extends Factory
{
    protected $model = Sekolah::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_sekolah' => fake()->numerify('########'),
            'nama' => fake()->company() . ' School',
            'tahun' => [2024],
            'jenjang_pendidikan_id' => JenjangPendidikan::inRandomOrder()->first()?->id ?? 1,
            'wilayah_id' => Wilayah::inRandomOrder()->first()?->id ?? 1,
            'status_sekolah' => fake()->randomElement(['aktif', 'non-aktif']),
            'latitude' => fake()->latitude(-2, 1),
            'longitude' => fake()->longitude(119, 124),
            'alamat' => fake()->address(),
        ];
    }
}
