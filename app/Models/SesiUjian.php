<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusUjian;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Support\Facades\DB;

class SesiUjian extends Model
{
    use HasFactory;

    protected $fillable = [
        'ujian_id',
        'siswa_id',
        'status',
        'current_section_index',
        'section_mulai_at',
        'waktu_mulai',
        'waktu_selesai',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'status' => StatusUjian::class,
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'section_mulai_at' => 'datetime',
    ];

    /* ================= RELATIONS ================= */

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(Jawaban::class);
    }

    /* ================= ACCESS ================= */

    public function pastikanMilikSiswa(int $siswaId): void
    {
        if ((int) $this->siswa_id !== (int) $siswaId) {
            abort(403, 'Bukan pemilik sesi');
        }
    }

    public function pastikanBelumSelesai(): void
    {
        if ($this->status === StatusUjian::SELESAI) {
            abort(403, 'UJIAN SUDAH SELESAI');
        }
    }

    public function pastikanBisaDiaksesOleh(int $siswaId): void
    {
        if ((int) $this->siswa_id !== (int) $siswaId) {
            throw new AccessDeniedHttpException('Bukan sesi anda');
        }
    }

    /* ================= SECTION FLOW ================= */

    public function sectionAktif()
    {
        return $this->ujian
            ->sections()
            ->orderBy('urutan')
            ->skip((int) $this->current_section_index)
            ->first();
    }

    public function lanjutKeSectionBerikutnya(): void
    {
        $this->update([
            'current_section_index' => (int) $this->current_section_index + 1,
            'section_mulai_at' => now(),
        ]);
    }

    public function semuaSectionSelesai(): bool
    {
        $total = (int) $this->ujian->sections()->count();
        return (int) $this->current_section_index >= $total;
    }

    /* ================= TIMER ================= */

    public function sectionEndTime(): Carbon
    {
        // Safety check kalau section_mulai_at null
        if (!$this->section_mulai_at) {
            return now(); 
        }
        
        return $this->section_mulai_at
            ->copy()
            ->addMinutes((int) $this->sectionAktif()->durasi_menit);
    }

    public function isSectionExpired(): bool
    {
        if (! $this->section_mulai_at || ! $this->sectionAktif()) {
            return true;
        }

        return now()->greaterThanOrEqualTo($this->sectionEndTime());
    }

    public function sisaWaktuSection(): int
    {
        if (! $this->section_mulai_at || ! $this->sectionAktif()) {
            return 0;
        }

        return max(
            0,
            (int) now()->diffInSeconds($this->sectionEndTime(), false)
        );
    }


    /* ================= HITUNG NILAI & SELESAI ================= */

    public function hitungNilaiDanSelesai()
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            
            // 1. Ambil Jawaban User + Soal + UjianSection (Sesuai nama relasi di model Soal)
            $semuaJawaban = $this->jawaban()->with(['soal.ujianSection'])->get();

            $benarListening = 0;
            $benarStructure = 0;
            $benarReading   = 0;

            // 2. Hitung Jawaban Benar
            foreach ($semuaJawaban as $jawaban) {
                // Pakai relasi 'ujianSection' bukan 'section'
                $section = $jawaban->soal?->ujianSection; 
                
                // Cek kebenaran (pake 'is_benar' sesuai model abang)
                if (($jawaban->is_benar || $jawaban->is_correct) && $section) {
                    
                    $tipe = strtolower($section->tipe_section ?? '');
                    
                    if (str_contains($tipe, 'listen')) {
                        $benarListening++;
                    } elseif (str_contains($tipe, 'struc')) {
                        $benarStructure++;
                    } elseif (str_contains($tipe, 'read')) {
                        $benarReading++;
                    }
                }
            }

            // 3. Konversi Skor (Pake Helper private convertScoreToefl di bawah)
            $skorListening = $this->convertScoreToefl('listening', $benarListening);
            $skorStructure = $this->convertScoreToefl('structure', $benarStructure);
            $skorReading   = $this->convertScoreToefl('reading', $benarReading);

            // 4. Hitung Total TOEFL ITP
            $skorTotal = round((($skorListening + $skorStructure + $skorReading) * 10) / 3);

            // 5. Simpan ke Table NILAI
            $nilai = \App\Models\Nilai::updateOrCreate(
                ['sesi_ujian_id' => $this->id],
                [
                    'siswa_id'       => $this->siswa_id,
                    'ujian_id'       => $this->ujian_id,
                    'skor_listening' => $skorListening,
                    'skor_structure' => $skorStructure,
                    'skor_reading'   => $skorReading,
                    'skor_total'     => $skorTotal,
                    'tanggal_ujian'  => now(),
                ]
            );

            // 6. Update Status Sesi Jadi SELESAI
            $this->status = \App\Enums\StatusUjian::SELESAI;
            $this->waktu_selesai = now();
            $this->save();
        });
    }

    /**
     * Helper Konversi Skor TOEFL ITP (Hardcoded Standard Table)
     * Biar gak perlu query DB lagi.
     */
    private function convertScoreToefl($section, $correctCount)
    {
        // Data Konversi Standar TOEFL ITP
        // Format: [Jumlah Benar => Skor Konversi]
        
        $listeningTable = [
            0 => 24, 1 => 25, 2 => 26, 3 => 27, 4 => 28, 5 => 29, 
            6 => 30, 7 => 31, 8 => 32, 9 => 32, 10 => 33, 11 => 35, 
            12 => 37, 13 => 37, 14 => 38, 15 => 41, 16 => 41, 17 => 42, 
            18 => 43, 19 => 44, 20 => 45, 21 => 45, 22 => 46, 23 => 47, 
            24 => 47, 25 => 48, 26 => 48, 27 => 49, 28 => 49, 29 => 50, 
            30 => 51, 31 => 51, 32 => 52, 33 => 52, 34 => 53, 35 => 54, 
            36 => 54, 37 => 55, 38 => 56, 39 => 57, 40 => 57, 41 => 58, 
            42 => 59, 43 => 60, 44 => 61, 45 => 62, 46 => 63, 47 => 64, 
            48 => 65, 49 => 66, 50 => 68
        ];

        $structureTable = [
            0 => 20, 1 => 20, 2 => 21, 3 => 22, 4 => 23, 5 => 25, 
            6 => 26, 7 => 27, 8 => 29, 9 => 31, 10 => 33, 11 => 35, 
            12 => 36, 13 => 37, 14 => 38, 15 => 40, 16 => 40, 17 => 41, 
            18 => 42, 19 => 43, 20 => 44, 21 => 45, 22 => 46, 23 => 47, 
            24 => 48, 25 => 49, 26 => 50, 27 => 51, 28 => 52, 29 => 53, 
            30 => 54, 31 => 55, 32 => 56, 33 => 57, 34 => 58, 35 => 60, 
            36 => 61, 37 => 63, 38 => 65, 39 => 67, 40 => 68
        ];

        $readingTable = [
            0 => 21, 1 => 22, 2 => 23, 3 => 23, 4 => 24, 5 => 25, 
            6 => 26, 7 => 27, 8 => 28, 9 => 28, 10 => 29, 11 => 30, 
            12 => 31, 13 => 32, 14 => 34, 15 => 35, 16 => 36, 17 => 37, 
            18 => 38, 19 => 39, 20 => 40, 21 => 41, 22 => 42, 23 => 43, 
            24 => 43, 25 => 44, 26 => 45, 27 => 46, 28 => 46, 29 => 47, 
            30 => 48, 31 => 48, 32 => 49, 33 => 50, 34 => 51, 35 => 52, 
            36 => 52, 37 => 53, 38 => 54, 39 => 54, 40 => 55, 41 => 56, 
            42 => 57, 43 => 58, 44 => 59, 45 => 60, 46 => 61, 47 => 63, 
            48 => 65, 49 => 66, 50 => 67
        ];

        $table = [];
        if (str_contains($section, 'listen')) $table = $listeningTable;
        elseif (str_contains($section, 'struc')) $table = $structureTable;
        elseif (str_contains($section, 'read')) $table = $readingTable;

        // Ambil nilai konversi, kalau jumlah benar melebihi max tabel, ambil max-nya
        return $table[$correctCount] ?? end($table);
    }
}