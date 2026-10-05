<?php

namespace App\Enums;

use App\Models\User;

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
     * Get the permission a person needs to read this category at a campus.
     *
     * Ordinary categories need none beyond the sharing permissions.
     */
    public function readPermission(): ?string
    {
        return match ($this) {
            self::Health => 'read health record',
            self::Discipline => 'read incident',
            self::Safeguarding => 'read safeguarding case',
            self::Wellbeing => 'read confidential support plan',
            self::Finance => 'read fee invoice',
            default => null,
        };
    }

    /**
     * Check if the person may read this category at the campus they work in now.
     */
    public function isReadableBy(User $user): bool
    {
        $permission = $this->readPermission();

        return $permission === null || $user->can($permission);
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
