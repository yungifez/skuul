<?php

namespace App\Services\Calendar;

use App\Enums\GradeEntryState;
use App\Enums\TimetableStatus;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\GradeEntry;
use App\Models\GradeItem;
use App\Models\Timetable;
use Illuminate\Support\Collection;

/**
 * Work out what is still outstanding before a period can close.
 *
 * The check is read-only and it never closes anything. A person reads the
 * findings, finishes the work or decides to accept it, and confirms the close.
 * That confirmation is the step this application refuses to automate.
 *
 * Finance is deliberately absent. An unpaid invoice does not stop a term from
 * ending, and closing a term must not stop a school from collecting the fee.
 */
class ClosureReadinessCheck
{
    /**
     * Run every check against the period.
     *
     * @return Collection<int, ClosureFinding>
     */
    public function for(AcademicYear|AcademicPeriod $period): Collection
    {
        $findings = $period instanceof AcademicYear
            ? $this->forYear($period)
            : $this->forPeriod($period);

        return collect($findings)->filter(fn (ClosureFinding $finding): bool => $finding->count > 0)->values();
    }

    /**
     * Check whether the period can close without a person forcing it.
     */
    public function isReady(AcademicYear|AcademicPeriod $period): bool
    {
        return $this->for($period)->every(fn (ClosureFinding $finding): bool => !$finding->blocking);
    }

    /**
     * Get the findings as arrays, for storing on the close record.
     *
     * @return array<int, array{key: string, summary: string, count: int, blocking: bool}>
     */
    public function snapshot(AcademicYear|AcademicPeriod $period): array
    {
        return $this->for($period)->map(fn (ClosureFinding $finding): array => $finding->toArray())->all();
    }

    /**
     * Check one period of a cycle.
     *
     * @return array<int, ClosureFinding>
     */
    private function forPeriod(AcademicPeriod $period): array
    {
        return [
            $this->unpublishedTimetables($period),
            $this->ungradedItems($period),
            $this->unpublishedExamResults($period),
        ];
    }

    /**
     * Check a whole cycle, and every period inside it.
     *
     * @return array<int, ClosureFinding>
     */
    private function forYear(AcademicYear $year): array
    {
        $openPeriods = $year->academicPeriods()->operational()->count();

        // Closing a cycle is what closes the periods inside it, so an open
        // period is not an obstacle. It is told to the person confirming,
        // because the close reaches further than the row they clicked.
        $findings = [new ClosureFinding(
            key: 'open_periods',
            summary: 'Closing this cycle will also close the periods still open in it.',
            count: $openPeriods,
            blocking: false,
        )];

        foreach ($year->academicPeriods()->get() as $period) {
            $findings = array_merge($findings, $this->forPeriod($period));
        }

        return $this->merge($findings);
    }

    /**
     * Add up findings of the same kind, so a cycle reports one line each.
     *
     * @param  array<int, ClosureFinding>  $findings
     * @return array<int, ClosureFinding>
     */
    private function merge(array $findings): array
    {
        $merged = [];

        foreach ($findings as $finding) {
            $existing = $merged[$finding->key] ?? null;

            $merged[$finding->key] = new ClosureFinding(
                key: $finding->key,
                summary: $finding->summary,
                count: ($existing?->count ?? 0) + $finding->count,
                blocking: $finding->blocking,
            );
        }

        return array_values($merged);
    }

    /**
     * Count timetables that never left draft.
     *
     * A draft timetable means nobody agreed what was taught, so the period has
     * no record of its own teaching.
     */
    private function unpublishedTimetables(AcademicPeriod $period): ClosureFinding
    {
        $count = Timetable::where('academic_period_id', $period->id)
            ->where('status', TimetableStatus::Draft)
            ->count();

        return new ClosureFinding(
            key: 'draft_timetables',
            summary: 'Timetables are still in draft.',
            count: $count,
            blocking: false,
        );
    }

    /**
     * Count grade items that still have entries nobody graded.
     *
     * A missing entry is a decision; an ungraded one is unfinished work.
     */
    private function ungradedItems(AcademicPeriod $period): ClosureFinding
    {
        $itemIds = GradeItem::where('academic_period_id', $period->id)->pluck('id');

        $count = $itemIds->isEmpty() ? 0 : GradeEntry::whereIn('grade_item_id', $itemIds)
            ->where('state', GradeEntryState::Incomplete)
            ->count();

        return new ClosureFinding(
            key: 'ungraded_entries',
            summary: 'Grade entries are still incomplete.',
            count: $count,
            blocking: true,
        );
    }

    /**
     * Count exams whose results were never published.
     */
    private function unpublishedExamResults(AcademicPeriod $period): ClosureFinding
    {
        $count = $period->exams()->where('publish_result', false)->count();

        return new ClosureFinding(
            key: 'unpublished_exam_results',
            summary: 'Exam results are not published.',
            count: $count,
            blocking: false,
        );
    }
}
