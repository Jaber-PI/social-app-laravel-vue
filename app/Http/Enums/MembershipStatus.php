<?php

namespace App\Enums;

enum MembershipStatus: string
{
    // case Active = 'active';
    case Pending = 'pending';
    // case Banned = 'banned';
    // case Invited = 'invited';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
