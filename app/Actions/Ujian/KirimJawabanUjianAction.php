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

            //  Ambil jawaban existing (kalau ada)
            $jawaban = Jawaban::where('sesi_ujian_id', $sesi->id)
                ->where('soal_id', $soal->id)
                ->first();

            //  GUARD UTAMA: jawaban sudah dikunci
            if ($jawaban && $jawaban->is_locked) {
                // Tolak diam-diam, CBT tidak berdebat
                return;
            }

            //  Kalau belum ada jawaban, buat baru
            if (! $jawaban) {
                Jawaban::create([
                    'sesi_ujian_id' => $sesi->id,
                    'soal_id'       => $soal->id,
                    'jawaban'       => $jawabanInput,
                    'is_benar'      => $this->cekBenar($soal, $jawabanInput),
                ]);
            } 
            //  Kalau sudah ada & belum dikunci, update
            else {
                $jawaban->update([
                    'jawaban'  => $jawabanInput,
                    'is_benar' => $this->cekBenar($soal, $jawabanInput),
                ]);
            }

            //  Cek progres section
            $this->handleSectionProgress($sesi);
        });
    }

    protected function cekBenar(Soal $soal, mixed $jawaban): ?bool
    {
        // Essay / soal tanpa kunci
        if ($soal->jawaban_benar === null) {
            return null;
        }

        return strtoupper((string) $jawaban) === strtoupper($soal->jawaban_benar);
    }

    protected function handleSectionProgress(SesiUjian $sesi): void
    {
        $ujian = $sesi->ujian()
            ->with('sections.soal')
            ->first();

        $sections = $ujian->sections;

        $currentIndex   = $sesi->current_section_index ?? 0;
        $currentSection = $sections[$currentIndex] ?? null;

        // Tidak ada section lagi → selesai
        if (! $currentSection) {
            $this->selesaikanUjian($sesi);
            return;
        }

        $soalIds = $currentSection->soal->pluck('id');

        $totalSoal = $soalIds->count();

        $answered = $sesi->jawaban()
            ->whereIn('soal_id', $soalIds)
            ->distinct('soal_id')
            ->count();

        // Semua soal di section ini sudah dijawab
        if ($answered >= $totalSoal) {
            $sesi->increment('current_section_index');
        }

        // Kalau sudah lewat section terakhir
        if ($sesi->current_section_index >= $sections->count()) {
            $this->selesaikanUjian($sesi);
        }
    }

    protected function selesaikanUjian(SesiUjian $sesi): void
    {
        //  Guard idempotent
        if ($sesi->status === StatusUjian::SELESAI) {
            return;
        }

        $sesi->update([
            'status'        => StatusUjian::SELESAI,
            'waktu_selesai' => now(),
        ]);
    }
}
