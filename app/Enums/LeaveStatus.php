<?php

namespace App\Enums;

/**
 * How far along a leave request is.
 */
enum LeaveStatus: string
{
    /**
     * Somebody asked. Nobody has answered yet.
     */
    case Requested = 'requested';

    /**
     * The school agreed.
     */
    case Approved = 'approved';

    /**
     * The school said no.
     */
    case Declined = 'declined';

    /**
     * The request was withdrawn.
     */
    case Cancelled = 'cancelled';

    /**
     * The leave happened.
     */
    case Taken = 'taken';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Requested',
            self::Approved  => 'Approved',
            self::Declined  => 'Declined',
            self::Cancelled => 'Cancelled',
            self::Taken     => 'Taken',
        };
    }

    /**
     * Check if the request still keeps the days blocked.
     */
    public function holdsTheDays(): bool
    {
        return in_array($this, [self::Requested, self::Approved, self::Taken], true);
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Requested => [self::Approved, self::Declined, self::Cancelled],
            self::Approved  => [self::Taken, self::Cancelled],
            self::Declined  => [self::Requested],
            self::Cancelled => [],
            self::Taken     => [],
        };
    }

    /**
     * Check if this state can move to the given state.
     */
    public function canMoveTo(self $status): bool
    {
        return in_array($status, $this->allowedNext(), true);
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
