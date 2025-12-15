<?php

namespace App\Actions\Ujian;

use App\Models\SesiUjian;
use App\Enums\StatusUjian;
use Illuminate\Support\Facades\DB;

class AutoSubmitSectionAction
{
    public function handle(SesiUjian $sesi): void
    {
        if (! $sesi->isSectionExpired()) {
            return;
        }

        $this->submit($sesi);
    }

    public function submit(SesiUjian $sesi): void
    {
        $section = $sesi->sectionAktif();
        if (! $section) return;

        DB::transaction(function () use ($sesi, $section) {

            $soalIds = $section->soal()->pluck('id');

            // Kunci jawaban biar gak bisa diedit lagi
            $sesi->jawaban()
                ->whereIn('soal_id', $soalIds)
                ->where('is_locked', false)
                ->update(['is_locked' => true]);

            // Hitung nilai (Perbaikan nama method di sini)
            $sesi->jawaban()
                ->whereIn('soal_id', $soalIds)
                ->get()
                ->each(fn ($j) => $j->nilaiPilihanGanda());

            // Cek lanjut atau selesai
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