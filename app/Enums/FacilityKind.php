<?php

namespace App\Enums;

/**
 * The kind of thing a school books.
 *
 * A hall, a minibus, and a set of laboratory equipment are booked the same
 * way, so they are one catalogue rather than three.
 */
enum FacilityKind: string
{
    case Classroom = 'classroom';

    case Laboratory = 'laboratory';

    case Hall = 'hall';

    case SportsGround = 'sports_ground';

    case Vehicle = 'vehicle';

    case Equipment = 'equipment';

    case Other = 'other';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Classroom => 'Classroom',
            self::Laboratory => 'Laboratory',
            self::Hall => 'Hall',
            self::SportsGround => 'Sports ground',
            self::Vehicle => 'Vehicle',
            self::Equipment => 'Equipment',
            self::Other => 'Other',
        };
    }

    /**
     * Check whether a lesson can be taught in this.
     *
     * A minibus is booked, but no timetable entry is ever moved into one.
     */
    public function holdsLessons(): bool
    {
        return in_array($this, [self::Classroom, self::Laboratory, self::Hall, self::SportsGround], true);
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $kind): string => $kind->value, self::cases());
    }
}
