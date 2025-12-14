<?php

namespace App\Actions\Ujian;

use App\Models\SesiUjian;
use App\Enums\StatusUjian;
use Illuminate\Support\Facades\DB;

class AutoSubmitSectionAction
{
    public function handle(SesiUjian $sesi): void
    {
        $section = $sesi->sectionAktif();

        if (! $section) {
            return;
        }

        if (! $sesi->isSectionExpired()) {
            return;
        }

        DB::transaction(function () use ($sesi, $section) {

            // Kunci jawaban (opsional flag kalau ada)
            $sesi->jawaban()
                ->whereIn('soal_id', $section->soal()->pluck('id'))
                ->update(['is_locked' => true]);

            // Nilai PG
            $sesi->jawaban
                ->each(fn ($j) => $j->cekJawabanPilihanGanda());

            // Pindah section atau selesai
            if ($sesi->semuaSectionSelesai()) {
                $sesi->update([
                    'status' => StatusUjian::SELESAI,
                    'waktu_selesai' => now(),
                ]);
            } else {
                $sesi->lanjutKeSectionBerikutnya();
            }
        });
    }
}
