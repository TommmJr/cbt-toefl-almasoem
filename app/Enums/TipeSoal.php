<?php

declare(strict_types=1);

namespace App\Enums;

enum TipeSoal: string
{
    case LISTENING = 'listening';
    case WRITING   = 'writing';
    case READING   = 'reading';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::LISTENING => 'Listening',
            self::WRITING   => 'Writing',
            self::READING   => 'Reading',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::LISTENING => 'headphones',
            self::WRITING   => 'pen-tool',
            self::READING   => 'book-open',
        };
    }

    public function config(): array
    {
        return config("toefl.sections.{$this->value}", []);
    }

    public function durasi(): int
    {
        return $this->config()['durasi_menit'] ?? 0;
    }

    public function jumlahSoal(): int
    {
        return $this->config()['jumlah_soal'] ?? 0;
    }

    public function bobotNilai(): float
    {
        return $this->config()['bobot_nilai'] ?? 1.0;
    }

    public function hasAudio(): bool
    {
        return $this === self::LISTENING;
    }
}
