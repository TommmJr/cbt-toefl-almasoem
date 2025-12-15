<?php

declare(strict_types=1);

namespace App\Actions\Ujian;

use App\Models\SesiUjian;
use App\Models\UjianSection;

class HitungSkorSectionAction
{
    /**
     * Hitung skor pilihan ganda untuk 1 section
     *
     * @return array{
     *   total_soal: int,
     *   total_pg: int,
     *   benar: int,
     *   salah: int
     * }
     */
    public function execute(
        SesiUjian $sesi,
        UjianSection $section
    ): array {
        // Ambil semua soal di section ini
        $soalIds = $section->soal()
            ->whereNotNull('jawaban_benar') // PG only
            ->pluck('id');

        $totalPg = $soalIds->count();

        if ($totalPg === 0) {
            return [
                'total_soal' => 0,
                'total_pg'   => 0,
                'benar'      => 0,
                'salah'      => 0,
            ];
        }

        // Hitung jawaban benar
        $benar = $sesi->jawaban()
            ->whereIn('soal_id', $soalIds)
            ->where('is_benar', true)
            ->count();

        return [
            'total_soal' => $section->soal()->count(),
            'total_pg'   => $totalPg,
            'benar'      => $benar,
            'salah'      => $totalPg - $benar,
        ];
    }
}
