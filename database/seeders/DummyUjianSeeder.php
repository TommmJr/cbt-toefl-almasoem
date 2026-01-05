<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ujian;
use App\Models\UjianSection;
use App\Enums\TipeUjian;
use Illuminate\Support\Facades\DB;

class DummyUjianSeeder extends Seeder
{
    public function run(): void
    {
        $guruId = DB::table('guru')
            ->whereNull('deleted_at')
            ->value('id');

        if (! $guruId) {
            throw new \Exception('Seeder error: Data guru tidak ditemukan di tabel guru.');
        }

        $ujian = Ujian::firstOrCreate(
            ['kode_ujian' => 'DUMMY-TOEFL'],
            [
                'guru_id' => $guruId,
                'judul' => 'Simulasi TOEFL ITP',
                'deskripsi' => 'Ujian dummy untuk testing alur siswa',
                'tipe_ujian' => TipeUjian::PRACTICE,
                'durasi_menit' => 115,
                'waktu_mulai' => now()->subDay(),
                'waktu_selesai' => now()->addDay(),
                'is_published' => true,
            ]
        );

        // UPDATE URUTAN DI SINI:
        // 1. Reading
        // 2. Writing (Structure)
        // 3. Listening
        $sections = [
            [
                'judul_section' => 'Reading Comprehension', 
                'tipe_section' => 'reading',    
                'urutan' => 1, 
                'durasi_menit' => 55
            ],
            [
                'judul_section' => 'Structure & Written',   
                'tipe_section' => 'writing',    
                'urutan' => 2, 
                'durasi_menit' => 25
            ],
            [
                'judul_section' => 'Listening Comprehension', 
                'tipe_section' => 'listening',  
                'urutan' => 3, 
                'durasi_menit' => 35
            ],
        ];

        foreach ($sections as $s) {
            UjianSection::updateOrCreate(
                [
                    'ujian_id' => $ujian->id, 
                    'tipe_section' => $s['tipe_section'] // Kunci update biar gak duplikat
                ],
                array_merge($s, ['ujian_id' => $ujian->id])
            );
        }
    }
}