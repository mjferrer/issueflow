<?php

namespace App\Enums;

enum Priority: string
{
    case Low      = 'low';
    case Medium   = 'medium';
    case High     = 'high';
    case Critical = 'critical';

    public function label(): string
    {
        return match($this) {
            self::Low      => 'Low',
            self::Medium   => 'Medium',
            self::High     => 'High',
            self::Critical => 'Critical',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Low      => 'text-emerald-400 bg-emerald-400/10 ring-emerald-400/20',
            self::Medium   => 'text-amber-400 bg-amber-400/10 ring-amber-400/20',
            self::High     => 'text-orange-400 bg-orange-400/10 ring-orange-400/20',
            self::Critical => 'text-red-400 bg-red-400/10 ring-red-400/20',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Low      => 'badge-low',
            self::Medium   => 'badge-medium',
            self::High     => 'badge-high',
            self::Critical => 'badge-critical',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isEscalatable(): bool
    {
        return in_array($this, [self::High, self::Critical]);
    }
}