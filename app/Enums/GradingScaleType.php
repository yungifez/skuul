<?php

namespace App\Enums;

enum GradingScaleType: string
{
    case Points = 'points';
    case Percentage = 'percentage';
    case Gpa = 'gpa';
    case Descriptive = 'descriptive';

    public function label(): string
    {
        return match ($this) {
            self::Points => 'Custom points',
            self::Percentage => 'Percentage',
            self::Gpa => 'GPA',
            self::Descriptive => 'Descriptive only',
        };
    }

    public function usesNumericValues(): bool
    {
        return $this !== self::Descriptive;
    }
}
