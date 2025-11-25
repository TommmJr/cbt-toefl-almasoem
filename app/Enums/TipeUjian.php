<?php

declare(strict_types=1);

namespace App\Enums;

enum TipeUjian: string
{
    case PRACTICE = 'practice';
    case EXAM = 'exam';

    /**
     * Ambil semua nilai enum
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Label untuk display
     */
    public function label(): string
    {
        return match($this) {
            self::PRACTICE => 'Latihan',
            self::EXAM => 'Ujian Resmi',
        };
    }

    /**
     * Icon untuk UI
     */
    public function icon(): string
    {
        return match($this) {
            self::PRACTICE => 'clipboard',
            self::EXAM => 'file-text',
        };
    }

    /**
     * Cek apakah tipe ujian adalah practice
     */
    public function isPractice(): bool
    {
        return $this === self::PRACTICE;
    }

    /**
     * Cek apakah tipe ujian adalah exam resmi
     */
    public function isExam(): bool
    {
        return $this === self::EXAM;
    }
}