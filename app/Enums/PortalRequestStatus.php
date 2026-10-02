<?php

namespace App\Enums;

/**
 * How far along a family's request is.
 */
enum PortalRequestStatus: string
{
    /**
     * The family sent it. Nobody has read it yet.
     */
    case Submitted = 'submitted';

    /**
     * Somebody at the school is dealing with it.
     */
    case InReview = 'in_review';

    /**
     * The school answered it.
     */
    case Answered = 'answered';

    /**
     * The school said no.
     */
    case Declined = 'declined';

    /**
     * The family withdrew it.
     */
    case Cancelled = 'cancelled';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Sent',
            self::InReview => 'Being looked at',
            self::Answered => 'Answered',
            self::Declined => 'Declined',
            self::Cancelled => 'Withdrawn',
        };
    }

    /**
     * Check if the request is still open.
     */
    public function isOpen(): bool
    {
        return in_array($this, [self::Submitted, self::InReview], true);
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Submitted => [self::InReview, self::Answered, self::Declined, self::Cancelled],
            self::InReview => [self::Answered, self::Declined, self::Cancelled],
            self::Answered => [],
            self::Declined => [],
            self::Cancelled => [],
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
