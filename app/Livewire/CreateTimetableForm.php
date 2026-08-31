<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Enums\Role;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\Weekday;
use App\Services\Timetable\TimetableService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class CreateTimetableForm extends Component
{
    public string $name = '';

    public string $description = '';

    public ?int $academicPeriodId = null;

    public string $recurrence = 'weekly';

    public ?string $occursOn = null;

    public string $scope = 'section';

    public bool $canCreateSchoolwide = false;

    public ?int $academicCycleSectionId = null;

    /** @var array<int, array{weekday_id: int, start_time: string, stop_time: string, type: string, subject_id: int|null, title: string, audience_role: string}> */
    public array $events = [];

    /** @var array{weekday_id: int|null, start_time: string, stop_time: string, type: string, subject_id: int|null, title: string, audience_role: string} */
    public array $newEvent = [
        'weekday_id' => null,
        'start_time' => '08:00',
        'stop_time' => '09:00',
        'type' => 'subject',
        'subject_id' => null,
        'title' => '',
        'audience_role' => '',
    ];

    /** @var array<int, array{id: int, label: string}> */
    public array $cycleSections = [];

    /** @var array<int, array{id: int, name: string}> */
    public array $subjects = [];

    /** @var array<int, array{id: int, name: string}> */
    public array $weekdays = [];

    /** @var array<int, array{id: string, name: string}> */
    public array $roles = [];

    /** @var array<int, array{id: int, name: string, starts_on: string|null, ends_on: string|null}> */
    public array $periods = [];

    public function mount(): void
    {
        $this->authorize('create', Timetable::class);
        $this->canCreateSchoolwide = auth()->user()->can('create schoolwide timetable');

        $this->periods = AcademicPeriod::inSchool()
            ->where('academic_year_id', current_academic_year_id())
            ->topLevel()->ordered()->get(['id', 'name', 'starts_on', 'ends_on'])
            ->map(fn (AcademicPeriod $period): array => [
                'id' => $period->id,
                'name' => $period->displayName,
                'starts_on' => $period->starts_on?->toDateString(),
                'ends_on' => $period->ends_on?->toDateString(),
            ])->all();
        $this->academicPeriodId = current_academic_period_id() ?? $this->periods[0]['id'] ?? null;
        $this->occursOn = $this->selectedPeriod()['starts_on'] ?? now()->toDateString();
        $this->cycleSections = AcademicCycleSection::inSchool()
            ->with('academicLevel')->where('academic_year_id', current_academic_year_id())
            ->where('status', AcademicStructureStatus::Active)->orderBy('position')->orderBy('name')->get()
            ->map(fn (AcademicCycleSection $section): array => [
                'id' => $section->id,
                'label' => $section->academicLevel->name.' · '.($section->label ?? $section->name),
            ])->all();
        $this->academicCycleSectionId = $this->cycleSections[0]['id'] ?? null;
        $this->subjects = Subject::inSchool()->orderBy('name')->get(['id', 'name'])->toArray();
        $this->weekdays = Weekday::query()->orderBy('id')->get(['id', 'name'])->toArray();
        $this->newEvent['weekday_id'] = $this->weekdays[0]['id'] ?? null;
        $this->roles = collect(Role::cases())->reject(fn (Role $role): bool => $role->isSystemScoped())
            ->map(fn (Role $role): array => ['id' => $role->value, 'name' => $role->label()])->values()->all();
    }

    public function updatedScope(string $scope): void
    {
        if ($scope === 'schoolwide' && !$this->canCreateSchoolwide) {
            $this->scope = 'section';
            $this->addError('scope', 'You do not have permission to create a schoolwide timetable.');

            return;
        }

        if ($scope === 'section' && $this->academicCycleSectionId === null) {
            $this->academicCycleSectionId = $this->cycleSections[0]['id'] ?? null;
        }
    }

    public function updatedRecurrence(string $recurrence): void
    {
        if ($recurrence === 'one_time' && $this->occursOn === null) {
            $this->occursOn = $this->selectedPeriod()['starts_on'] ?? now()->toDateString();
        }
    }

    public function updatedAcademicPeriodId(): void
    {
        if ($this->recurrence === 'one_time') {
            $this->occursOn = $this->selectedPeriod()['starts_on'] ?? now()->toDateString();
        }
    }

    public function addEvent(): void
    {
        if ($this->recurrence === 'one_time') {
            $this->validateOccursOn();

            if ($this->getErrorBag()->has('occursOn')) {
                return;
            }

            $this->newEvent['weekday_id'] = $this->weekdayIdForDate();
        }

        Validator::make($this->newEvent, [
            'weekday_id' => ['required', 'integer', Rule::exists('weekdays', 'id')],
            'start_time' => ['required', 'date_format:H:i'],
            'stop_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'type' => ['required', 'in:subject,role,freehand'],
            'subject_id' => ['required_if:type,subject', 'nullable', 'integer', Rule::exists('subjects', 'id')->where('school_id', current_school_id())],
            'title' => ['required_if:type,role,freehand', 'nullable', 'string', 'max:255'],
            'audience_role' => ['required_if:type,role', 'nullable', Rule::in(array_column($this->roles, 'id'))],
        ])->validate();
        $this->events[] = [
            ...$this->newEvent,
            'weekday_id' => (int) $this->newEvent['weekday_id'],
            'subject_id' => $this->newEvent['type'] === 'subject' ? (int) $this->newEvent['subject_id'] : null,
            'audience_role' => $this->newEvent['type'] === 'role' ? $this->newEvent['audience_role'] : '',
        ];
        $this->newEvent['title'] = '';
        $this->newEvent['subject_id'] = null;
        $this->newEvent['audience_role'] = '';
    }

    public function removeEvent(int $index): void
    {
        unset($this->events[$index]);
        $this->events = array_values($this->events);
    }

    public function save(TimetableService $timetables): void
    {
        $this->authorize('create', Timetable::class);
        if ($this->scope === 'schoolwide') {
            $this->authorize('createSchoolwide', Timetable::class);
        }

        if ($this->recurrence === 'one_time') {
            $this->validateOccursOn();

            if ($this->getErrorBag()->has('occursOn')) {
                return;
            }
        }

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'academicPeriodId' => ['required', 'integer', Rule::exists('academic_periods', 'id')->where('school_id', current_school_id())->where('academic_year_id', current_academic_year_id())],
            'recurrence' => ['required', Rule::in(['weekly', 'one_time'])],
            'occursOn' => ['required_if:recurrence,one_time', 'nullable', 'date'],
            'scope' => ['required', 'in:section,schoolwide'],
            'academicCycleSectionId' => ['required_if:scope,section', 'nullable', 'integer', Rule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id())->where('academic_year_id', current_academic_year_id())],
        ]);
        foreach ($this->events as $event) {
            Validator::make($event, [
                'weekday_id' => ['required', 'integer', Rule::exists('weekdays', 'id')],
                'start_time' => ['required', 'date_format:H:i'],
                'stop_time' => ['required', 'date_format:H:i', 'after:start_time'],
                'type' => ['required', 'in:subject,role,freehand'],
                'subject_id' => ['required_if:type,subject', 'nullable', 'integer', Rule::exists('subjects', 'id')->where('school_id', current_school_id())],
                'title' => ['required_if:type,role,freehand', 'nullable', 'string', 'max:255'],
                'audience_role' => ['required_if:type,role', 'nullable', Rule::in(array_column($this->roles, 'id'))],
            ])->validate();
        }
        $timetable = $timetables->createTimetableWithEvents([
            'name' => $this->name,
            'description' => $this->description ?: null,
            'academic_period_id' => $this->academicPeriodId,
            'recurrence' => $this->recurrence,
            'occurs_on' => $this->recurrence === 'one_time' ? $this->occursOn : null,
            'academic_cycle_section_id' => $this->scope === 'section' ? $this->academicCycleSectionId : null,
        ], array_map(fn (array $event): array => [
            'weekday_id' => (int) $event['weekday_id'],
            'start_time' => $event['start_time'],
            'stop_time' => $event['stop_time'],
            'type' => $event['type'],
            'subject_id' => $event['subject_id'] ?? null,
            'title' => $event['title'] ?? null,
            'audience_role' => $event['audience_role'] ?: null,
        ], $this->events));
        $this->redirectRoute('timetables.manage', $timetable, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.create-timetable-form');
    }

    /**
     * @return array{id: int, name: string, starts_on: string|null, ends_on: string|null}|null
     */
    private function selectedPeriod(): ?array
    {
        return collect($this->periods)->firstWhere('id', $this->academicPeriodId);
    }

    private function validateOccursOn(): void
    {
        $this->resetErrorBag('occursOn');
        $period = $this->selectedPeriod();

        if ($this->occursOn === null || $period === null || $period['starts_on'] === null || $period['ends_on'] === null) {
            $this->addError('occursOn', 'Choose a date inside the selected academic period.');

            return;
        }

        $date = Carbon::parse($this->occursOn);

        if ($date->lt(Carbon::parse($period['starts_on'])) || $date->gt(Carbon::parse($period['ends_on']))) {
            $this->addError('occursOn', 'The date must be inside the selected academic period.');
        }
    }

    private function weekdayIdForDate(): ?int
    {
        if ($this->occursOn === null) {
            return null;
        }

        return Weekday::query()->where('name', Carbon::parse($this->occursOn)->englishDayOfWeek)->value('id');
    }
}
