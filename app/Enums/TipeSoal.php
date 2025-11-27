<?php

declare(strict_types=1);

namespace App\Enums;

enum TipeSoal: string
{
    case LISTENING = 'listening';
    case STRUCTURE = 'structure';
    case READING = 'reading';

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
            self::LISTENING => 'Listening',
            self::READING => 'Reading',
            self::WRITING => 'Writing',
        };
    }

    /**
     * Icon untuk UI (sesuai desain proposal)
     */
    public function icon(): string
    {
        return match($this) {
            self::LISTENING => 'headphones',
            self::READING => 'book-open',
            self::WRITING => 'pen-tool',
        };
    }

    /**
     * Ambil config section dari config/toefl.php
     */
    public function config(): array
    {
        return config("toefl.sections.{$this->value}", []);
    }

    /**
     * Ambil durasi section dalam menit
     */
    public function durasi(): int
    {
        return $this->config()['durasi_menit'] ?? 0;
    }

    /**
     * Ambil jumlah soal section
     */
    public function jumlahSoal(): int
    {
        return $this->config()['jumlah_soal'] ?? 0;
    }

    /**
     * Ambil bobot nilai section
     */
    public function bobotNilai(): float
    {
        return $this->config()['bobot_nilai'] ?? 1.0;
    }

    /**
     * Cek apakah soal punya audio
     */
    public function hasAudio(): bool
    {
        return $this === self::LISTENING;
    }
}