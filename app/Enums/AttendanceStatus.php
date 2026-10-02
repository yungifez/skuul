<?php

namespace App\Enums;

/**
 * What a student's attendance says for one day or one lesson.
 *
 * "Not recorded" is a real answer. It means nobody took the register, which
 * is different from a student being absent.
 */
enum AttendanceStatus: string
{
    /**
     * The student was there.
     */
    case Present = 'present';

    /**
     * The student was not there, and nobody explained why.
     */
    case Absent = 'absent';

    /**
     * The student arrived after the start.
     */
    case Late = 'late';

    /**
     * The student was away with permission.
     */
    case Excused = 'excused';

    /**
     * The student went home before the end.
     */
    case LeftEarly = 'left_early';

    /**
     * The student took part from somewhere else.
     */
    case Remote = 'remote';

    /**
     * The student was out on school business.
     */
    case SchoolActivity = 'school_activity';

    /**
     * Nobody took the register.
     */
    case NotRecorded = 'not_recorded';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Present => 'Present',
            self::Absent => 'Absent',
            self::Late => 'Late',
            self::Excused => 'Excused',
            self::LeftEarly => 'Left early',
            self::Remote => 'Remote',
            self::SchoolActivity => 'School activity',
            self::NotRecorded => 'Not recorded',
        };
    }

    /**
     * Check if the student counts as attending school that day.
     */
    public function countsAsPresent(): bool
    {
        return in_array($this, [self::Present, self::Late, self::Remote, self::SchoolActivity, self::LeftEarly], true);
    }

    /**
     * Check if the day counts in an attendance rate at all.
     */
    public function countsInRate(): bool
    {
        return $this !== self::NotRecorded;
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
