<?php

declare(strict_types=1);

namespace App\Enums;

enum TipeSoal: string
{
    case LISTENING = 'listening';
    case READING = 'reading';
    case WRITING = 'writing';

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
     * Cek apakah soal butuh AI grading
     */
    public function needsAIGrading(): bool
    {
        return $this === self::WRITING;
    }

    /**
     * Cek apakah soal punya audio
     */
    public function hasAudio(): bool
    {
        return $this === self::LISTENING;
    }
}