<?php

namespace App\Enums;

/**
 * What happened with one student on one grade item.
 *
 * An empty box must never quietly mean zero. Every entry says which of these
 * it is, so a report can tell missing work from excused work.
 */
enum GradeEntryState: string
{
    /**
     * The work was marked.
     */
    case Graded = 'graded';

    /**
     * The work was never handed in.
     */
    case Missing = 'missing';

    /**
     * The student was away.
     */
    case Absent = 'absent';

    /**
     * The student does not have to do this item.
     */
    case Exempt = 'exempt';

    /**
     * The work started but is not finished.
     */
    case Incomplete = 'incomplete';

    /**
     * The item does not apply to this student.
     */
    case NotApplicable = 'not_applicable';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Graded        => 'Graded',
            self::Missing       => 'Missing',
            self::Absent        => 'Absent',
            self::Exempt        => 'Exempt',
            self::Incomplete    => 'Incomplete',
            self::NotApplicable => 'Not applicable',
        };
    }

    /**
     * Check if the entry counts in the total at all.
     *
     * Excused work leaves the total alone. It does not become a zero.
     */
    public function countsInTotal(): bool
    {
        return !in_array($this, [self::Exempt, self::NotApplicable], true);
    }

    /**
     * Check if the entry needs a mark.
     */
    public function needsPoints(): bool
    {
        return $this === self::Graded;
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $state): string => $state->value, self::cases());
    }
}
