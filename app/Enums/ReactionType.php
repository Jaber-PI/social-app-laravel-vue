<?php

namespace App\Enums;

enum ReactionType: string
{
    case Like = 'like';
    case Dislike = 'dislike';
    case Sad = 'sad';
    case Laugh = 'laugh';

    public function label(): string
    {
        return match($this) {
            self::Like => '❤️ Like',
            self::Dislike => '👎 Dislike',
            self::Sad => '😢 Sad',
            self::Laugh => '😂 Laugh',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
