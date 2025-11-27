<?php

namespace Database\Factories;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuruFactory extends Factory
{
    protected $model = Guru::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Bikin user baru otomatis
            'nip' => fake()->unique()->numerify('19##########'),
            'nama_lengkap' => fake()->name(),
            'mata_pelajaran' => 'Bahasa Inggris',
        ];
    }
}
