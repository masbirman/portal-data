<?php

namespace Database\Factories;

use App\Models\SiklusAsesmen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SiklusAsesmen>
 */
class SiklusAsesmenFactory extends Factory
{
    protected $model = SiklusAsesmen::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tahun = fake()->numberBetween(2020, 2025);
        return [
            'tahun' => $tahun,
            'nama' => 'Siklus ' . $tahun,
        ];
    }
}
