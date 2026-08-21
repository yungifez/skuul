<?php

namespace App\Enums;

/**
 * The parts of the application a school can turn on or off.
 *
 * Identity, authorization, audit logging, and enrollment history are not
 * listed here on purpose: the application cannot work without them, so they
 * are never a setting.
 */
enum Feature: string
{
    /**
     * Daily and lesson registers.
     */
    case Attendance = 'attendance';

    /**
     * The student and guardian portal.
     */
    case Portal = 'portal';

    /**
     * Behaviour records and safeguarding cases.
     */
    case Discipline = 'discipline';

    /**
     * Health, counselling, and support records.
     */
    case Wellbeing = 'wellbeing';

    /**
     * Staff leave, cover, and appraisals.
     */
    case StaffOperations = 'staff_operations';

    /**
     * The school calendar and events.
     */
    case Events = 'events';

    /**
     * Class and subject rankings.
     */
    case Ranking = 'ranking';

    /**
     * Bulk imports and outside integrations.
     */
    case Imports = 'imports';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Attendance      => 'Attendance',
            self::Portal          => 'Student and guardian portal',
            self::Discipline      => 'Discipline and safeguarding',
            self::Wellbeing       => 'Student support and wellbeing',
            self::StaffOperations => 'Staff operations',
            self::Events          => 'Calendar and events',
            self::Ranking         => 'Rankings',
            self::Imports         => 'Imports and integrations',
        };
    }

    /**
     * Check whether the feature is on when nobody has chosen.
     *
     * Rankings start off, because a school should decide to rank children
     * rather than find that the application already does.
     */
    public function defaultsToOn(): bool
    {
        return match ($this) {
            self::Ranking => false,
            default       => true,
        };
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $feature): string => $feature->value, self::cases());
    }
}
