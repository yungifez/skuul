<?php

namespace App\Enums;

/**
 * What staff found when they checked one boarder.
 */
enum BoardingRollEntryStatus: string
{
    case Present = 'present';
    case Late = 'late';
    case Away = 'away';
    case Excused = 'excused';
    case Unaccounted = 'unaccounted';
    case NotRecorded = 'not_recorded';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Present => 'Present',
            self::Late => 'Late',
            self::Away => 'Away',
            self::Excused => 'Excused',
            self::Unaccounted => 'Unaccounted',
            self::NotRecorded => 'Not recorded',
        };
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $status): string => $status->value, self::cases());
    }
}
