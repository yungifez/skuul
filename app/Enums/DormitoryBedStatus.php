<?php

namespace App\Enums;

/**
 * The operational state of one boarding bed.
 *
 * Occupied is derived from the current boarding place. It is not stored here.
 */
enum DormitoryBedStatus: string
{
    case Available = 'available';

    case Maintenance = 'maintenance';

    case Unavailable = 'unavailable';

    case Retired = 'retired';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Maintenance => 'Maintenance',
            self::Unavailable => 'Unavailable',
            self::Retired => 'Retired',
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

    /**
     * Check whether a bed may receive a learner.
     */
    public function isAssignable(): bool
    {
        return $this === self::Available;
    }
}
