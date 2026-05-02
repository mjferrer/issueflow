<?php

namespace App\Enums;

enum Category: string
{
    case Bug           = 'bug';
    case Feature       = 'feature';
    case Security      = 'security';
    case Performance   = 'performance';
    case Infrastructure = 'infrastructure';
    case DataIssue     = 'data_issue';
    case UX            = 'ux';
    case Other         = 'other';

    public function label(): string
    {
        return match($this) {
            self::Bug            => 'Bug',
            self::Feature        => 'Feature Request',
            self::Security       => 'Security',
            self::Performance    => 'Performance',
            self::Infrastructure => 'Infrastructure',
            self::DataIssue      => 'Data Issue',
            self::UX             => 'UX / Design',
            self::Other          => 'Other',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Bug            => '🐛',
            self::Feature        => '✨',
            self::Security       => '🔒',
            self::Performance    => '⚡',
            self::Infrastructure => '🏗️',
            self::DataIssue      => '📊',
            self::UX             => '🎨',
            self::Other          => '📋',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}