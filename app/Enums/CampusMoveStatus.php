<?php

namespace App\Enums;

/**
 * How far along a request to move a student to another campus is.
 *
 * A person with organization authority moves a student without a request, so
 * these states only describe a move one campus asked another campus for.
 */
enum CampusMoveStatus: string
{
    /**
     * One campus asked to move the student to another campus.
     */
    case Requested = 'requested';

    /**
     * The move was agreed, and it happened.
     */
    case Approved = 'approved';

    /**
     * The receiving campus said no.
     */
    case Rejected = 'rejected';

    /**
     * The campus that asked took the request back.
     */
    case Cancelled = 'cancelled';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Waiting for a decision',
            self::Approved  => 'Approved',
            self::Rejected  => 'Rejected',
            self::Cancelled => 'Taken back',
        };
    }

    /**
     * Check if the request is still waiting for somebody to decide.
     */
    public function isOpen(): bool
    {
        return $this === self::Requested;
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Requested => [self::Approved, self::Rejected, self::Cancelled],
            self::Approved, self::Rejected, self::Cancelled => [],
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
