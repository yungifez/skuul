<?php

namespace App\Enums;

enum NoticeAudienceScope: string
{
    case School = 'school';

    case Classes = 'class';

    case Section = 'section';

    /**
     * The learners on the rosters of the chosen course offerings.
     */
    case Offering = 'offering';

    public function label(): string
    {
        return match ($this) {
            self::School => 'Whole school',
            self::Classes => 'Classes / levels',
            self::Section => 'Sections',
            self::Offering => 'Subjects',
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
