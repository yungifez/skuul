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
     * What the learner has out of the library, and what they are waiting for.
     */
    case Library = 'library';

    case Boarding = 'boarding';

    case Graduation = 'graduation';

    case Programmes = 'programmes';

    /**
     * The published syllabi of the learner's courses, and how far the class is.
     */
    case Syllabi = 'syllabi';

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
            self::Library => 'Library',
            self::Boarding => 'Boarding',
            self::Requests => 'Requests',
            self::Graduation => 'Graduation progress',
            self::Programmes => 'Programmes',
            self::Syllabi => 'Syllabi',
        };
    }

    /**
     * Get the route that shows this area for one enrollment.
     */
    public function routeName(): string
    {
        return match ($this) {
            self::Results => 'portal.results.index',
            self::Attendance => 'portal.attendance.show',
            self::Timetable => 'portal.timetable.show',
            self::Calendar => 'portal.calendar.index',
            self::Notices => 'portal.notices.index',
            self::Invoices => 'portal.invoices.index',
            self::Documents => 'portal.documents.index',
            self::Library => 'portal.library.index',
            self::Boarding => 'portal.boarding.index',
            self::Requests => 'portal.requests.index',
            self::Graduation => 'portal.graduation.show',
            self::Programmes => 'portal.programmes.index',
            self::Syllabi => 'portal.syllabi.index',
        };
    }

    /**
     * Get the sidebar icon for this area.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Results => 'chart-column',
            self::Attendance => 'clipboard-check',
            self::Timetable => 'clock',
            self::Calendar => 'calendar-days',
            self::Notices => 'megaphone',
            self::Invoices => 'receipt',
            self::Documents => 'file-text',
            self::Library => 'library',
            self::Boarding => 'bed-double',
            self::Requests => 'message-square',
            self::Graduation => 'graduation-cap',
            self::Programmes => 'sparkles',
            self::Syllabi => 'book-open',
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
