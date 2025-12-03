<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RolePengguna;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * 
     */
     protected $fillable = [
        'username', 
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];


    /**
     * Hidden attributes (tidak masuk JSON response)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast attributes ke tipe data tertentu
     */
    protected function casts(): array
    {
        return [
            'role' => RolePengguna::class, // Pastikan file Enum ada!
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke tabel siswa (1:1)
     */
    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }

    /**
     * Relasi ke tabel guru (1:1)
     */
    public function guru(): HasOne
    {
        return $this->hasOne(Guru::class);
    }

    /**
     * Scope: Filter user yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter berdasarkan role
     */
    public function scopeByRole($query, RolePengguna $role)
    {
        return $query->where('role', $role->value);
    }

    /**
     * : Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === RolePengguna::ADMIN;
    }

    /**
     * : Cek apakah user adalah guru
     */
    public function isGuru(): bool
    {
        return $this->role === RolePengguna::GURU;
    }

    /**
     * : Cek apakah user adalah siswa
     */
    public function isSiswa(): bool
    {
        return $this->role === RolePengguna::SISWA;
    }

    /**
     * : Update waktu login terakhir
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}