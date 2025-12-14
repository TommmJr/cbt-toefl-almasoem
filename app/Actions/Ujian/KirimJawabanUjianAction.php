<?php

declare(strict_types=1);

namespace App\Actions\Ujian;

use App\Models\SesiUjian;
use App\Models\Jawaban;
use App\Models\Soal;
use App\Enums\StatusUjian;
use Illuminate\Support\Facades\DB;

class KirimJawabanUjianAction
{
    public function execute(
        SesiUjian $sesi,
        Soal $soal,
        mixed $jawabanInput
    ): void {
        DB::transaction(function () use ($sesi, $soal, $jawabanInput) {

            // 1️⃣ Simpan / update jawaban
            Jawaban::updateOrCreate(
                [
                    'sesi_ujian_id' => $sesi->id,
                    'soal_id' => $soal->id,
                ],
                [
                    'jawaban' => $jawabanInput,
                    'is_benar' => $this->cekBenar($soal, $jawabanInput),
                ]
            );

            // 2️⃣ Cek & pindah section
            $this->handleSectionProgress($sesi);
        });
    }

    protected function cekBenar(Soal $soal, mixed $jawaban): ?bool
    {
        if ($soal->jawaban_benar === null) {
            return null; // essay
        }

        return strtoupper($jawaban) === strtoupper($soal->jawaban_benar);
    }

    protected function handleSectionProgress(SesiUjian $sesi): void
    {
        $ujian = $sesi->ujian()->with('sections.soal')->first();
        $sections = $ujian->sections;

        $currentIndex = $sesi->current_section_index ?? 0;
        $currentSection = $sections[$currentIndex] ?? null;

        // Kalau section sudah habis
        if (!$currentSection) {
            $this->selesaikanUjian($sesi);
            return;
        }

        $totalSoal = $currentSection->soal->count();

        $answered = $sesi->jawaban()
            ->whereIn(
                'soal_id',
                $currentSection->soal->pluck('id')
            )
            ->distinct('soal_id')
            ->count();

        // Semua soal di section ini sudah dijawab
        if ($answered >= $totalSoal) {
            $sesi->increment('current_section_index');
        }

        // Kalau tidak ada section berikutnya
        if ($sesi->current_section_index >= $sections->count()) {
            $this->selesaikanUjian($sesi);
        }
    }

    protected function selesaikanUjian(SesiUjian $sesi): void
    {
        $sesi->update([
            'status' => StatusUjian::SELESAI,
            'waktu_selesai' => now(),
        ]);
    }
}
