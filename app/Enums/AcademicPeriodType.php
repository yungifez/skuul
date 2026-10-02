<?php

namespace App\Enums;

/**
 * The kind of period inside an academic cycle.
 *
 * A school divides its year the way its calendar requires. The application
 * stores the choice instead of assuming academic periods. The type says what a period
 * is for; the local label on the period says what the school calls it.
 */
enum AcademicPeriodType: string
{
    /**
     * A term. Three of them usually make a year.
     */
    case Term = 'term';

    /**
     * A semester. Two of them usually make a year.
     */
    case Semester = 'semester';

    /**
     * A trimester.
     */
    case Trimester = 'trimester';

    /**
     * A quarter. Four of them make a year.
     */
    case Quarter = 'quarter';

    /**
     * A stretch of days set aside for examinations.
     */
    case ExamWindow = 'exam_window';

    /**
     * A break. Teaching stops and attendance is not expected.
     */
    case Holiday = 'holiday';

    /**
     * A window that exists to report on, such as a midterm.
     */
    case ReportingPeriod = 'reporting_period';

    /**
     * Any other division a school uses.
     */
    case Other = 'other';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Term => 'Term',
            self::Semester => 'Semester',
            self::Trimester => 'Trimester',
            self::Quarter => 'Quarter',
            self::ExamWindow => 'Exam window',
            self::Holiday => 'Holiday',
            self::ReportingPeriod => 'Reporting period',
            self::Other => 'Period',
        };
    }

    /**
     * Check if the school teaches during this kind of period.
     *
     * Attendance, timetables, and grading belong to teaching periods. A
     * holiday holds none of them.
     */
    public function isTeaching(): bool
    {
        return match ($this) {
            self::Holiday => false,
            default => true,
        };
    }

    /**
     * Check if this kind of period divides the cycle itself.
     *
     * A cycle is made of these, one after another. The rest sit inside them.
     */
    public function isPrimaryDivision(): bool
    {
        return match ($this) {
            self::Term, self::Semester, self::Trimester, self::Quarter => true,
            default => false,
        };
    }

    /**
     * Get the kinds that only make sense inside another period.
     *
     * @return array<int, self>
     */
    public static function subPeriodTypes(): array
    {
        return [self::ExamWindow, self::Holiday, self::ReportingPeriod];
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $type): string => $type->value, self::cases());
    }
}
