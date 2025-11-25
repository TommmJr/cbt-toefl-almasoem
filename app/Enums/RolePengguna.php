<?php

declare(strict_types=1);

namespace App\Enums;

enum RolePengguna: string
{
    case ADMIN = 'admin';
    case GURU = 'guru';
    case SISWA = 'siswa';

    /**
     * Ambil semua nilai enum sebagai array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Ambil label yang lebih readable
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::GURU => 'Guru',
            self::SISWA => 'Siswa',
        };
    }

    /**
     * Cek apakah role adalah admin
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Cek apakah role adalah guru
     */
    public function isGuru(): bool
    {
        return $this === self::GURU;
    }

    /**
     * Cek apakah role adalah siswa
     */
    public function isSiswa(): bool
    {
        return $this === self::SISWA;
    }
}