<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipeUjian;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Ujian extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel
     */
    protected $table = 'ujian';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'guru_id',
        'kode_ujian',
        'judul',
        'deskripsi',
        'tipe_ujian',
        'durasi_menit',
        'waktu_mulai',
        'waktu_selesai',
        'target_kelas',
        'passing_score',
        'is_published',
        'tab_lock_enabled',
        'show_result_immediately',
    ];

    /**
     * Cast attributes
     */
    protected function casts(): array
    {
        return [
            'tipe_ujian' => TipeUjian::class,
            'durasi_menit' => 'integer',
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
            'passing_score' => 'integer',
            'is_published' => 'boolean',
            'tab_lock_enabled' => 'boolean',
            'show_result_immediately' => 'boolean',
        ];
    }

    /**
     * Relasi ke guru pembuat ujian (many:1)
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    /**
     * Relasi ke sesi ujian (1:many)
     */
    public function sesiUjian(): HasMany
    {
        return $this->hasMany(SesiUjian::class);
    }

    /**
     * Relasi ke nilai (1:many)
     */
    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Scope: Filter ujian yang sudah published
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope: Filter ujian yang sedang berlangsung
     */
    public function scopeAktif($query)
    {
        return $query->where('waktu_mulai', '<=', now())
                     ->where('waktu_selesai', '>=', now())
                     ->where('is_published', true);
    }

    /**
     * Scope: Filter berdasarkan tipe ujian
     */
    public function scopeByTipe($query, TipeUjian $tipe)
    {
        return $query->where('tipe_ujian', $tipe->value);
    }

    /**
     * : Cek apakah ujian sedang berlangsung
     */
    public function isAktif(): bool
    {
        return $this->is_published 
            && now()->between($this->waktu_mulai, $this->waktu_selesai);
    }

    /**
     * : Cek apakah ujian sudah selesai
     */
    public function isSudahSelesai(): bool
    {
        return now()->greaterThan($this->waktu_selesai);
    }

    /**
     * : Cek apakah ujian belum dimulai
     */
    public function isBelumMulai(): bool
    {
        return now()->lessThan($this->waktu_mulai);
    }

    /**
     * : Hitung jumlah peserta ujian
     */
    public function getTotalPesertaAttribute(): int
    {
        return $this->sesiUjian()->count();
    }

    /**
     * : Hitung jumlah peserta yang sudah selesai
     */
    public function getTotalSelesaiAttribute(): int
    {
        return $this->sesiUjian()
            ->whereIn('status', ['selesai', 'diskualifikasi'])
            ->count();
    }

    /**
     * : Parse target kelas (jika pakai comma separated)
     */
    public function getTargetKelasArrayAttribute(): array
    {
        if (empty($this->target_kelas)) {
            return [];
        }
        
        return explode(',', $this->target_kelas);
    }

    /**
 * Relasi ke ujian sections (1:many)
 */
public function sections(): HasMany
{
    return $this->hasMany(UjianSection::class)->orderBy('urutan');
}

/**
 * Relasi ke token ujian (1:many)
 */
public function tokens(): HasMany
{
    return $this->hasMany(TokenUjian::class);
}

/**
 * Helper: Generate token untuk ujian ini
 */
public function generateToken(User $pembuatToken, int $kuota = 3000): TokenUjian
{
    return TokenUjian::create([
        'ujian_id' => $this->id,
        'kode_token' => TokenUjian::generateKodeToken(),
        'kuota_pemakaian' => $kuota,
        'berlaku_dari' => $this->waktu_mulai->subHour(), // 1 jam sebelum ujian
        'berlaku_sampai' => $this->waktu_selesai,
        'dibuat_oleh' => $pembuatToken->id,
        'ip_address' => request()->ip(),
    ]);
}
}