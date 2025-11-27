<?php

namespace Database\Seeders;

use App\Models\{Ujian, UjianSection, Soal};
use App\Enums\TipeSoal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateOldDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Untuk setiap ujian yang ada
            Ujian::chunk(100, function ($ujians) {
                foreach ($ujians as $ujian) {
                    // 2. Buat 3 section default (Listening, Structure, Reading)
                    $sections = [
                        [
                            'tipe_section' => TipeSoal::LISTENING,
                            'urutan' => 1,
                            'durasi_menit' => 35,
                            'jumlah_soal_target' => 50,
                        ],
                        [
                            'tipe_section' => TipeSoal::STRUCTURE,
                            'urutan' => 2,
                            'durasi_menit' => 25,
                            'jumlah_soal_target' => 40,
                        ],
                        [
                            'tipe_section' => TipeSoal::READING,
                            'urutan' => 3,
                            'durasi_menit' => 55,
                            'jumlah_soal_target' => 50,
                        ],
                    ];

                    foreach ($sections as $sectionData) {
                        $section = $ujian->sections()->create($sectionData);

                        // 3. Migrate soal lama ke section baru
                        Soal::where('ujian_id', $ujian->id)
                            ->where('tipe_soal', $sectionData['tipe_section']->value)
                            ->update(['ujian_section_id' => $section->id]);
                    }
                }
            });

            $this->command->info(' Data migration selesai!');
        });
    }
}