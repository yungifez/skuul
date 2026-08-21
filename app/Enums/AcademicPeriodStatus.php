<?php

namespace App\Enums;

/**
 * The states of an academic period.
 *
 * A period is the reporting boundary for placements, timetables, and results.
 * Closing it freezes those records; reopening it needs permission and leaves
 * an audit record.
 */
enum AcademicPeriodStatus: string
{
    /**
     * The period is being prepared and is not in use yet.
     */
    case Draft = 'draft';

    /**
     * The period is in use. Records can be created and changed.
     */
    case Open = 'open';

    /**
     * The period is finished. Its records are read-only.
     */
    case Closed = 'closed';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft  => 'Draft',
            self::Open   => 'Open',
            self::Closed => 'Closed',
        };
    }

    /**
     * Check if records of this period can still be written.
     */
    public function acceptsWrites(): bool
    {
        return $this === self::Open;
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Draft  => [self::Open, self::Closed],
            self::Open   => [self::Closed],
            self::Closed => [self::Open],
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
