<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusUjian;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | ACCESS CONTROL
    |--------------------------------------------------------------------------
    */

    public function pastikanBisaDiaksesOleh(int $siswaId): void
    {
        if ($this->siswa_id !== $siswaId) {
            abort(403, 'Ini bukan sesi ujian lu');
        }

        if (! $this->status->canContinue()) {
            abort(403, 'Ujian tidak bisa diakses');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SECTION FLOW (INTI CBT)
    |--------------------------------------------------------------------------
    */

    public function sectionAktif()
    {
        return $this->ujian
            ->sections()
            ->orderBy('urutan')
            ->skip($this->current_section_index)
            ->first();
    }

    public function sectionSelesai(): bool
    {
        $section = $this->sectionAktif();

        if (! $section) {
            return true;
        }

        $soalIds = $section->soal()->pluck('id');
        $jumlahSoal = $soalIds->count();

        if ($jumlahSoal === 0) {
            return true;
        }

        $jawabanMasuk = $this->jawaban()
            ->whereIn('soal_id', $soalIds)
            ->count();

        return $jumlahSoal === $jawabanMasuk;
    }

    public function lanjutKeSectionBerikutnya(): void
    {
        $this->increment('current_section_index');
    }

    public function semuaSectionSelesai(): bool
    {
        return $this->sectionAktif() === null;
    }

    /*
    |--------------------------------------------------------------------------
    | TIMER (SERVER-SIDE)
    |--------------------------------------------------------------------------
    */

    public function sisaWaktuSection(): int
    {
        $section = $this->sectionAktif();

        if (! $section || ! $this->section_mulai_at) {
            return 0;
        }

        $durasiDetik = $section->durasi_menit * 60;
        $habisPada = $this->section_mulai_at->copy()->addSeconds($durasiDetik);

        return now()->diffInSeconds($habisPada, false);
    }
}
