<?php

namespace App\Enums;

/**
 * The states of a timetable.
 *
 * A published timetable is what the school teaches, so it stops changing.
 * Changing it means publishing the next revision, which keeps the schedule
 * people already read.
 */
enum TimetableStatus: string
{
    /**
     * The timetable is being prepared. It can still change.
     */
    case Draft = 'draft';

    /**
     * The timetable is in use. Its entries are read-only.
     */
    case Published = 'published';

    /**
     * A later revision replaced this timetable. It stays readable.
     */
    case Archived = 'archived';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    /**
     * Check if the entries of the timetable can still be changed.
     */
    public function acceptsChanges(): bool
    {
        return $this === self::Draft;
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Draft => [self::Published, self::Archived],
            self::Published => [self::Archived],
            self::Archived => [],
        };
    }

    /**
     * Check if this state can move to the given state.
     */
    public function canMoveTo(self $status): bool
    {
        return in_array($status, $this->allowedNext(), true);
    }
}
