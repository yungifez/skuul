<?php

namespace App\Enums;

/**
 * Where an overnight leave request has got to.
 *
 * The states answer one question staff ask every evening: who is not in the
 * building tonight, and does somebody know about it?
 */
enum OvernightLeaveStatus: string
{
    case Requested = 'requested';

    case Approved = 'approved';

    case Refused = 'refused';

    case Cancelled = 'cancelled';

    case Returned = 'returned';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Waiting for a decision',
            self::Approved => 'Approved',
            self::Refused => 'Refused',
            self::Cancelled => 'Cancelled',
            self::Returned => 'Back in the house',
        };
    }

    /**
     * Check whether the request is still waiting for somebody.
     */
    public function isOpen(): bool
    {
        return $this === self::Requested;
    }

    /**
     * Check whether the learner may be out of the house on this request.
     */
    public function allowsTheLearnerOut(): bool
    {
        return $this === self::Approved;
    }

    /**
     * Check whether nothing more will happen to the request.
     */
    public function isFinished(): bool
    {
        return in_array($this, [self::Refused, self::Cancelled, self::Returned], true);
    }

    /**
     * Get the states this one may move to.
     *
     * @return array<int, self>
     */
    public function nextStates(): array
    {
        return match ($this) {
            self::Requested => [self::Approved, self::Refused, self::Cancelled],
            self::Approved => [self::Returned, self::Cancelled],
            self::Refused, self::Cancelled, self::Returned => [],
        };
    }

    /**
     * Check whether the request may move to the given state.
     */
    public function canMoveTo(self $next): bool
    {
        return in_array($next, $this->nextStates(), true);
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
