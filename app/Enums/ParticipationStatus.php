<?php

namespace App\Enums;

/**
 * How far along a person is in a programme.
 */
enum ParticipationStatus: string
{
    /**
     * Somebody asked for a place.
     */
    case Requested = 'requested';

    /**
     * The person is taking part.
     */
    case Active = 'active';

    /**
     * The person finished it.
     */
    case Completed = 'completed';

    /**
     * The person stopped before the end.
     */
    case Withdrawn = 'withdrawn';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Requested',
            self::Active    => 'Taking part',
            self::Completed => 'Completed',
            self::Withdrawn => 'Withdrawn',
        };
    }

    /**
     * Check if the person is still in the programme.
     */
    public function isRunning(): bool
    {
        return in_array($this, [self::Requested, self::Active], true);
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Requested => [self::Active, self::Withdrawn],
            self::Active    => [self::Completed, self::Withdrawn],
            self::Completed => [],
            self::Withdrawn => [self::Active],
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
