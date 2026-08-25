<?php

namespace App\Enums;

/**
 * Where one person stands in the queue for a title.
 */
enum LibraryReservationStatus: string
{
    /**
     * In the queue. Every copy is out.
     */
    case Waiting = 'waiting';

    /**
     * A copy is being kept behind the desk for this person.
     */
    case Ready = 'ready';

    /**
     * They came and took it.
     */
    case Collected = 'collected';

    /**
     * They said they no longer want it, or the library took it off.
     */
    case Cancelled = 'cancelled';

    /**
     * They did not come in time, so the copy went to the next person.
     */
    case Expired = 'expired';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'Waiting',
            self::Ready => 'Ready to collect',
            self::Collected => 'Collected',
            self::Cancelled => 'Cancelled',
            self::Expired => 'Not collected',
        };
    }

    /**
     * Check whether this reservation is still going.
     */
    public function isOpen(): bool
    {
        return $this === self::Waiting || $this === self::Ready;
    }
}
