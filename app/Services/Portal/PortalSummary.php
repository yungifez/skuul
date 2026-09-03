<?php

namespace App\Services\Portal;

use App\Enums\Feature;
use App\Enums\NoticeStatus;
use App\Enums\PortalArea;
use App\Models\BoardingPlace;
use App\Models\FeeInvoice;
use App\Models\LibraryLoan;
use App\Models\LibraryReservation;
use App\Models\NoticeRecipient;
use App\Models\ReportCardSnapshot;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Timetable;
use App\Models\TranscriptSnapshot;
use App\Services\Attendance\AttendanceSummary;
use App\Services\Finance\StudentLedger;
use Illuminate\Support\Collection;

/**
 * Gather what one family may read about one student.
 *
 * Every part comes from a record the school already published: a result
 * snapshot, a published timetable, a published notice, a posted ledger line.
 * Work in progress never reaches the portal.
 */
class PortalSummary
{
    public function __construct(
        private PortalAccess $access,
        private AttendanceSummary $attendance,
        private StudentLedger $ledger,
    ) {}

    /**
     * Get the newest published result of each course offering.
     *
     * @return Collection<int, ResultSnapshot>
     */
    public function results(StudentRecord $enrollment, ?int $academicYearId = null, ?int $academicPeriodId = null): Collection
    {
        if (!$this->access->areaIsOpen(PortalArea::Results, $enrollment->school_id)) {
            return collect();
        }

        return ResultSnapshot::query()
            ->where('student_record_id', $enrollment->id)
            ->approved()
            ->when($academicYearId !== null, fn ($query) => $query->whereHas('courseOffering', fn ($query) => $query->where('academic_year_id', $academicYearId)))
            ->when($academicPeriodId !== null, fn ($query) => $query->whereHas('courseOffering', fn ($query) => $query->where('academic_period_id', $academicPeriodId)))
            ->with('courseOffering.subject')
            ->get()
            ->groupBy('course_offering_id')
            ->map(fn (Collection $rows): ResultSnapshot => $rows->sortByDesc('revision')->first())
            ->values();
    }

    /**
     * Count what the registers say.
     *
     * @return array{present: int, absent: int, late: int, excused: int, recorded: int, rate: float|null}|null
     */
    public function attendance(StudentRecord $enrollment, mixed $from = null, mixed $to = null): ?array
    {
        if (!$this->access->areaIsOpen(PortalArea::Attendance, $enrollment->school_id)) {
            return null;
        }

        return $this->attendance->forStudent($enrollment, $from, $to);
    }

    /**
     * Get the published timetable of the student's current home group.
     */
    public function timetable(StudentRecord $enrollment): ?Timetable
    {
        if (!$this->access->areaIsOpen(PortalArea::Timetable, $enrollment->school_id)) {
            return null;
        }

        return Timetable::query()
            ->where('academic_cycle_section_id', $enrollment->academic_cycle_section_id)
            ->published()
            ->orderByDesc('published_at')
            ->first();
    }

    /**
     * Get the notices sent to the student.
     *
     * @return Collection<int, NoticeRecipient>
     */
    public function notices(StudentRecord $enrollment): Collection
    {
        if (!$this->access->areaIsOpen(PortalArea::Notices, $enrollment->school_id)) {
            return collect();
        }

        return NoticeRecipient::query()
            ->where('user_id', $enrollment->user_id)
            ->whereHas('notice', fn ($query) => $query->where('status', NoticeStatus::Published))
            ->with('notice')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Get the invoices and what the student owes.
     *
     * @return array{invoices: Collection<int, FeeInvoice>, balance: float, unapplied_credit: float}|null
     */
    public function invoices(StudentRecord $enrollment): ?array
    {
        if (!$this->access->areaIsOpen(PortalArea::Invoices, $enrollment->school_id)) {
            return null;
        }

        return [
            'invoices' => FeeInvoice::query()
                ->ofSchool($enrollment->school_id)
                ->where('student_record_id', $enrollment->id)
                ->with(['feeInvoiceRecords', 'allocations'])
                ->orderByDesc('id')
                ->get(),
            'balance' => $this->ledger->balance($enrollment),
            'unapplied_credit' => $this->ledger->unappliedCredit($enrollment),
        ];
    }

    /**
     * Get the learner's current loans and library queue entries.
     *
     * @return array{loans: Collection<int, LibraryLoan>, reservations: Collection<int, LibraryReservation>}|null
     */
    public function library(StudentRecord $enrollment): ?array
    {
        if (!$this->access->areaIsOpen(PortalArea::Library, $enrollment->school_id)) {
            return null;
        }

        return [
            'loans' => LibraryLoan::query()
                ->where('school_id', $enrollment->school_id)
                ->where('user_id', $enrollment->user_id)
                ->open()
                ->with('copy.title')
                ->orderBy('due_on')
                ->get(),
            'reservations' => LibraryReservation::query()
                ->where('school_id', $enrollment->school_id)
                ->where('user_id', $enrollment->user_id)
                ->stillGoing()
                ->with(['title', 'copy'])
                ->orderBy('id')
                ->get(),
        ];
    }

    /**
     * Get the newest official documents published for the learner.
     *
     * @return array{reportCards: Collection<int, ReportCardSnapshot>, transcript: TranscriptSnapshot|null}|null
     */
    public function documents(StudentRecord $enrollment): ?array
    {
        if (!$this->access->areaIsOpen(PortalArea::Documents, $enrollment->school_id)) {
            return null;
        }

        $reportCards = ReportCardSnapshot::query()
            ->where('school_id', $enrollment->school_id)
            ->where('student_record_id', $enrollment->id)
            ->with('academicPeriod:id,name,label')
            ->orderBy('academic_period_id')
            ->orderByDesc('revision')
            ->get()
            ->unique('academic_period_id')
            ->values();

        return [
            'reportCards' => $reportCards,
            'transcript' => TranscriptSnapshot::query()
                ->where('school_id', $enrollment->school_id)
                ->where('student_record_id', $enrollment->id)
                ->latest('revision')
                ->first(),
        ];
    }

    /**
     * Get the current house, room, and bed for a boarder.
     *
     * @return array{place: string|null}|null
     */
    public function boarding(StudentRecord $enrollment): ?array
    {
        if (!$this->access->areaIsOpen(PortalArea::Boarding, $enrollment->school_id)
            || !features()->enabled(Feature::Boarding, $enrollment->school_id)) {
            return null;
        }

        $place = BoardingPlace::query()
            ->where('school_id', $enrollment->school_id)
            ->where('student_record_id', $enrollment->id)
            ->latest('id')
            ->with('bed.room.dormitory')
            ->first();
        $bed = $place?->bed;
        $room = $bed?->room;
        $house = $room?->dormitory;

        return [
            'place' => $place?->isBoarding()
                ? trim(implode(' · ', array_filter([$house?->name, $room?->name, $bed?->name])), ' ·')
                : null,
        ];
    }
}
