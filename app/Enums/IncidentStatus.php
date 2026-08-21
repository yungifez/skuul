<?php

namespace App\Enums;

/**
 * How far along a case is.
 */
enum IncidentStatus: string
{
    /**
     * Somebody wrote it down. Nobody has looked at it yet.
     */
    case Reported = 'reported';

    /**
     * Somebody is looking into it.
     */
    case UnderReview = 'under_review';

    /**
     * It was passed to somebody outside the school.
     */
    case Referred = 'referred';

    /**
     * The school did something about it and is watching the result.
     */
    case ActionTaken = 'action_taken';

    /**
     * Nothing more is needed.
     */
    case Resolved = 'resolved';

    /**
     * The case is finished and read-only.
     */
    case Closed = 'closed';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Reported => 'Reported',
            self::UnderReview => 'Under review',
            self::Referred => 'Referred',
            self::ActionTaken => 'Action taken',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
        };
    }

    /**
     * Check if the case still accepts work.
     */
    public function isOpen(): bool
    {
        return !in_array($this, [self::Resolved, self::Closed], true);
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Reported => [self::UnderReview, self::Referred, self::ActionTaken, self::Resolved, self::Closed],
            self::UnderReview => [self::Referred, self::ActionTaken, self::Resolved, self::Closed],
            self::Referred => [self::UnderReview, self::ActionTaken, self::Resolved, self::Closed],
            self::ActionTaken => [self::UnderReview, self::Resolved, self::Closed],
            self::Resolved => [self::Closed, self::UnderReview],
            self::Closed => [],
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
