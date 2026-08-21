<?php

namespace App\Enums;

/**
 * The kinds of information one school can ask another for.
 *
 * Sharing an organization does not mean sharing everything. Identity,
 * guardians, enrollment, and published academic work travel with a role scope.
 * Everything else needs an approved request, one category at a time.
 */
enum DataCategory: string
{
    case Identity = 'identity';
    case Guardians = 'guardians';
    case Enrollment = 'enrollment';
    case AcademicResults = 'academic_results';
    case Attendance = 'attendance';
    case Health = 'health';
    case Discipline = 'discipline';
    case Safeguarding = 'safeguarding';
    case Wellbeing = 'wellbeing';
    case Finance = 'finance';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Identity => 'Identity',
            self::Guardians => 'Guardians',
            self::Enrollment => 'Enrollment',
            self::AcademicResults => 'Published results',
            self::Attendance => 'Attendance',
            self::Health => 'Health',
            self::Discipline => 'Discipline',
            self::Safeguarding => 'Safeguarding',
            self::Wellbeing => 'Support and wellbeing',
            self::Finance => 'Detailed finance',
        };
    }

    /**
     * Check if the category is closed unless a request is approved for it.
     */
    public function isRestricted(): bool
    {
        return in_array($this, [
            self::Health,
            self::Discipline,
            self::Safeguarding,
            self::Wellbeing,
            self::Finance,
        ], true);
    }

    /**
     * Get the categories that travel with an ordinary role scope.
     *
     * @return array<int, self>
     */
    public static function ordinary(): array
    {
        return array_values(array_filter(self::cases(), fn (self $category): bool => !$category->isRestricted()));
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $category): string => $category->value, self::cases());
    }
}
