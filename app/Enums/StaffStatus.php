<?php

namespace App\Enums;

/**
 * Whether a member of staff is at work.
 */
enum StaffStatus: string
{
    /**
     * At work.
     */
    case Active = 'active';

    /**
     * Away, but still employed.
     */
    case OnLeave = 'on_leave';

    /**
     * Held away from work while something is looked into.
     */
    case Suspended = 'suspended';

    /**
     * No longer employed.
     */
    case Left = 'left';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::OnLeave => 'On leave',
            self::Suspended => 'Suspended',
            self::Left => 'Left',
        };
    }

    /**
     * Check if the person may be given work.
     */
    public function canBeGivenWork(): bool
    {
        return $this === self::Active;
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
