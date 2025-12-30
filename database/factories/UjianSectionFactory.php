<?php

namespace Database\Factories;

use App\Models\UjianSection;
use Illuminate\Database\Eloquent\Factories\Factory;

class UjianSectionFactory extends Factory
{
    protected $model = UjianSection::class;

    public function definition(): array
    {
        return [
            'judul_section' => 'Section ' . fake()->word(),
            'urutan' => 1,
            'durasi_menit' => 30,
            'instruksi' => fake()->paragraph(),
        ];
    }
}