<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Member = 'member';
    case Guest = 'guest';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
