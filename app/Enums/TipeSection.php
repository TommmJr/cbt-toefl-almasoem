<?php

namespace App\Enums;

enum TipeSection: string
{
    case LISTENING = 'listening';
    case STRUCTURE = 'structure';
    case READING   = 'reading';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
