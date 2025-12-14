<?php

namespace Database\Factories;

use App\Models\Soal;
use App\Models\UjianSection;
use Illuminate\Database\Eloquent\Factories\Factory;

class SoalFactory extends Factory
{
    protected $model = Soal::class;

    public function definition(): array
    {
        return [
            // Kita kosongin dulu, nanti diisi lewat state atau relationship
            'ujian_section_id' => UjianSection::factory(),
            'tipe_soal' => 'structure', // Default
            'nomor_urut' => fake()->numberBetween(1, 50),
            'pertanyaan' => fake()->sentence() . ' _______ ' . fake()->word() . '?',
            'opsi_jawaban' => json_encode([
                'A' => fake()->word(),
                'B' => fake()->word(),
                'C' => fake()->word(),
                'D' => fake()->word(),
            ]),
            'jawaban_benar' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'bobot_nilai' => 1,
        ];
    }

    // State untuk Listening
    public function listening()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipe_soal' => 'listening',
                'pertanyaan' => 'What does the man mean?',
                // Asumsi audio path relative
                'audio_path' => 'dummy-audio/question_1.mp3', 
                'audio_duration' => 15,
            ];
        });
    }

    // State untuk Reading
    public function reading()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipe_soal' => 'reading',
                'pertanyaan' => 'What is the main idea of the passage?',
                // Passage biasanya nempel di Section, tapi kalau per soal:
                'passage' => fake()->paragraphs(3, true),
            ];
        });
    }
    
    // State untuk Structure
    public function structure()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipe_soal' => 'structure',
                'pertanyaan' => fake()->sentence(3) . ' _______ ' . fake()->sentence(3),
            ];
        });
    }
}