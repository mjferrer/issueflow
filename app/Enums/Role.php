<?php

namespace App\Enums;

enum Role: string
{
    case Admin     = 'admin';
    case Moderator = 'moderator';
    case User      = 'user';

    public function label(): string
    {
        return match($this) {
            self::Admin     => 'Administrator',
            self::Moderator => 'Moderator',
            self::User      => 'User',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Admin     => 'text-red-400 bg-red-400/10',
            self::Moderator => 'text-violet-400 bg-violet-400/10',
            self::User      => 'text-slate-400 bg-slate-400/10',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}