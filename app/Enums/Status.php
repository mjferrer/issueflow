<?php

namespace App\Enums;

enum Status: string
{
    case Open        = 'open';
    case InProgress  = 'in_progress';
    case OnHold      = 'on_hold';
    case Resolved    = 'resolved';
    case Closed      = 'closed';

    public function label(): string
    {
        return match($this) {
            self::Open       => 'Open',
            self::InProgress => 'In Progress',
            self::OnHold     => 'On Hold',
            self::Resolved   => 'Resolved',
            self::Closed     => 'Closed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Open       => 'text-blue-400 bg-blue-400/10 ring-blue-400/20',
            self::InProgress => 'text-violet-400 bg-violet-400/10 ring-violet-400/20',
            self::OnHold     => 'text-yellow-400 bg-yellow-400/10 ring-yellow-400/20',
            self::Resolved   => 'text-emerald-400 bg-emerald-400/10 ring-emerald-400/20',
            self::Closed     => 'text-slate-400 bg-slate-400/10 ring-slate-400/20',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this, [self::Open, self::InProgress, self::OnHold]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}