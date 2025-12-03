<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel 
     */
    protected $table = 'siswa';

    /**
     *
     */
    protected $fillable = [
        'user_id',
        'nis',
        'nama_lengkap',
        'kelas',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'no_telepon',
        'foto_profil',
    ];

    /**
     * Cast attributes
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    /**
     * Relasi ke tabel users (many:1)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke sesi ujian yang pernah diikuti (1:many)
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
     * Scope: Filter berdasarkan kelas
     */
    public function scopeByKelas($query, string $kelas)
    {
        return $query->where('kelas', $kelas);
    }

    /**
     * Scope: Search siswa berdasarkan nama atau NIS
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('nama_lengkap', 'LIKE', "%{$keyword}%")
              ->orWhere('nis', 'LIKE', "%{$keyword}%");
        });
    }

    /**
     * : Ambil nama kelas tanpa spasi (untuk grouping)
     */
    public function getKelasSlugAttribute(): string
    {
        return str_replace(' ', '-', strtolower($this->kelas));
    }

    /**
     * : Ambil URL foto profil (dengan fallback ke default)
     */
    public function getFotoProfilUrlAttribute(): string
    {
        return $this->foto_profil 
            ? asset('storage/' . $this->foto_profil)
            : asset('images/default-avatar.png');
    }

    /**
     * : Hitung rata-rata nilai TOEFL siswa
     */
    public function getRataRataNilaiAttribute(): float
    {
        return $this->nilai()
            ->avg('skor_total') ?? 0;
    }
}