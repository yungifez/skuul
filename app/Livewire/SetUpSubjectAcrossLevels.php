<?php

namespace App\Livewire;

use App\Actions\Curriculum\CreateCourseOfferingsForLevels;
use App\Enums\AcademicStructureStatus;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Subject;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Add one subject to several classes or groups of a school year at once.
 *
 * Each chosen class gets its own offering with its own roster, periods a
 * week, and capacity. A group is always taught to everyone in it.
 */
class SetUpSubjectAcrossLevels extends Component
{
    public AcademicYear $academicYear;

    public ?int $subjectId = null;

    public string $academicPeriodId = '';

    /** @var array<int, int|string> */
    public array $levelIds = [];

    /**
     * The settings for each chosen class, keyed by its id.
     *
     * @var array<int|string, array{roster_mode: string, section_ids: array<int, int|string>, planned_periods_per_week: int|string|null, capacity: int|string|null}>
     */
    public array $configurations = [];

    public bool $setup = false;

    public function mount(AcademicYear $academicYear, ?int $subjectId = null, bool $setup = false): void
    {
        Gate::authorize('create', CourseOffering::class);

        abort_unless($academicYear->school_id === current_school_id(), 404);

        $this->subjectId = Subject::inSchool()->whereKey($subjectId)->value('id');
        $this->setup = $setup;
    }

    public function updatedLevelIds(): void
    {
        $levels = AcademicLevel::inSchool()->whereKey($this->levelIds)->get()->keyBy('id');

        foreach ($levels as $level) {
            $this->configurations[$level->id] ??= [
                'roster_mode' => $level->is_group ? RosterMode::AcademicLevel->value : RosterMode::HomeSection->value,
                'section_ids' => [],
                'planned_periods_per_week' => null,
                'capacity' => null,
            ];
        }
    }

    public function save(CreateCourseOfferingsForLevels $createCourseOfferingsForLevels): void
    {
        Gate::authorize('create', CourseOffering::class);

        $inSchool = fn (string $table) => Rule::exists($table, 'id')->where('school_id', current_school_id());
        $allowedModes = array_map(fn (RosterMode $mode): string => $mode->value, $this->rosterModes());

        $this->validate([
            'subjectId' => ['required', 'integer', $inSchool('subjects')],
            'academicPeriodId' => ['required', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value !== 'all' && !AcademicPeriod::inSchool()->whereKey((int) $value)->where('academic_year_id', $this->academicYear->id)->exists()) {
                    $fail('Choose a period of this year.');
                }
            }],
            'levelIds' => ['required', 'array', 'min:1'],
            'levelIds.*' => ['integer', 'distinct', $inSchool('academic_levels')],
            'configurations.*.roster_mode' => ['required', Rule::in([...$allowedModes, RosterMode::AcademicLevel->value])],
            'configurations.*.section_ids' => ['array'],
            'configurations.*.section_ids.*' => ['integer', 'distinct', $inSchool('academic_cycle_sections')],
            'configurations.*.planned_periods_per_week' => ['nullable', 'integer', 'min:1', 'max:80'],
            'configurations.*.capacity' => ['nullable', 'integer', 'min:1', 'max:5000'],
        ], [
            'subjectId.required' => 'Choose a subject.',
            'academicPeriodId.required' => 'Choose a period.',
            'levelIds.required' => 'Choose at least one class or group.',
            'configurations.*.roster_mode.in' => 'This school year is not taught that way.',
        ], [
            'configurations.*.planned_periods_per_week' => 'periods a week',
            'configurations.*.capacity' => 'capacity',
        ]);

        $levels = AcademicLevel::inSchool()->whereKey($this->levelIds)->get()->keyBy('id');
        $plan = [];

        foreach ($this->levelIds as $levelId) {
            $level = $levels->get((int) $levelId);
            $configuration = $this->configurations[$levelId] ?? [];

            $plan[] = [
                'academic_level_id' => (int) $levelId,
                'roster_mode' => $level?->is_group ? RosterMode::AcademicLevel->value : ($configuration['roster_mode'] ?? RosterMode::HomeSection->value),
                'academic_cycle_section_ids' => array_map('intval', $configuration['section_ids'] ?? []),
                'planned_periods_per_week' => $this->wholeNumber($configuration['planned_periods_per_week'] ?? null),
                'capacity' => $this->wholeNumber($configuration['capacity'] ?? null),
            ];
        }

        $subject = Subject::inSchool()->findOrFail($this->subjectId);

        try {
            $created = $createCourseOfferingsForLevels->create($subject, $this->academicYear, $this->academicPeriodId, $plan, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('levelIds', $exception->getMessage());

            return;
        }

        session()->flash('success', $created->count() === 1
            ? "{$subject->name} added once for review."
            : "{$subject->name} added {$created->count()} times for review.");

        $this->setup
            ? $this->redirectRoute('course-offerings.bulk-create', ['academic_year_id' => $this->academicYear->id, 'setup' => 1])
            : $this->redirectRoute('course-offerings.index');
    }

    public function render(): View
    {
        $this->academicYear->loadMissing('topLevelPeriods');
        $levels = AcademicLevel::inSchool()->orderBy('position')->orderBy('name')->get();

        return view('livewire.set-up-subject-across-levels', [
            'subjects' => Subject::inSchool()->orderBy('name')->get(),
            'classes' => $levels->where('is_group', false),
            'groups' => $levels->where('is_group', true),
            'chosenLevels' => $levels->whereIn('id', array_map('intval', $this->levelIds)),
            'rosterModes' => $this->rosterModes(),
            'sectionsByLevel' => AcademicCycleSection::inSchool()
                ->where('academic_year_id', $this->academicYear->id)
                ->where('status', '!=', AcademicStructureStatus::Archived)
                ->orderBy('position')
                ->orderBy('name')
                ->get()
                ->groupBy('academic_level_id'),
        ]);
    }

    /**
     * @return array<int, RosterMode>
     */
    private function rosterModes(): array
    {
        return array_values(array_filter(
            instructional_model($this->academicYear)->rosterModes(),
            fn (RosterMode $mode): bool => $mode !== RosterMode::IndividualRoster,
        ));
    }

    private function wholeNumber(int|string|null $value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }
}
