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
     * Boarding houses, beds, and nights away.
     */
    case Boarding = 'boarding';

    /**
     * The library catalogue and its loans.
     */
    case Library = 'library';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Attendance => 'Attendance',
            self::Portal => 'Student and guardian portal',
            self::Discipline => 'Discipline and safeguarding',
            self::Wellbeing => 'Student support and wellbeing',
            self::StaffOperations => 'Staff operations',
            self::Events => 'Calendar and events',
            self::Ranking => 'Rankings',
            self::Imports => 'Imports and integrations',
            self::Boarding => 'Boarding',
            self::Library => 'Library',
        };
    }

    /**
     * Get one sentence that says what the school turns off.
     */
    public function description(): string
    {
        return match ($this) {
            self::Attendance => 'Daily and lesson registers, and the attendance a family reads.',
            self::Portal => 'The area where learners and guardians sign in to read their own records.',
            self::Discipline => 'Behaviour records, incidents, and safeguarding cases.',
            self::Wellbeing => 'Health notes, counselling, and support plans.',
            self::StaffOperations => 'Staff leave, cover for absent teachers, and appraisals.',
            self::Events => 'The school calendar and the events on it.',
            self::Ranking => 'Positions that compare one learner with another.',
            self::Imports => 'Bulk uploads and links to outside systems.',
            self::Boarding => 'Boarding houses, who sleeps where, and who is out for the night.',
            self::Library => 'What the school lends, who has it, and when it is due back.',
        };
    }

    /**
     * Get the heading this feature belongs under on the settings screen.
     */
    public function group(): string
    {
        return match ($this) {
            self::Attendance, self::Events => 'Daily operations',
            self::Discipline, self::Wellbeing => 'Care and conduct',
            self::Portal => 'Families and learners',
            self::Ranking => 'Reporting',
            self::StaffOperations, self::Imports => 'Staff and data',
            self::Boarding, self::Library => 'Beyond the classroom',
        };
    }

    /**
     * Get the features of each group, in the order the screen shows them.
     *
     * @return array<string, array<int, self>>
     */
    public static function grouped(): array
    {
        $groups = [];

        foreach (self::cases() as $feature) {
            $groups[$feature->group()][] = $feature;
        }

        return $groups;
    }

    /**
     * Check whether the feature is on when nobody has chosen.
     *
     * Rankings start off, because a school should decide to rank children
     * rather than find that the application already does. Boarding and the
     * library start off because not every school has either.
     */
    public function defaultsToOn(): bool
    {
        return match ($this) {
            self::Ranking, self::Boarding, self::Library => false,
            default => true,
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
