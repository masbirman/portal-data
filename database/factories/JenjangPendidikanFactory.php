<?php

namespace Database\Factories;

use App\Models\JenjangPendidikan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JenjangPendidikan>
 */
class JenjangPendidikanFactory extends Factory
{
    protected $model = JenjangPendidikan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenjang = fake()->randomElement(['SD', 'SMP', 'SMA', 'SMK']);
        return [
            'kode' => $jenjang,
            'nama' => $jenjang,
        ];
    }
}
