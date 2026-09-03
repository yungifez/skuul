<?php

namespace App\Http\Controllers;

use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\Cohort;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Services\Ranking\ResultRanking;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
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
        $participants = $this->participants($academicLevel, $section, $cohort);
        $offerings = $this->offerings($period, $academicLevel, $participants);
        $subjects = $this->subjects($offerings, $participants);
        $subject = $this->subjectFrom($request, $subjects);
        $courseOfferingIds = $subject === null
            ? null
            : $offerings
                ->filter(fn (CourseOffering $offering): bool => $this->participantIdsCoveredByOffering($offering, $participants)->isNotEmpty())
                ->where('subject_id', $subject->id)
                ->modelKeys();

        [$rows, $error] = $this->rank($academicLevel, $section, $cohort, $period, $subject?->id, $courseOfferingIds);

        return view('pages.ranking.index', [
            'rows' => $rows,
            'error' => $error,
            'learners' => $this->learnersOf($rows),
            'academicLevels' => AcademicLevel::query()->inSchool()->with('parent:id,name')->orderBy('is_group')->orderBy('position')->orderBy('name')->get(['id', 'parent_id', 'name', 'is_group']),
            'sections' => $this->sections($academicLevel),
            'cohorts' => Cohort::query()->inSchool()->active()->orderBy('name')->get(['id', 'name']),
            'periods' => AcademicPeriod::query()->inSchool()->ordered()->get(['id', 'name', 'label']),
            'subjects' => $subjects,
            'academicLevel' => $academicLevel,
            'section' => $section,
            'cohort' => $cohort,
            'period' => $period,
            'subject' => $subject,
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
        ?int $subjectId,
        ?array $courseOfferingIds,
    ): array {
        if ($academicLevel === null && $section === null && $cohort === null) {
            return [collect(), null];
        }

        try {
            if ($cohort !== null) {
                $rows = $this->ranking->forCohort($cohort, academicPeriodId: $period?->id, subjectId: $subjectId, courseOfferingIds: $courseOfferingIds);
            } elseif ($section !== null) {
                $rows = $this->ranking->forCycleSection($section, academicPeriodId: $period?->id, subjectId: $subjectId, courseOfferingIds: $courseOfferingIds);
            } elseif ($academicLevel !== null) {
                $rows = $this->ranking->forAcademicLevel($academicLevel, academicPeriodId: $period?->id, subjectId: $subjectId, courseOfferingIds: $courseOfferingIds);
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
     * @param  EloquentCollection<int, StudentRecord>  $participants
     * @return EloquentCollection<int, CourseOffering>
     */
    private function offerings(?AcademicPeriod $period, ?AcademicLevel $academicLevel, EloquentCollection $participants): EloquentCollection
    {
        $levelIds = $participants
            ->map(fn (StudentRecord $participant): ?int => $participant->academicCycleSection?->academic_level_id)
            ->filter()
            ->map(fn (int|string $levelId): int => (int) $levelId)
            ->all();

        if ($academicLevel !== null) {
            $levelIds = [...$levelIds, ...$this->offeringLevelIds($academicLevel)];
        }

        $levelIds = array_values(array_unique($levelIds));

        if ($levelIds === []) {
            return CourseOffering::query()->inSchool()->whereKey(-1)->get();
        }

        return CourseOffering::query()
            ->inSchool()
            ->with([
                'subject:id,name',
                'academicLevel:id,name',
                'cycleSections:id',
                'studentRecords:id,academic_cycle_section_id',
            ])
            ->whereIn('academic_level_id', $levelIds)
            ->when($period !== null, function (Builder $query) use ($period): void {
                $query->where('academic_period_id', $period->id);
            })
            ->orderBy('id')
            ->get(['id', 'academic_level_id', 'subject_id', 'roster_mode']);
    }

    /**
     * Get subjects that cover every learner in the selected scope.
     *
     * @param  EloquentCollection<int, CourseOffering>  $offerings
     * @param  EloquentCollection<int, StudentRecord>  $participants
     * @return EloquentCollection<int, Subject>
     */
    private function subjects(EloquentCollection $offerings, EloquentCollection $participants): EloquentCollection
    {
        $participantIds = $participants->modelKeys();

        if ($participantIds === []) {
            return Subject::query()->inSchool()->whereKey(-1)->get(['id', 'name']);
        }

        /** @var array<int, Collection<int, int>> $coveredBySubject */
        $coveredBySubject = [];

        foreach ($offerings as $offering) {
            $coveredIds = $this->participantIdsCoveredByOffering($offering, $participants);

            if ($coveredIds->isEmpty()) {
                continue;
            }

            $coveredBySubject[$offering->subject_id] = collect($coveredBySubject[$offering->subject_id] ?? [])
                ->merge($coveredIds)
                ->unique()
                ->values();
        }

        $commonSubjectIds = collect($coveredBySubject)
            ->filter(fn (Collection $coveredIds): bool => $coveredIds->count() === count($participantIds))
            ->keys()
            ->map(fn (int|string $subjectId): int => (int) $subjectId)
            ->values();

        if ($commonSubjectIds->isEmpty()) {
            return Subject::query()->inSchool()->whereKey(-1)->get(['id', 'name']);
        }

        return Subject::query()
            ->inSchool()
            ->whereIn('id', $commonSubjectIds)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Find the learners who take part in the active ranking scope.
     *
     * @return EloquentCollection<int, StudentRecord>
     */
    private function participants(?AcademicLevel $academicLevel, ?AcademicCycleSection $section, ?Cohort $cohort): EloquentCollection
    {
        $query = StudentRecord::query()
            ->inSchool()
            ->with('academicCycleSection:id,academic_level_id');

        if ($cohort !== null) {
            $studentRecordIds = $cohort->members()
                ->current()
                ->whereNotNull('student_record_id')
                ->pluck('student_record_id')
                ->all();

            return $query
                ->whereKey($studentRecordIds === [] ? [-1] : $studentRecordIds)
                ->get(['id', 'academic_cycle_section_id']);
        }

        if ($section !== null) {
            return $query
                ->where('academic_cycle_section_id', $section->id)
                ->get(['id', 'academic_cycle_section_id']);
        }

        if ($academicLevel === null) {
            return $query->whereKey(-1)->get(['id', 'academic_cycle_section_id']);
        }

        return $query
            ->whereHas('academicCycleSection', function (Builder $query) use ($academicLevel): void {
                $query->whereIn('academic_level_id', $academicLevel->teachingScopeIds());
            })
            ->get(['id', 'academic_cycle_section_id']);
    }

    /**
     * Return the chosen subject only when it is available to the scope.
     */
    private function subjectFrom(Request $request, EloquentCollection $subjects): ?Subject
    {
        $id = $request->integer('subject_id') ?: null;
        $subject = $id === null ? null : $subjects->firstWhere('id', $id);

        return $subject instanceof Subject ? $subject : null;
    }

    /**
     * Get the participant IDs covered by one roster.
     *
     * @param  EloquentCollection<int, StudentRecord>  $participants
     * @return Collection<int, int>
     */
    private function participantIdsCoveredByOffering(CourseOffering $offering, EloquentCollection $participants): Collection
    {
        if ($offering->roster_mode->usesHomeSections()) {
            $sectionIds = $offering->cycleSections->modelKeys();

            return collect($participants
                ->filter(fn (StudentRecord $participant): bool => in_array($participant->academic_cycle_section_id, $sectionIds, true))
                ->modelKeys())
                ->map(fn (int|string $studentRecordId): int => (int) $studentRecordId);
        }

        if ($offering->roster_mode === RosterMode::AcademicLevel) {
            return collect($participants
                ->filter(function (StudentRecord $participant) use ($offering): bool {
                    $levelId = $participant->academicCycleSection?->academic_level_id;

                    return $levelId !== null
                        && in_array($offering->academic_level_id, $this->academicLevelHierarchyIds($levelId), true);
                })
                ->modelKeys())
                ->map(fn (int|string $studentRecordId): int => (int) $studentRecordId);
        }

        $studentIds = $offering->studentRecords->modelKeys();

        return collect($participants
            ->filter(fn (StudentRecord $participant): bool => in_array($participant->id, $studentIds, true))
            ->modelKeys())
            ->map(fn (int|string $studentRecordId): int => (int) $studentRecordId);
    }

    /**
     * @var array<int, list<int>>
     */
    private array $academicLevelHierarchies = [];

    /**
     * @return list<int>
     */
    private function academicLevelHierarchyIds(int $academicLevelId): array
    {
        if (!array_key_exists($academicLevelId, $this->academicLevelHierarchies)) {
            $this->academicLevelHierarchies[$academicLevelId] = AcademicLevel::query()
                ->inSchool()
                ->find($academicLevelId)?->hierarchyIds() ?? [$academicLevelId];
        }

        return $this->academicLevelHierarchies[$academicLevelId];
    }

    /**
     * Get sections below the selected class or group.
     *
     * @return EloquentCollection<int, AcademicCycleSection>
     */
    private function sections(?AcademicLevel $academicLevel): EloquentCollection
    {
        if ($academicLevel === null) {
            return AcademicCycleSection::query()->inSchool()->whereKey(-1)->get();
        }

        return AcademicCycleSection::query()
            ->inSchool()
            ->where('academic_year_id', current_academic_year_id())
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
                ->where('academic_year_id', current_academic_year_id())
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
}
