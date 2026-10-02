<?php

namespace App\Enums;

/**
 * How far along a request to share records is.
 */
enum DataSharingStatus: string
{
    /**
     * One school asked another.
     */
    case Requested = 'requested';

    /**
     * The holding school agreed.
     */
    case Approved = 'approved';

    /**
     * The holding school said no.
     */
    case Declined = 'declined';

    /**
     * The records were handed over.
     */
    case Fulfilled = 'fulfilled';

    /**
     * The permission ran out before it was used.
     */
    case Expired = 'expired';

    /**
     * The holding school took the permission back.
     */
    case Revoked = 'revoked';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Asked for',
            self::Approved => 'Approved',
            self::Declined => 'Declined',
            self::Fulfilled => 'Handed over',
            self::Expired => 'Expired',
            self::Revoked => 'Taken back',
        };
    }

    /**
     * Check if the records may still be handed over.
     */
    public function allowsFulfilment(): bool
    {
        return $this === self::Approved;
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Requested => [self::Approved, self::Declined, self::Expired, self::Revoked],
            self::Approved => [self::Fulfilled, self::Expired, self::Revoked],
            self::Fulfilled => [self::Revoked],
            self::Declined, self::Expired, self::Revoked => [],
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
