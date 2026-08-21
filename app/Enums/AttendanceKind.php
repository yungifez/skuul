<?php

namespace App\Enums;

/**
 * Which register a record belongs to.
 *
 * A school may take the register once a day, once a lesson, or both. The two
 * are separate records so one never overwrites the other.
 */
enum AttendanceKind: string
{
    /**
     * One record for the whole school day.
     */
    case Daily = 'daily';

    /**
     * One record for a lesson or a section period.
     */
    case Period = 'period';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Daily => 'Daily register',
            self::Period => 'Lesson register',
        };
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $kind): string => $kind->value, self::cases());
    }
}
