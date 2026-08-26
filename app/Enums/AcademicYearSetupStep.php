<?php

namespace App\Enums;

enum AcademicYearSetupStep: string
{
    case Calendar = 'calendar';
    case Teaching = 'teaching';
    case Structure = 'structure';
    case Subjects = 'subjects';
    case Review = 'review';

    public function label(): string
    {
        return match ($this) {
            self::Calendar => 'Dates and periods',
            self::Teaching => 'Teaching approach',
            self::Structure => 'Classes and teachers',
            self::Subjects => 'Subjects being taught',
            self::Review => 'Review and publish',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Calendar => 'Set the dates and terms or semesters.',
            self::Teaching => 'Choose how learners are grouped for teaching.',
            self::Structure => 'Create this year’s classes and assign class teachers.',
            self::Subjects => 'Choose the subjects for each class and period.',
            self::Review => 'Check the setup and make the year available.',
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::Calendar => 1,
            self::Teaching => 2,
            self::Structure => 3,
            self::Subjects => 4,
            self::Review => 5,
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::Calendar => self::Teaching,
            self::Teaching => self::Structure,
            self::Structure => self::Subjects,
            self::Subjects => self::Review,
            self::Review => null,
        };
    }

    public function previous(): ?self
    {
        return match ($this) {
            self::Calendar => null,
            self::Teaching => self::Calendar,
            self::Structure => self::Teaching,
            self::Subjects => self::Structure,
            self::Review => self::Subjects,
        };
    }
}
