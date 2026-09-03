<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
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
        $academicLevel = $this->academicLevelFrom($request, $section);
        $section = $this->sectionFrom($request, $academicLevel);
        $cohort = $this->cohortFrom($request);
        $period = $this->periodFrom($request);
        $offering = $this->offeringFrom($request, $academicLevel, $period);

        [$rows, $error] = $this->rank($academicLevel, $section, $cohort, $period, $offering);

        return view('pages.ranking.index', [
            'rows' => $rows,
            'error' => $error,
            'learners' => $this->learnersOf($rows),
            'academicLevels' => AcademicLevel::query()->inSchool()->with('parent:id,name')->orderBy('is_group')->orderBy('position')->orderBy('name')->get(['id', 'parent_id', 'name', 'is_group']),
            'sections' => $this->sections($academicLevel),
            'cohorts' => Cohort::query()->inSchool()->active()->orderBy('name')->get(['id', 'name']),
            'periods' => AcademicPeriod::query()->inSchool()->ordered()->get(['id', 'name', 'label']),
            'offerings' => $this->offerings($period, $academicLevel),
            'academicLevel' => $academicLevel,
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
        ?AcademicLevel $academicLevel,
        ?AcademicCycleSection $section,
        ?Cohort $cohort,
        ?AcademicPeriod $period,
        ?CourseOffering $offering,
    ): array {
        if ($academicLevel === null && $section === null && $cohort === null) {
            return [collect(), null];
        }

        try {
            if ($cohort !== null) {
                $rows = $this->ranking->forCohort($cohort, academicPeriodId: $period?->id, courseOffering: $offering);
            } elseif ($section !== null) {
                $rows = $this->ranking->forCycleSection($section, academicPeriodId: $period?->id, courseOffering: $offering);
            } elseif ($academicLevel !== null) {
                $rows = $this->ranking->forAcademicLevel($academicLevel, academicPeriodId: $period?->id, courseOffering: $offering);
            } else {
                return [collect(), null];
            }
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
    private function offerings(?AcademicPeriod $period, ?AcademicLevel $academicLevel): \Illuminate\Database\Eloquent\Collection
    {
        return CourseOffering::query()
            ->inSchool()
            ->with(['subject:id,name', 'academicLevel:id,name'])
            ->when($academicLevel !== null, fn (Builder $query): Builder => $query->whereIn('academic_level_id', $this->offeringLevelIds($academicLevel)))
            ->when($period !== null, function (Builder $query) use ($period): void {
                $query->where('academic_period_id', $period->id);
            })
            ->orderBy('id')
            ->get();
    }

    /**
     * Get sections below the selected class or group.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, AcademicCycleSection>
     */
    private function sections(?AcademicLevel $academicLevel): \Illuminate\Database\Eloquent\Collection
    {
        if ($academicLevel === null) {
            return AcademicCycleSection::query()->inSchool()->whereKey(-1)->get();
        }

        return AcademicCycleSection::query()
            ->inSchool()
            ->with('academicLevel:id,name')
            ->whereIn('academic_level_id', $academicLevel->teachingScopeIds())
            ->orderBy('academic_level_id')
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'academic_level_id', 'name', 'label']);
    }

    /**
     * Get offerings declared for the selected level, its child levels, or a
     * parent group that teaches across it.
     *
     * @return list<int>
     */
    private function offeringLevelIds(AcademicLevel $academicLevel): array
    {
        return array_values(array_unique([
            ...$academicLevel->teachingScopeIds(),
            ...$academicLevel->hierarchyIds(),
        ]));
    }

    /**
     * Read the home group the screen was asked for.
     */
    private function sectionFrom(Request $request, ?AcademicLevel $academicLevel = null): ?AcademicCycleSection
    {
        $id = $request->integer('academic_cycle_section_id') ?: null;

        return $id === null
            ? null
            : AcademicCycleSection::query()
                ->inSchool()
                ->when($academicLevel !== null, fn (Builder $query): Builder => $query->whereIn('academic_level_id', $academicLevel->teachingScopeIds()))
                ->with('academicLevel:id,name,parent_id')
                ->find($id);
    }

    /**
     * Read the selected class or group, or infer its top-level group from a
     * selected section.
     */
    private function academicLevelFrom(Request $request, ?AcademicCycleSection $section): ?AcademicLevel
    {
        $groupId = $request->integer('group_academic_level_id') ?: null;

        if ($groupId !== null) {
            return AcademicLevel::query()->inSchool()->where('is_group', true)->find($groupId);
        }

        $id = $request->integer('academic_level_id') ?: null;

        if ($id !== null) {
            return AcademicLevel::query()->inSchool()->find($id);
        }

        if ($section === null || $section->academicLevel === null) {
            return null;
        }

        $hierarchyIds = $section->academicLevel->hierarchyIds();
        $rootId = end($hierarchyIds);

        return $rootId === false ? null : AcademicLevel::query()->inSchool()->find($rootId);
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
    private function offeringFrom(Request $request, ?AcademicLevel $academicLevel, ?AcademicPeriod $period): ?CourseOffering
    {
        $id = $request->integer('course_offering_id') ?: null;

        return $id === null
            ? null
            : CourseOffering::query()
                ->inSchool()
                ->when($academicLevel !== null, fn (Builder $query): Builder => $query->whereIn('academic_level_id', $this->offeringLevelIds($academicLevel)))
                ->when($period !== null, fn (Builder $query): Builder => $query->where('academic_period_id', $period->id))
                ->find($id);
    }
}
