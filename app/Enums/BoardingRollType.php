<?php

namespace App\Enums;

/**
 * The regular checks a boarding house takes during a day.
 */
enum BoardingRollType: string
{
    case Morning = 'morning';
    case Evening = 'evening';
    case Curfew = 'curfew';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Morning => 'Morning roll',
            self::Evening => 'Evening roll',
            self::Curfew => 'Curfew roll',
        };
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $type): string => $type->value, self::cases());
    }
}
