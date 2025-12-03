<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel
     */
    protected $table = 'guru';

    /**
     * 
     */
    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'mata_pelajaran',
        'no_telepon',
        'foto_profil',
    ];

    /**
     * Relasi ke tabel users (many:1)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke ujian yang dibuat (1:many)
     */
    public function ujian(): HasMany
    {
        return $this->hasMany(Ujian::class);
    }

    /**
     * Scope: Search guru berdasarkan nama atau NIP
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('nama_lengkap', 'LIKE', "%{$keyword}%")
              ->orWhere('nip', 'LIKE', "%{$keyword}%");
        });
    }

    /**
     * : Ambil URL foto profil
     */
    public function getFotoProfilUrlAttribute(): string
    {
        return $this->foto_profil 
            ? asset('storage/' . $this->foto_profil)
            : asset('images/default-avatar.png');
    }

    /**
     * : Hitung total ujian yang dibuat
     */
    public function getTotalUjianAttribute(): int
    {
        return $this->ujian()->count();
    }
}