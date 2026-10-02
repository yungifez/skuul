<?php

namespace App\Enums;

/**
 * Why a member of staff is away.
 */
enum LeaveType: string
{
    case Annual = 'annual';
    case Sick = 'sick';
    case Compassionate = 'compassionate';
    case Study = 'study';
    case Maternity = 'maternity';
    case Unpaid = 'unpaid';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Annual => 'Annual leave',
            self::Sick => 'Sick leave',
            self::Compassionate => 'Compassionate leave',
            self::Study => 'Study leave',
            self::Maternity => 'Maternity leave',
            self::Unpaid => 'Unpaid leave',
        };
    }

    /**
     * Check if the school can ask for it before it starts.
     *
     * Sick and compassionate leave are often recorded after the fact.
     */
    public function needsNotice(): bool
    {
        return !in_array($this, [self::Sick, self::Compassionate], true);
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
