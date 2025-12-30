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
}
