<?php

namespace App\Livewire;

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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Filter and display rankings without reloading the whole page.
 *
 * Subject options are calculated from the selected learners and their actual
 * course offerings. A subject is available only when those offerings cover
 * every learner in the active scope.
 */
class RankingFilters extends Component
{
    #[Url(as: 'selection_mode')]
    public string $selectionMode = 'class';

    #[Url(as: 'academic_level_id')]
    public ?int $academicLevelId = null;

    #[Url(as: 'group_academic_level_id')]
    public ?int $groupAcademicLevelId = null;

    #[Url(as: 'academic_cycle_section_id')]
    public ?int $academicCycleSectionId = null;

    #[Url(as: 'cohort_id')]
    public ?int $cohortId = null;

    #[Url(as: 'academic_period_id')]
    public ?int $academicPeriodId = null;

    #[Url(as: 'subject_id')]
    public ?int $subjectId = null;

    /**
     * @var array<int, list<int>>
     */
    private array $academicLevelHierarchies = [];

    protected ResultRanking $ranking;

    public function boot(ResultRanking $ranking): void
    {
        $this->ranking = $ranking;
    }

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('read ranking'), 403);

        $this->normalizeInitialSelection();
    }

    public function updatedSelectionMode(): void
    {
        if ($this->selectionMode === 'group') {
            $this->academicLevelId = null;
        } else {
            $this->selectionMode = 'class';
            $this->groupAcademicLevelId = null;
        }

        $this->academicCycleSectionId = null;
        $this->subjectId = null;
    }

    public function updatedAcademicLevelId(): void
    {
        $this->selectionMode = 'class';
        $this->groupAcademicLevelId = null;
        $this->academicCycleSectionId = null;
        $this->subjectId = null;
    }

    public function updatedGroupAcademicLevelId(): void
    {
        $this->selectionMode = 'group';
        $this->academicLevelId = null;
        $this->academicCycleSectionId = null;
        $this->subjectId = null;
    }

    public function updatedAcademicCycleSectionId(): void
    {
        $this->subjectId = null;
    }

    public function updatedCohortId(): void
    {
        $this->subjectId = null;
    }

    public function updatedAcademicPeriodId(): void
    {
        $this->subjectId = null;
    }

    public function updatedSubjectId(): void
    {
        $academicLevel = $this->academicLevel();
        $section = $this->section($academicLevel);
        $cohort = $this->cohort();
        $participants = $this->participants($academicLevel, $section, $cohort);
        $subjects = $this->subjects($this->offerings($this->period(), $academicLevel, $participants), $participants);

        if ($this->subjectId !== null && !$subjects->contains('id', $this->subjectId)) {
            $this->subjectId = null;
        }
    }

    public function clearFilters(): void
    {
        $this->selectionMode = 'class';
        $this->academicLevelId = null;
        $this->groupAcademicLevelId = null;
        $this->academicCycleSectionId = null;
        $this->cohortId = null;
        $this->academicPeriodId = null;
        $this->subjectId = null;
    }

    public function render(): View
    {
        $academicLevel = $this->academicLevel();
        $section = $this->section($academicLevel);
        $cohort = $this->cohort();
        $period = $this->period();
        $participants = $this->participants($academicLevel, $section, $cohort);
        $offerings = $this->offerings($period, $academicLevel, $participants);
        $subjects = $this->subjects($offerings, $participants);
        $subject = $this->subject($subjects);
        $courseOfferingIds = $subject === null
            ? null
            : $offerings
                ->filter(fn (CourseOffering $offering): bool => $this->participantIdsCoveredByOffering($offering, $participants)->isNotEmpty())
                ->where('subject_id', $subject->id)
                ->modelKeys();

        [$rows, $error] = $this->rank($academicLevel, $section, $cohort, $period, $subject?->id, $courseOfferingIds);

        return view('livewire.ranking-filters', [
            'rows' => $rows,
            'error' => $error,
            'learners' => $this->learnersOf($rows),
            'academicLevels' => AcademicLevel::query()
                ->inSchool()
                ->with('parent:id,name')
                ->orderBy('is_group')
                ->orderBy('position')
                ->orderBy('name')
                ->get(['id', 'parent_id', 'name', 'is_group']),
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
     * @return EloquentCollection<int, StudentRecord>
     */
    private function participants(
        ?AcademicLevel $academicLevel = null,
        ?AcademicCycleSection $section = null,
        ?Cohort $cohort = null,
    ): EloquentCollection {
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
     * @return list<int>
     */
    private function offeringLevelIds(AcademicLevel $academicLevel): array
    {
        return array_values(array_unique([
            ...$academicLevel->teachingScopeIds(),
            ...$academicLevel->hierarchyIds(),
        ]));
    }

    private function academicLevel(): ?AcademicLevel
    {
        $id = $this->selectionMode === 'group'
            ? $this->groupAcademicLevelId
            : $this->academicLevelId;

        if ($id === null) {
            return null;
        }

        return AcademicLevel::query()
            ->inSchool()
            ->when($this->selectionMode === 'group', fn (Builder $query): Builder => $query->where('is_group', true))
            ->when($this->selectionMode === 'class', fn (Builder $query): Builder => $query->where('is_group', false))
            ->find($id);
    }

    private function section(?AcademicLevel $academicLevel): ?AcademicCycleSection
    {
        if ($this->academicCycleSectionId === null || $academicLevel === null) {
            return null;
        }

        return AcademicCycleSection::query()
            ->inSchool()
            ->where('academic_year_id', current_academic_year_id())
            ->whereIn('academic_level_id', $academicLevel->teachingScopeIds())
            ->with('academicLevel:id,name,parent_id')
            ->find($this->academicCycleSectionId);
    }

    private function cohort(): ?Cohort
    {
        if ($this->cohortId === null) {
            return null;
        }

        $cohort = Cohort::query()->inSchool()->find($this->cohortId);

        return $cohort === null || auth()->user()->cannot('view', $cohort) ? null : $cohort;
    }

    private function period(): ?AcademicPeriod
    {
        return $this->academicPeriodId === null
            ? null
            : AcademicPeriod::query()->inSchool()->find($this->academicPeriodId);
    }

    /**
     * @param  EloquentCollection<int, Subject>  $subjects
     */
    private function subject(EloquentCollection $subjects): ?Subject
    {
        $subject = $this->subjectId === null ? null : $subjects->firstWhere('id', $this->subjectId);

        return $subject instanceof Subject ? $subject : null;
    }

    private function normalizeInitialSelection(): void
    {
        if (!in_array($this->selectionMode, ['class', 'group'], true)) {
            $this->selectionMode = 'class';
        }

        $selectedLevel = $this->academicLevelId === null
            ? null
            : AcademicLevel::query()->inSchool()->find($this->academicLevelId);

        if ($selectedLevel?->is_group) {
            $this->selectionMode = 'group';
            $this->groupAcademicLevelId = $selectedLevel->id;
            $this->academicLevelId = null;
        }

        if ($this->groupAcademicLevelId !== null) {
            $this->selectionMode = 'group';
            $this->academicLevelId = null;

            return;
        }

        if ($this->academicLevelId !== null) {
            $this->selectionMode = 'class';

            return;
        }

        if ($this->academicCycleSectionId === null) {
            return;
        }

        $section = AcademicCycleSection::query()
            ->inSchool()
            ->where('academic_year_id', current_academic_year_id())
            ->with('academicLevel:id,parent_id,is_group')
            ->find($this->academicCycleSectionId);
        $hierarchyIds = $section?->academicLevel?->hierarchyIds() ?? [];
        $rootId = end($hierarchyIds);

        if ($rootId === false) {
            return;
        }

        $root = AcademicLevel::query()->inSchool()->find($rootId);

        if ($root?->is_group) {
            $this->selectionMode = 'group';
            $this->groupAcademicLevelId = $root->id;
        } elseif ($root !== null) {
            $this->selectionMode = 'class';
            $this->academicLevelId = $root->id;
        }
    }
}
