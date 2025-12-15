<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusUjian;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    /* ================= ACCESS CONTROL ================= */

    public function pastikanMilikSiswa(int $siswaId): void
    {
        if ($this->siswa_id !== $siswaId) {
            abort(403, 'Bukan pemilik sesi');
        }
    }

    public function pastikanBelumSelesai(): void
    {
        if ($this->status === StatusUjian::SELESAI) {
            abort(403, 'UJIAN SUDAH SELESAI / TERKUNCI');
        }
    }

    /* ================= SECTION FLOW ================= */

   public function sectionAktif()
{
    return $this->ujian
        ->sections()
        ->orderBy('urutan')
        ->skip($this->current_section_index)
        ->first();
}

    public function masihAdaSection(): bool
{
    $totalSection = $this->ujian->sections->count();

    return ($this->current_section_index + 1) < $totalSection;
}


    public function lanjutKeSectionBerikutnya(): void
{
    $this->increment('current_section_index');
    $this->update(['section_mulai_at' => now()]);
}


   public function semuaSectionSelesai(): bool
{
    return $this->current_section_index >= $this->ujian->sections()->count();
}

    

    /* ================= TIMER ================= */

    public function sectionEndTime(): Carbon
    {
        return $this->section_mulai_at
            ->copy()
            ->addMinutes($this->sectionAktif()->durasi_menit);
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

    $sisa = now()->diffInSeconds(
        $this->sectionEndTime(),
        false
    );

    return max(0, (int) floor($sisa));
}




    public function pastikanBisaDiaksesOleh(int $siswaId): void
    {
        if ((int) $this->siswa_id !== (int) $siswaId) {
            throw new AccessDeniedHttpException('Sesi ujian bukan milik siswa ini');
        }
    }

}
