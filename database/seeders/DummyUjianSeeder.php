<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ujian;
use App\Models\UjianSection;
use App\Models\Soal;
use App\Enums\TipeUjian; // Pastikan Enum ini ada, atau ganti string biasa
use Illuminate\Support\Facades\DB;

class DummyUjianSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil ID Guru (Siapa aja yg ada)
        $guruId = DB::table('guru')->whereNull('deleted_at')->value('id');

        if (!$guruId) {
            // Fallback kalau tabel guru kosong, ambil user pertama
            $guruId = DB::table('users')->value('id');
        }

        // 2. Buat Ujian Induk
        // Pake updateOrCreate biar gak duplikat kalau di-seed ulang
        $ujian = Ujian::updateOrCreate(
            ['judul' => 'TOEFL Prediction - Full Simulation'], // Kunci pencarian
            [
                'guru_id' => $guruId,
                'kode_ujian' => 'TOEFL-001',
                'deskripsi' => 'Simulasi lengkap: Listening, Structure, Reading, Writing.',
                'durasi_menit' => 140,
                'waktu_mulai' => now()->subDays(1),
                'waktu_selesai' => now()->addYears(1),
                'is_published' => true,
                // 'tipe_ujian' => 'practice' // Sesuaikan kalau pake Enum/String
            ]
        );

        // SECTION 1: LISTENING (Audio)
        $secListening = UjianSection::updateOrCreate(
            ['ujian_id' => $ujian->id, 'tipe_section' => 'listening'],
            [
                'judul_section' => 'Listening Comprehension',
                'durasi_menit' => 40,
                'urutan' => 1
            ]
        );

        // Soal Listening
        Soal::firstOrCreate(
            ['ujian_section_id' => $secListening->id, 'nomor_urut' => 1],
            [
                'tipe_soal' => 'listening',
                'pertanyaan' => '<p>Listen to the conversation. What does the man imply?</p>',
                'audio_path' => 'soal/audio.mp3', // Pastikan file ini ada di storage!
                'opsi_jawaban' => json_encode([
                    'A' => 'He will study tonight.',
                    'B' => 'He is going to the cinema.',
                    'C' => 'He wants to eat dinner.',
                    'D' => 'He is sleeping early.'
                ]),
                'jawaban_benar' => 'A',
                'bobot_nilai' => 10
            ]
        );

        // SECTION 2: STRUCTURE (Grammar - Pilihan Ganda)
        $secStructure = UjianSection::updateOrCreate(
            ['ujian_id' => $ujian->id, 'tipe_section' => 'structure'],
            [
                'judul_section' => 'Structure & Written Expression',
                'durasi_menit' => 25,
                'urutan' => 2
            ]
        );

        // Soal Structure
        Soal::firstOrCreate(
            ['ujian_section_id' => $secStructure->id, 'nomor_urut' => 1],
            [
                'tipe_soal' => 'structure',
                'pertanyaan' => '<p>The sun ______ in the east and sets in the west.</p>',
                'opsi_jawaban' => json_encode([
                    'A' => 'rise',
                    'B' => 'rises',
                    'C' => 'rose',
                    'D' => 'rising'
                ]),
                'jawaban_benar' => 'B',
                'bobot_nilai' => 10
            ]
        );

        // SECTION 3: READING (Bacaan)
        $secReading = UjianSection::updateOrCreate(
            ['ujian_id' => $ujian->id, 'tipe_section' => 'reading'],
            [
                'judul_section' => 'Reading Comprehension',
                'durasi_menit' => 55,
                'urutan' => 3
            ]
        );

        // Soal Reading
        Soal::firstOrCreate(
            ['ujian_section_id' => $secReading->id, 'nomor_urut' => 1],
            [
                'tipe_soal' => 'reading',
                'passage' => '<div class="trix-content"><h3>The History of Internet</h3><p>The history of the Internet has its origin in...</p></div>',
                'pertanyaan' => '<p>What is the main topic of the passage?</p>',
                'opsi_jawaban' => json_encode([
                    'A' => 'Computer Science',
                    'B' => 'The History of Internet',
                    'C' => 'Modern Technology',
                    'D' => 'Future AI'
                ]),
                'jawaban_benar' => 'B',
                'bobot_nilai' => 10
            ]
        );

        // SECTION 4: WRITING (Essay)
        $secWriting = UjianSection::updateOrCreate(
            ['ujian_id' => $ujian->id, 'tipe_section' => 'writing'],
            [
                'judul_section' => 'Essay Writing',
                'durasi_menit' => 30,
                'urutan' => 4
            ]
        );

        // Soal Essay
        Soal::firstOrCreate(
            ['ujian_section_id' => $secWriting->id, 'nomor_urut' => 1],
            [
                'tipe_soal' => 'writing', // Ini baru trigger Textarea
                'pertanyaan' => '<p>Do you agree or disagree with the following statement? Technology has made children less creative. Use specific reasons and examples to support your opinion.</p>',
                'opsi_jawaban' => null,
                'jawaban_benar' => null,
                'bobot_nilai' => 30,
                'min_kata' => 250
            ]
        );
    }
}