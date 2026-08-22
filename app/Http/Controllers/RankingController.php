<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\Cohort;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Services\Ranking\ResultRanking;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Put a group of learners in order by their published results.
 *
 * A position is worked out when it is asked for, never stored. Correcting a
 * result changes the order the next time somebody opens this screen, and
 * nothing has to be rewritten.
 */
class RankingController extends Controller
{
    public function __construct(private ResultRanking $ranking) {}

    /**
     * Show one group in order.
     */
    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('read ranking'), 403);

        $section = $this->sectionFrom($request);
        $cohort = $this->cohortFrom($request);
        $period = $this->periodFrom($request);
        $offering = $this->offeringFrom($request);

        [$rows, $error] = $this->rank($section, $cohort, $period, $offering);

        return view('pages.ranking.index', [
            'rows' => $rows,
            'error' => $error,
            'learners' => $this->learnersOf($rows),
            'sections' => AcademicCycleSection::query()->inSchool()->orderBy('name')->get(['id', 'name']),
            'cohorts' => Cohort::query()->inSchool()->active()->orderBy('name')->get(['id', 'name']),
            'periods' => AcademicPeriod::query()->inSchool()->ordered()->get(['id', 'name', 'label']),
            'offerings' => $this->offerings($period),
            'section' => $section,
            'cohort' => $cohort,
            'period' => $period,
            'offering' => $offering,
        ]);
    }

    /**
     * Work out the order, or say why it cannot be worked out.
     *
     * @return array{0: Collection<int, array{student_record_id: int, average: float, subjects: int, position: int}>, 1: string|null}
     */
    private function rank(
        ?AcademicCycleSection $section,
        ?Cohort $cohort,
        ?AcademicPeriod $period,
        ?CourseOffering $offering,
    ): array {
        if ($section === null && $cohort === null) {
            return [collect(), null];
        }

        try {
            $rows = $cohort !== null
                ? $this->ranking->forCohort($cohort, academicPeriodId: $period?->id, courseOffering: $offering)
                : $this->ranking->forCycleSection($section, academicPeriodId: $period?->id, courseOffering: $offering);
        } catch (InvalidValueException $exception) {
            return [collect(), $exception->getMessage()];
        }

        return [$rows, null];
    }

    /**
     * Get the learners named in the order, by enrollment.
     *
     * @param  Collection<int, array{student_record_id: int, average: float, subjects: int, position: int}>  $rows
     * @return Collection<int, StudentRecord>
     */
    private function learnersOf(Collection $rows): Collection
    {
        if ($rows->isEmpty()) {
            return collect();
        }

        return StudentRecord::query()
            ->inSchool()
            ->with('user:id,name')
            ->whereIn('id', $rows->pluck('student_record_id'))
            ->get(['id', 'user_id', 'admission_number'])
            ->keyBy('id');
    }

    /**
     * Get the subjects the order can be narrowed to.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, CourseOffering>
     */
    private function offerings(?AcademicPeriod $period): \Illuminate\Database\Eloquent\Collection
    {
        return CourseOffering::query()
            ->inSchool()
            ->with('subject:id,name')
            ->when($period !== null, function (Builder $query) use ($period): void {
                $query->where('academic_period_id', $period->id);
            })
            ->orderBy('id')
            ->get();
    }

    /**
     * Read the home group the screen was asked for.
     */
    private function sectionFrom(Request $request): ?AcademicCycleSection
    {
        $id = $request->integer('academic_cycle_section_id') ?: null;

        return $id === null ? null : AcademicCycleSection::query()->inSchool()->find($id);
    }

    /**
     * Read the group the screen was asked for.
     *
     * A private group stays private, so a person who may not read one never
     * ranks it either.
     */
    private function cohortFrom(Request $request): ?Cohort
    {
        $id = $request->integer('cohort_id') ?: null;

        if ($id === null) {
            return null;
        }

        $cohort = Cohort::query()->inSchool()->find($id);

        return $cohort === null || $request->user()->cannot('view', $cohort) ? null : $cohort;
    }

    /**
     * Read the period the screen was asked for.
     */
    private function periodFrom(Request $request): ?AcademicPeriod
    {
        $id = $request->integer('academic_period_id') ?: null;

        return $id === null ? null : AcademicPeriod::query()->inSchool()->find($id);
    }

    /**
     * Read the subject the screen was asked for.
     */
    private function offeringFrom(Request $request): ?CourseOffering
    {
        $id = $request->integer('course_offering_id') ?: null;

        return $id === null ? null : CourseOffering::query()->inSchool()->find($id);
    }
}
