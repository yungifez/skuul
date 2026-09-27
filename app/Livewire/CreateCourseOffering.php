<?php

namespace App\Livewire;

use App\Actions\Curriculum\CreateCourseOffering as CreateCourseOfferingAction;
use App\Actions\Curriculum\CreateCourseOfferingsForSections;
use App\Enums\AcademicStructureStatus;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Add one subject to a school year for a class, a group, or named learners.
 *
 * The choices narrow as the form fills: the year sets the periods and
 * sections, and the class sets who can attend.
 */
class CreateCourseOffering extends Component
{
    public ?int $academicYearId = null;

    public ?int $academicLevelId = null;

    public string $rosterMode = '';

    /** @var array<int, int|string> */
    public array $academicCycleSectionIds = [];

    /** @var array<int, int|string> */
    public array $studentRecordIds = [];

    public string $academicPeriodId = '';

    public ?int $subjectId = null;

    public ?int $plannedPeriodsPerWeek = null;

    public ?int $capacity = null;

    public bool $setup = false;

    public function mount(?int $academicYearId = null, bool $setup = false): void
    {
        Gate::authorize('create', CourseOffering::class);

        $this->setup = $setup;
        $this->academicYearId = AcademicYear::inSchool()->whereKey($academicYearId ?: current_academic_year_id())->value('id');
        $this->rosterMode = RosterMode::HomeSection->value;
    }

    public function updatedAcademicYearId(): void
    {
        $this->reset('academicCycleSectionIds', 'studentRecordIds', 'academicPeriodId');
        $this->keepRosterModeAllowed();
    }

    public function updatedAcademicLevelId(): void
    {
        $this->reset('academicCycleSectionIds', 'studentRecordIds');

        if ($this->selectedLevel()?->is_group) {
            $this->rosterMode = RosterMode::AcademicLevel->value;
        }
    }

