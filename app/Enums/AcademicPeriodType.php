<?php

namespace App\Enums;

/**
 * The kind of period inside an academic year.
 *
 * A school divides its year the way its calendar requires. The application
 * stores the choice instead of assuming semesters.
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
     * Any other division a school uses.
     */
    case Other = 'other';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Term      => 'Term',
            self::Semester  => 'Semester',
            self::Trimester => 'Trimester',
            self::Quarter   => 'Quarter',
            self::Other     => 'Period',
        };
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
