<?php

namespace App\Actions\Ujian;

use App\Models\SesiUjian;
use App\Enums\StatusUjian;
use Illuminate\Support\Facades\DB;

class AutoSubmitSectionAction
{
    /**
     * AUTO SUBMIT
     * - Dipanggil saat page load
     * - Dipanggil saat timer habis
     */
    public function handle(SesiUjian $sesi): array
    {
        if (! $sesi->isSectionExpired()) {
            return [];
        }

        $this->submit($sesi);

        if ($sesi->status === StatusUjian::SELESAI) {
            return [
                'redirect' => route('siswa.hasil', $sesi->id),
            ];
        }

        return [
            'redirect' => route('siswa.ujian.mulai', $sesi->id),
        ];
    }

    /**
     * SUBMIT SECTION
     * - Manual button
     * - Auto saat waktu habis
     */
    public function submit(SesiUjian $sesi): void
    {
        $section = $sesi->sectionAktif();
        if (! $section) {
            return;
        }

        DB::transaction(function () use ($sesi, $section) {

            $soalIds = $section->soal()->pluck('id');

            // 1. Kunci jawaban section ini
            $sesi->jawaban()
                ->whereIn('soal_id', $soalIds)
                ->where('is_locked', false)
                ->update(['is_locked' => true]);

            // 2. Nilai pilihan ganda
            $sesi->jawaban()
                ->whereIn('soal_id', $soalIds)
                ->get()
                ->each(fn ($j) => $j->nilaiPilihanGanda());

            // 3. Reset timer
            $sesi->update([
                'section_mulai_at' => null,
            ]);

            // 4. Lanjut atau selesai
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
