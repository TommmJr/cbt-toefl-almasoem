<?php

namespace Database\Factories;

use App\Models\Ujian;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UjianFactory extends Factory
{
    protected $model = Ujian::class;

    public function definition(): array
    {
        return [
            'guru_id' => \App\Models\Guru::factory(), 
            
            // FIX: Pake bothify biar pendek (Format: TEST-12345) -> Total 10 char (Aman < 20)
            'kode_ujian' => fake()->unique()->bothify('TEST-#####'), 
            
            'judul' => 'Ujian Test ' . fake()->word(),
            'deskripsi' => fake()->sentence(),
            'tipe_ujian' => 'exam',
            'durasi_menit' => 90,
            'waktu_mulai' => now(),
            'waktu_selesai' => now()->addDays(7),
            'passing_score' => 450,
            'is_published' => true,
        ];
    }
}