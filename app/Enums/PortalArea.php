<?php

namespace App\Enums;

/**
 * The parts of the portal a school can open or close.
 *
 * Each area is read-only for the family. Nothing here writes a school record.
 */
enum PortalArea: string
{
    case Results = 'results';
    case Attendance = 'attendance';
    case Timetable = 'timetable';
    case Calendar = 'calendar';
    case Notices = 'notices';
    case Invoices = 'invoices';
    case Documents = 'documents';
    case Requests = 'requests';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Results => 'Results',
            self::Attendance => 'Attendance',
            self::Timetable => 'Timetable',
            self::Calendar => 'School calendar',
            self::Notices => 'Notices',
            self::Invoices => 'Invoices and payments',
            self::Documents => 'Documents',
            self::Requests => 'Requests',
        };
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $area): string => $area->value, self::cases());
    }
}
