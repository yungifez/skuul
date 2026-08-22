<?php

namespace App\Enums;

/**
 * The states of an academic period.
 *
 * A period is the reporting boundary for placements, timetables, and results.
 * Closing it freezes those records; reopening it needs permission and leaves
 * an audit record.
 *
 * Finance is deliberately outside this lifecycle. Closing a period must never
 * close an invoice, a payment, or a ledger transaction: a school still collects
 * last term's fees after the term ends.
 */
enum AcademicPeriodStatus: string
{
    /**
     * Staff are still configuring it. Students and routine operations skip it.
     */
    case Draft = 'draft';

    /**
     * The dates are agreed but the period has not started.
     */
    case Scheduled = 'scheduled';

    /**
     * The period is in use. Records can be created and changed.
     */
    case Open = 'open';

    /**
     * Teaching has finished. New work is restricted while staff finish the
     * checks that a close requires.
     */
    case Closing = 'closing';

    /**
     * The period is finished. Its records are read-only.
     */
    case Closed = 'closed';

    /**
     * Kept for history only. It never accepts work again.
     */
    case Archived = 'archived';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Draft',
            self::Scheduled => 'Scheduled',
            self::Open      => 'Open',
            self::Closing   => 'Closing',
            self::Closed    => 'Closed',
            self::Archived  => 'Archived',
        };
    }

    /**
     * Check if records of this period can still be written.
     *
     * A closing period accepts corrections but not new work. Ask
     * `acceptsNewWork()` for that difference.
     */
    public function acceptsWrites(): bool
    {
        return $this === self::Open || $this === self::Closing;
    }

    /**
     * Check if the period accepts work that did not exist before.
     *
     * Closing a period is the window where staff finish what is already
     * started. Nothing new begins in it.
     */
    public function acceptsNewWork(): bool
    {
        return $this === self::Open;
    }

    /**
     * Check if routine operations run against this period.
     *
     * Attendance, timetables, and grading read this before they touch a
     * period a person selected.
     */
    public function isOperational(): bool
    {
        return $this === self::Open || $this === self::Closing;
    }

    /**
     * Check if the period is read-only.
     */
    public function isFrozen(): bool
    {
        return $this === self::Closed || $this === self::Archived;
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            // A draft can be dated, or opened straight away when staff are
            // setting up a period that is already running.
            self::Draft     => [self::Scheduled, self::Open, self::Closed],
            self::Scheduled => [self::Open, self::Draft, self::Closed],
            self::Open      => [self::Closing, self::Closed],
            self::Closing   => [self::Closed, self::Open],
            self::Closed    => [self::Open, self::Archived],

            // Archived is the end. Restoring one is a deliberate reopen of the
            // close before it, not a state change from here.
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

    /**
     * Check if moving to the given state needs a stated reason.
     *
     * Undoing a close is the change a school has to be able to explain later.
     */
    public function requiresReasonToReach(self $status): bool
    {
        return $this->isFrozen() && !$status->isFrozen();
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
