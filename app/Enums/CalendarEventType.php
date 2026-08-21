<?php

namespace App\Enums;

/**
 * What kind of day or event the calendar is holding.
 */
enum CalendarEventType: string
{
    /**
     * A day nobody comes in.
     */
    case Holiday = 'holiday';

    /**
     * The school is shut, for weather or another reason.
     */
    case Closure = 'closure';

    /**
     * A teaching day that runs differently.
     */
    case SpecialDay = 'special_day';

    /**
     * The whole school gathers.
     */
    case Assembly = 'assembly';

    /**
     * A club or an activity.
     */
    case Activity = 'activity';

    /**
     * A meeting with guardians.
     */
    case ParentMeeting = 'parent_meeting';

    /**
     * An appointment between named people.
     */
    case Appointment = 'appointment';

    /**
     * An exam sitting.
     */
    case Examination = 'examination';

    /**
     * Anything else the school puts on the calendar.
     */
    case Other = 'other';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Holiday => 'Holiday',
            self::Closure => 'Closure',
            self::SpecialDay => 'Special day',
            self::Assembly => 'Assembly',
            self::Activity => 'Activity',
            self::ParentMeeting => 'Parent meeting',
            self::Appointment => 'Appointment',
            self::Examination => 'Examination',
            self::Other => 'Event',
        };
    }

    /**
     * Check if the school teaches on a day of this kind.
     */
    public function isTeachingDay(): bool
    {
        return !in_array($this, [self::Holiday, self::Closure], true);
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
