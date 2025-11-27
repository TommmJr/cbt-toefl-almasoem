<?php

namespace Database\Factories;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), 
            
            // Data dummy siswa sesuai tabel 'siswas'
            // Gunakan uniqid() agar unik namun tetap muat ke kolom NIS (<=20)
            'nis' => uniqid(),
            'nama_lengkap' => fake()->name(),
            'kelas' => fake()->randomElement(['X-IPA-1', 'XI-IPS-2', 'XII-BAHASA']),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'tanggal_lahir' => fake()->date('Y-m-d', '2008-01-01'),
            'alamat' => fake()->address(),
            'no_telepon' => fake()->phoneNumber(),
            'foto_profil' => null,
        ];
    }
}