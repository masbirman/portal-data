<?php

namespace Database\Factories;

use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wilayah>
 */
class WilayahFactory extends Factory
{
    protected $model = Wilayah::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'Kabupaten ' . fake()->city(),
            'logo' => null,
            'urutan' => fake()->numberBetween(1, 20),
            'latitude' => fake()->latitude(-2, 1),
            'longitude' => fake()->longitude(119, 124),
            'geometry' => null,
        ];
    }
}
