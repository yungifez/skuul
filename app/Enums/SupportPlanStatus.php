<?php

namespace App\Enums;

/**
 * How far along a support plan is.
 */
enum SupportPlanStatus: string
{
    /**
     * Somebody is still writing it.
     */
    case Draft = 'draft';

    /**
     * The school is doing it.
     */
    case Active = 'active';

    /**
     * The plan is paused, but not finished.
     */
    case OnHold = 'on_hold';

    /**
     * The child no longer needs it.
     */
    case Completed = 'completed';

    /**
     * The plan was dropped without being finished.
     */
    case Cancelled = 'cancelled';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Draft',
            self::Active    => 'Active',
            self::OnHold    => 'On hold',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Check if the plan still accepts work.
     */
    public function isOpen(): bool
    {
        return !in_array($this, [self::Completed, self::Cancelled], true);
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Draft     => [self::Active, self::Cancelled],
            self::Active    => [self::OnHold, self::Completed, self::Cancelled],
            self::OnHold    => [self::Active, self::Completed, self::Cancelled],
            self::Completed => [self::Active],
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
