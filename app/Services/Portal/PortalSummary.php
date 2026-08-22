<?php

namespace App\Services\Portal;

use App\Enums\NoticeStatus;
use App\Enums\PortalArea;
use App\Models\FeeInvoice;
use App\Models\NoticeRecipient;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Timetable;
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
                ->where('user_id', $enrollment->user_id)
                ->orderByDesc('id')
                ->get(),
            'balance' => $this->ledger->balance($enrollment),
            'unapplied_credit' => $this->ledger->unappliedCredit($enrollment),
        ];
    }
}