    public function save(
        CreateCourseOfferingAction $createCourseOffering,
        CreateCourseOfferingsForSections $createCourseOfferingsForSections,
    ): void {
        Gate::authorize('create', CourseOffering::class);

        $this->validate([
            'academicYearId' => ['required', 'integer', Rule::exists('academic_years', 'id')->where('school_id', current_school_id())],
            'academicLevelId' => ['required', 'integer', Rule::exists('academic_levels', 'id')->where('school_id', current_school_id())],
            'rosterMode' => ['required', Rule::enum(RosterMode::class)],
            'academicCycleSectionIds' => ['array', Rule::requiredIf(in_array($this->rosterMode, [RosterMode::HomeSection->value, RosterMode::CombinedHomeSections->value], true))],
            'academicCycleSectionIds.*' => ['integer', 'distinct', Rule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id())],
            'studentRecordIds' => ['array', Rule::requiredIf($this->rosterMode === RosterMode::IndividualRoster->value)],
            'studentRecordIds.*' => ['integer', 'distinct', Rule::exists('student_records', 'id')->where('school_id', current_school_id())],
            'academicPeriodId' => ['required', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value !== 'all' && !AcademicPeriod::inSchool()->whereKey((int) $value)->where('academic_year_id', $this->academicYearId)->exists()) {
                    $fail('Choose a period of this year.');
                }
            }],
            'subjectId' => ['required', 'integer', Rule::exists('subjects', 'id')->where('school_id', current_school_id())],
            'plannedPeriodsPerWeek' => ['nullable', 'integer', 'min:1', 'max:80'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:5000'],
        ], [
            'academicCycleSectionIds.required' => 'Choose at least one '.strtolower(school_term('section', 'section')).'.',
            'studentRecordIds.required' => 'Choose at least one learner.',
        ]);

        $academicYear = AcademicYear::inSchool()->findOrFail($this->academicYearId);
        $subject = Subject::inSchool()->findOrFail($this->subjectId);
        $academicLevel = AcademicLevel::inSchool()->findOrFail($this->academicLevelId);
        $rosterMode = RosterMode::from($this->rosterMode);
        $sectionIds = $rosterMode->usesHomeSections() ? array_map('intval', $this->academicCycleSectionIds) : [];
        $learnerIds = $rosterMode === RosterMode::IndividualRoster ? array_map('intval', $this->studentRecordIds) : [];

        try {
            if ($rosterMode === RosterMode::HomeSection && count($sectionIds) > 1) {
                $created = $createCourseOfferingsForSections->create($subject, $academicYear, $this->academicPeriodId, $academicLevel, $sectionIds, $this->plannedPeriodsPerWeek, $this->capacity, auth()->user())->count();
            } elseif ($this->academicPeriodId === 'all') {
                $created = $createCourseOffering->createForAcademicYear($subject, $academicYear, $academicLevel, $sectionIds, $rosterMode, $learnerIds, $this->plannedPeriodsPerWeek, $this->capacity, auth()->user())->count();
            } else {
                $createCourseOffering->create($subject, $academicYear, AcademicPeriod::inSchool()->findOrFail((int) $this->academicPeriodId), $academicLevel, $sectionIds, $rosterMode, $learnerIds, $this->plannedPeriodsPerWeek, $this->capacity, auth()->user());
                $created = 1;
            }
        } catch (InvalidValueException $exception) {
            $this->addError('subjectId', $exception->getMessage());

            return;
        }

        session()->flash('success', $created === 1 ? "{$subject->name} added for review." : "{$subject->name} added {$created} times for review.");

        $this->setup
            ? $this->redirectRoute('academic-years.setup', [$academicYear, 'subjects'])
            : $this->redirectRoute('course-offerings.index');
    }

    public function render(): View
    {
        $academicYears = AcademicYear::inSchool()->with('topLevelPeriods')->orderByDesc('start_year')->get();
        $selectedYear = $academicYears->firstWhere('id', $this->academicYearId);
        $selectedLevel = $this->selectedLevel();

        return view('livewire.create-course-offering', [
            'academicYears' => $academicYears,
            'selectedYear' => $selectedYear,
            'academicLevels' => AcademicLevel::inSchool()->orderBy('position')->orderBy('name')->get(),
            'selectedLevel' => $selectedLevel,
            'rosterModes' => $this->allowedRosterModes($selectedYear),
            'sections' => $this->sections($selectedLevel),
            'learners' => $this->rosterMode === RosterMode::IndividualRoster->value ? $this->learners($selectedLevel) : collect(),
            'subjects' => Subject::inSchool()->orderBy('name')->get(),
        ]);
    }

    private function selectedLevel(): ?AcademicLevel
    {
        return $this->academicLevelId === null ? null : AcademicLevel::inSchool()->find($this->academicLevelId);
    }

    /**
     * @return array<int, RosterMode>
     */
    private function allowedRosterModes(?AcademicYear $academicYear): array
    {
        return $academicYear instanceof AcademicYear ? instructional_model($academicYear)->rosterModes() : RosterMode::cases();
    }

    private function keepRosterModeAllowed(): void
    {
        $allowed = array_map(fn (RosterMode $mode): string => $mode->value, $this->allowedRosterModes(AcademicYear::inSchool()->find($this->academicYearId)));

        if (!in_array($this->rosterMode, $allowed, true)) {
            $this->rosterMode = $allowed[0] ?? RosterMode::HomeSection->value;
        }
    }

    /**
     * @return Collection<int, AcademicCycleSection>
     */
    private function sections(?AcademicLevel $academicLevel): Collection
    {
        if ($academicLevel === null || $this->academicYearId === null) {
            return new Collection;
        }

        return AcademicCycleSection::inSchool()
            ->where('academic_year_id', $this->academicYearId)
            ->where('academic_level_id', $academicLevel->id)
            ->where('status', '!=', AcademicStructureStatus::Archived)
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, StudentRecord>
     */
    private function learners(?AcademicLevel $academicLevel): Collection
    {
        if ($academicLevel === null || $this->academicYearId === null) {
            return new Collection;
        }

        return StudentRecord::inSchool()
            ->attending()
            ->with(['academicCycleSection:id,name,label', 'user:id,name'])
            ->whereHas('academicCycleSection', fn (Builder $query) => $query
                ->where('academic_year_id', $this->academicYearId)
                ->where('academic_level_id', $academicLevel->id))
            ->orderBy('admission_number')
            ->get();
    }
}
