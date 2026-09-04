<?php

namespace App\Enums;

enum NoticeAudienceScope: string
{
    case School = 'school';

    case Classes = 'class';

    case Section = 'section';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::School => 'Whole school',
            self::Classes => 'Classes / levels',
            self::Section => 'Sections',
        };
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $scope): string => $scope->value, self::cases());
    }
}
