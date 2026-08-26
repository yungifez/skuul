<?php

namespace App\Enums;

enum SchoolSetupStep: string
{
    case Details = 'details';
    case Language = 'language';
    case Classes = 'classes';
    case AcademicYear = 'academic-year';
    case Finish = 'finish';

    public function label(): string
    {
        return match ($this) {
            self::Details => 'School details',
            self::Language => 'School language',
            self::Classes => 'Classes or grades',
            self::AcademicYear => 'Academic year',
            self::Finish => 'Finish setup',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Details => 'Confirm the information shown to families and staff.',
            self::Language => 'Use the terms your school already knows.',
            self::Classes => 'Add the reusable classes or grades your school teaches.',
            self::AcademicYear => 'Create the first year and its reporting periods.',
            self::Finish => 'Review what is ready and choose the next task.',
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::Details => 1,
            self::Language => 2,
            self::Classes => 3,
            self::AcademicYear => 4,
            self::Finish => 5,
        };
    }
}
