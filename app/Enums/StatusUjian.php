<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusUjian: string
{
    case BELUM_MULAI        = 'belum_mulai';
    case SEDANG_MENGERJAKAN = 'sedang_mengerjakan';
    case SELESAI            = 'selesai';
    case DISKUALIFIKASI     = 'diskualifikasi';

    /**
     * Semua value enum (buat validasi / form / migration)
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Label manusiawi (buat Blade / UI)
     */
    public function label(): string
    {
        return match ($this) {
            self::BELUM_MULAI        => 'Belum Mulai',
            self::SEDANG_MENGERJAKAN => 'Sedang Mengerjakan',
            self::SELESAI            => 'Selesai',
            self::DISKUALIFIKASI     => 'Diskualifikasi',
        };
    }

    /**
     * Warna badge (Tailwind-ready)
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::BELUM_MULAI        => 'bg-gray-100 text-gray-800',
            self::SEDANG_MENGERJAKAN => 'bg-blue-100 text-blue-800',
            self::SELESAI            => 'bg-green-100 text-green-800',
            self::DISKUALIFIKASI     => 'bg-red-100 text-red-800',
        };
    }

    /**
     * Apakah siswa boleh lanjut mengerjakan
     */
    public function canContinue(): bool
    {
        return $this === self::SEDANG_MENGERJAKAN;
    }

    /**
     * Apakah status final (tidak bisa diubah)
     */
    public function isFinished(): bool
    {
        return in_array($this, [
            self::SELESAI,
            self::DISKUALIFIKASI,
        ], true);
    }
}
