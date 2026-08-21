<?php

namespace App\Enums;

/**
 * What kind of programme a school runs beside its lessons.
 */
enum ProgramType: string
{
    case Club = 'club';
    case Intervention = 'intervention';
    case SupportService = 'support_service';
    case Extracurricular = 'extracurricular';
    case Special = 'special';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Club            => 'Club',
            self::Intervention    => 'Intervention',
            self::SupportService  => 'Support service',
            self::Extracurricular => 'Extracurricular activity',
            self::Special         => 'Special programme',
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
