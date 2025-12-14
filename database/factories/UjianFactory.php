<?php

namespace Database\Factories;

use App\Models\Ujian;
use App\Models\UjianSection;
use App\Models\Soal;
use App\Models\Guru; // Pastikan import Guru
use Illuminate\Database\Eloquent\Factories\Factory;

class UjianFactory extends Factory
{
    protected $model = Ujian::class;

    public function definition(): array
    {
        return [
            'guru_id' => Guru::factory(),
            'kode_ujian' => fake()->unique()->bothify('TOEFL-#####'),
            'judul' => 'TOEFL Prediction Test ' . fake()->date(),
            'deskripsi' => 'Ujian simulasi TOEFL lengkap (Listening, Structure, Reading).',
            'tipe_ujian' => 'exam',
            'durasi_menit' => 120, // Total durasi global (display only)
            'waktu_mulai' => now()->subDay(),
            'waktu_selesai' => now()->addDays(7),
            'passing_score' => 450,
            'is_published' => true,
        ];
    }

    /**
     * State untuk generate soal lengkap (Listening, Structure, Reading)
     */
    public function withSoalLengkap()
    {
        return $this->afterCreating(function (Ujian $ujian) {
            
            // 1. Buat Section Listening (30 Menit)
            $listening = UjianSection::factory()->create([
                'ujian_id' => $ujian->id,
                'tipe_section' => 'listening',
                'judul_section' => 'Section 1: Listening Comprehension',
                'urutan' => 1,
                'durasi_menit' => 30, // Timer section jalan 30 menit
                'instruksi' => 'Listen to the audio and answer the questions.',
            ]);
            
            // Buat 5 Soal Listening Dummy
            Soal::factory()->count(5)->listening()->create([
                'ujian_section_id' => $listening->id,
            ]);

            // 2. Buat Section Structure (25 Menit)
            $structure = UjianSection::factory()->create([
                'ujian_id' => $ujian->id,
                'tipe_section' => 'structure',
                'judul_section' => 'Section 2: Structure and Written Expression',
                'urutan' => 2,
                'durasi_menit' => 25,
                'instruksi' => 'Choose the correct answer to complete the sentence.',
            ]);

            // Buat 5 Soal Structure Dummy
            Soal::factory()->count(5)->structure()->create([
                'ujian_section_id' => $structure->id,
            ]);

            // 3. Buat Section Reading (55 Menit)
            $reading = UjianSection::factory()->create([
                'ujian_id' => $ujian->id,
                'tipe_section' => 'reading',
                'judul_section' => 'Section 3: Reading Comprehension',
                'urutan' => 3,
                'durasi_menit' => 55,
                'instruksi' => 'Read the passage and answer the questions.',
            ]);

            // Buat 5 Soal Reading Dummy
            Soal::factory()->count(5)->reading()->create([
                'ujian_section_id' => $reading->id,
            ]);
        });
    }
}