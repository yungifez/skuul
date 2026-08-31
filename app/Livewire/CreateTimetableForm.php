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

    public string $scope = 'section';

    public string $calendarView = 'month';

    public string $calendarDate = '';

    public int $eventStep = 1;

    public bool $showEventDialog = false;

    public bool $canCreateSchoolwide = false;

    public ?int $academicCycleSectionId = null;

    /** @var array<int, array{weekday_id: int, weekday_ids: array<int, int>, start_time: string, stop_time: string, recurrence: string, occurs_on: string|null, starts_on: string|null, recurrence_interval: int, recurrence_weekdays: array<int, int>, type: string, subject_id: int|null, title: string, audience_role: string}> */
    public array $events = [];

    /** @var array{weekday_id: int|null, weekday_ids: array<int, int>, start_time: string, stop_time: string, recurrence: string, occurs_on: string|null, starts_on: string|null, recurrence_interval: int, recurrence_weekdays: array<int, int>, type: string, subject_id: int|null, title: string, audience_role: string} */
    public array $newEvent = [
        'weekday_id' => null,
        'weekday_ids' => [],
        'start_time' => '08:00',
        'stop_time' => '09:00',
        'recurrence' => 'weekly',
        'occurs_on' => null,
        'starts_on' => null,
        'recurrence_interval' => 1,
        'recurrence_weekdays' => [],
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
        $this->newEvent['occurs_on'] = $this->selectedPeriod()['starts_on'] ?? now()->toDateString();
        $this->newEvent['starts_on'] = $this->newEvent['occurs_on'];
        $this->calendarDate = $this->selectedPeriod()['starts_on'] ?? now()->toDateString();
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
        $this->newEvent['weekday_ids'] = $this->newEvent['weekday_id'] === null ? [] : [(int) $this->newEvent['weekday_id']];
        $this->newEvent['recurrence_weekdays'] = $this->newEvent['weekday_ids'];
        $this->roles = collect(Role::cases())->reject(fn (Role $role): bool => $role->isSystemScoped())
            ->map(fn (Role $role): array => ['id' => $role->value, 'name' => $role->label()])->values()->all();
    }

    public function updatedScope(string $scope): void
    {
        if ($scope === 'schoolwide' && !auth()->user()->can('create schoolwide timetable')) {
            $this->scope = 'section';
            $this->addError('scope', 'You do not have permission to create a schoolwide timetable.');

            return;
        }

        if ($scope === 'section' && $this->academicCycleSectionId === null) {
            $this->academicCycleSectionId = $this->cycleSections[0]['id'] ?? null;
        }
    }

    public function updatedNewEventRecurrence(string $recurrence): void
    {
        if ($recurrence === 'one_time' && $this->newEvent['occurs_on'] === null) {
            $this->newEvent['occurs_on'] = $this->selectedPeriod()['starts_on'] ?? now()->toDateString();
        }

        if ($recurrence !== 'one_time' && $this->newEvent['starts_on'] === null) {
            $this->newEvent['starts_on'] = $this->selectedPeriod()['starts_on'] ?? now()->toDateString();
        }

        if ($recurrence === 'weekly' && $this->newEvent['weekday_ids'] === []) {
            $this->newEvent['weekday_ids'] = [(int) $this->weekdayIdForDate($this->newEvent['starts_on'])];
        }

        if ($recurrence === 'monthly') {
            $this->newEvent['weekday_ids'] = [(int) $this->weekdayIdForDate($this->newEvent['starts_on'])];
        }
    }

    public function updatedAcademicPeriodId(): void
    {
        $this->calendarDate = $this->selectedPeriod()['starts_on'] ?? now()->toDateString();

        if ($this->newEvent['recurrence'] === 'one_time') {
            $this->newEvent['occurs_on'] = $this->selectedPeriod()['starts_on'] ?? now()->toDateString();
        }

        $this->newEvent['starts_on'] = $this->selectedPeriod()['starts_on'] ?? now()->toDateString();
    }

    public function updatedNewEventStartsOn(?string $date): void
    {
        if ($this->newEvent['recurrence'] === 'weekly' && $date !== null && $date !== '') {
            $this->newEvent['weekday_ids'] = [(int) $this->weekdayIdForDate($date)];
        }

        if ($this->newEvent['recurrence'] === 'monthly' && $date !== null && $date !== '') {
            $this->newEvent['weekday_ids'] = [(int) $this->weekdayIdForDate($date)];
        }
    }

    public function setCalendarView(string $view): void
    {
        if (in_array($view, ['week', 'month'], true)) {
            $this->calendarView = $view;
        }
    }

    public function moveCalendar(int $direction): void
    {
        $date = Carbon::parse($this->calendarDate);
        $this->calendarDate = ($this->calendarView === 'month'
            ? $date->addMonths($direction)
            : $date->addWeeks($direction)
        )->toDateString();
    }

    public function goToCalendarToday(): void
    {
        $this->calendarDate = now()->toDateString();
    }

    public function chooseCalendarDate(string $date): void
    {
        $this->calendarDate = Carbon::parse($date)->toDateString();
        $this->newEvent['recurrence'] = 'one_time';
        $this->newEvent['occurs_on'] = $this->calendarDate;
        $this->newEvent['starts_on'] = null;
        $this->newEvent['weekday_id'] = $this->weekdayIdForDate($this->calendarDate);
        $this->newEvent['weekday_ids'] = $this->newEvent['weekday_id'] === null ? [] : [(int) $this->newEvent['weekday_id']];
    }

    public function openEventDialog(?string $date = null): void
    {
        $this->resetErrorBag();

        if ($date !== null) {
            $this->chooseCalendarDate($date);
        }

        $this->eventStep = 1;
        $this->showEventDialog = true;
    }

    public function closeEventDialog(): void
    {
        $this->showEventDialog = false;
        $this->eventStep = 1;
    }

    public function chooseEventType(string $type): void
    {
        if (!in_array($type, ['subject', 'role', 'freehand'], true)) {
            return;
        }

        $this->newEvent['type'] = $type;
        $this->newEvent['subject_id'] = null;
        $this->newEvent['title'] = '';
        $this->newEvent['audience_role'] = '';
        $this->eventStep = 2;
    }

    public function continueEventSchedule(): void
    {
        $this->resetErrorBag();

        $this->validate([
            'newEvent.start_time' => ['required', 'date_format:H:i'],
            'newEvent.stop_time' => ['required', 'date_format:H:i', 'after:newEvent.start_time'],
            'newEvent.recurrence' => ['required', Rule::in(['weekly', 'monthly', 'one_time'])],
            'newEvent.occurs_on' => ['required_if:newEvent.recurrence,one_time', 'nullable', 'date'],
            'newEvent.starts_on' => ['required_unless:newEvent.recurrence,one_time', 'nullable', 'date'],
            'newEvent.recurrence_interval' => ['required_unless:newEvent.recurrence,one_time', 'integer', 'min:1', 'max:52'],
            'newEvent.weekday_ids' => ['required_if:newEvent.recurrence,weekly', 'array', 'min:1'],
            'newEvent.weekday_ids.*' => ['integer', Rule::exists('weekdays', 'id')],
        ]);

        if ($this->newEvent['recurrence'] === 'one_time') {
            $this->validateEventDate($this->newEvent['occurs_on'], 'newEvent.occurs_on');
        } else {
            $this->validateEventDate($this->newEvent['starts_on'], 'newEvent.starts_on');
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $this->eventStep = 3;
    }

    public function backEventStep(): void
    {
        $this->eventStep = max(1, $this->eventStep - 1);
    }

    public function addEvent(): void
    {
        $this->resetErrorBag();

        $validator = Validator::make($this->newEvent, [
            'weekday_id' => ['nullable', 'integer', Rule::exists('weekdays', 'id')],
            'weekday_ids' => ['required_if:recurrence,weekly', 'array', 'min:1'],
            'weekday_ids.*' => ['integer', Rule::exists('weekdays', 'id')],
            'start_time' => ['required', 'date_format:H:i'],
            'stop_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'recurrence' => ['required', Rule::in(['weekly', 'monthly', 'one_time'])],
            'occurs_on' => ['required_if:recurrence,one_time', 'nullable', 'date'],
            'starts_on' => ['required_unless:recurrence,one_time', 'nullable', 'date'],
            'recurrence_interval' => ['required_unless:recurrence,one_time', 'integer', 'min:1', 'max:52'],
            'type' => ['required', 'in:subject,role,freehand'],
            'subject_id' => ['required_if:type,subject', 'nullable', 'integer', Rule::exists('subjects', 'id')->where('school_id', current_school_id())],
            'title' => ['required_if:type,role,freehand', 'nullable', 'string', 'max:255'],
            'audience_role' => ['required_if:type,role', 'nullable', Rule::in(array_column($this->roles, 'id'))],
        ]);
        $validator->validate();

        $weekdayIds = array_values(array_unique(array_map('intval', $this->newEvent['weekday_ids'] ?: (array) $this->newEvent['weekday_id'])));

        if ($this->newEvent['recurrence'] === 'weekly' && $weekdayIds === []) {
            $this->addError('newEvent.weekday_ids', 'Choose at least one weekday.');

            return;
        }

        if ($this->getErrorBag()->has('newEvent.occurs_on')) {
            return;
        }

        if ($this->newEvent['recurrence'] === 'one_time') {
            $this->validateEventDate($this->newEvent['occurs_on'], 'newEvent.occurs_on');

            if ($this->getErrorBag()->has('newEvent.occurs_on')) {
                return;
            }

            $this->newEvent['weekday_id'] = $this->weekdayIdForDate();
            $weekdayIds = $this->newEvent['weekday_id'] === null ? [] : [(int) $this->newEvent['weekday_id']];
        } else {
            $this->validateEventDate($this->newEvent['starts_on'], 'newEvent.starts_on');

            if ($this->getErrorBag()->has('newEvent.starts_on')) {
                return;
            }

            if ($this->newEvent['recurrence'] === 'monthly') {
                $weekdayIds = [(int) $this->weekdayIdForDate($this->newEvent['starts_on'])];
            }
        }

        $this->events[] = [
            ...$this->newEvent,
            'weekday_id' => (int) ($weekdayIds[0] ?? $this->weekdayIdForDate()),
            'weekday_ids' => $weekdayIds,
            'subject_id' => $this->newEvent['type'] === 'subject' ? (int) $this->newEvent['subject_id'] : null,
            'audience_role' => $this->newEvent['type'] === 'role' ? $this->newEvent['audience_role'] : '',
        ];
        $this->newEvent['title'] = '';
        $this->newEvent['subject_id'] = null;
        $this->newEvent['audience_role'] = '';
        $this->newEvent['weekday_ids'] = $weekdayIds;
        $this->newEvent['occurs_on'] = $this->newEvent['recurrence'] === 'one_time'
            ? ($this->selectedPeriod()['starts_on'] ?? now()->toDateString())
            : null;
        $this->newEvent['starts_on'] = $this->newEvent['recurrence'] === 'one_time'
            ? null
            : $this->newEvent['starts_on'];
        $this->newEvent['recurrence_weekdays'] = $this->newEvent['weekday_ids'];
        $this->eventStep = 1;
        $this->showEventDialog = false;
    }

    public function eventOccursOn(array $event, Carbon $date): bool
    {
        if ($event['recurrence'] === 'one_time') {
            return $event['occurs_on'] === $date->toDateString();
        }

        if (($event['starts_on'] ?? null) !== null && $date->lt(Carbon::parse($event['starts_on']))) {
            return false;
        }

        if ($event['recurrence'] === 'monthly') {
            return ($event['starts_on'] ?? null) !== null
                && $date->day === Carbon::parse($event['starts_on'])->day
                && Carbon::parse($event['starts_on'])->startOfMonth()->diffInMonths($date->startOfMonth()) % max(1, (int) ($event['recurrence_interval'] ?? 1)) === 0;
        }

        $weekdayId = $this->weekdayIdForDate($date->toDateString());
        $weekdays = $event['weekday_ids'] ?? [$event['weekday_id']];
        $startsOn = ($event['starts_on'] ?? null) === null ? null : Carbon::parse($event['starts_on']);
        $weeks = $startsOn === null ? 0 : $startsOn->startOfWeek(Carbon::MONDAY)->diffInWeeks($date->startOfWeek(Carbon::MONDAY));

        return in_array($weekdayId, $weekdays, true)
            && $weeks % max(1, (int) ($event['recurrence_interval'] ?? 1)) === 0;
    }

    public function eventDraftRuleLabel(): string
    {
        if ($this->newEvent['recurrence'] === 'one_time') {
            return 'One date'.($this->newEvent['occurs_on'] === null ? '' : ' · '.Carbon::parse($this->newEvent['occurs_on'])->format('j M Y'));
        }

        $unit = $this->newEvent['recurrence'] === 'monthly' ? 'month' : 'week';
        $interval = max(1, (int) $this->newEvent['recurrence_interval']);
        $frequency = 'Every '.($interval === 1 ? '' : $interval.' ').$unit.($interval === 1 ? '' : 's');
        $weekdayNames = collect($this->weekdays)->whereIn('id', $this->newEvent['weekday_ids'])
            ->pluck('name')->implode(', ');

        if ($this->newEvent['recurrence'] === 'weekly') {
            $frequency .= ' on '.($weekdayNames ?: 'selected weekdays');
        } elseif ($this->newEvent['starts_on'] !== null) {
            $frequency .= ' on the '.Carbon::parse($this->newEvent['starts_on'])->format('jS');
        }

        return $frequency.' from '.($this->newEvent['starts_on'] === null ? 'the start date' : Carbon::parse($this->newEvent['starts_on'])->format('j M Y'));
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

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'academicPeriodId' => ['required', 'integer', Rule::exists('academic_periods', 'id')->where('school_id', current_school_id())->where('academic_year_id', current_academic_year_id())],
            'scope' => ['required', 'in:section,schoolwide'],
            'academicCycleSectionId' => ['required_if:scope,section', 'nullable', 'integer', Rule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id())->where('academic_year_id', current_academic_year_id())],
        ]);
        foreach ($this->events as $index => $event) {
            Validator::make($event, [
                'weekday_id' => ['nullable', 'integer', Rule::exists('weekdays', 'id')],
                'weekday_ids' => ['required_if:recurrence,weekly', 'array', 'min:1'],
                'weekday_ids.*' => ['integer', Rule::exists('weekdays', 'id')],
                'start_time' => ['required', 'date_format:H:i'],
                'stop_time' => ['required', 'date_format:H:i', 'after:start_time'],
                'recurrence' => ['required', Rule::in(['weekly', 'monthly', 'one_time'])],
                'occurs_on' => ['required_if:recurrence,one_time', 'nullable', 'date'],
                'starts_on' => ['required_unless:recurrence,one_time', 'nullable', 'date'],
                'recurrence_interval' => ['required_unless:recurrence,one_time', 'integer', 'min:1', 'max:52'],
                'type' => ['required', 'in:subject,role,freehand'],
                'subject_id' => ['required_if:type,subject', 'nullable', 'integer', Rule::exists('subjects', 'id')->where('school_id', current_school_id())],
                'title' => ['required_if:type,role,freehand', 'nullable', 'string', 'max:255'],
                'audience_role' => ['required_if:type,role', 'nullable', Rule::in(array_column($this->roles, 'id'))],
            ])->validate();

            if ($event['recurrence'] === 'one_time') {
                $this->validateEventDate($event['occurs_on'], 'events.'.$index.'.occurs_on');

                if ($this->getErrorBag()->has('events.'.$index.'.occurs_on')) {
                    return;
                }
            } else {
                $this->validateEventDate($event['starts_on'], 'events.'.$index.'.starts_on');

                if ($this->getErrorBag()->has('events.'.$index.'.starts_on')) {
                    return;
                }
            }
        }
        $expandedEvents = [];

        foreach ($this->events as $event) {
            $weekdayIds = array_values(array_unique(array_map('intval', $event['weekday_ids'] ?: [$event['weekday_id']])));

            foreach ($weekdayIds as $weekdayId) {
                $expandedEvents[] = [
                    'weekday_id' => $event['recurrence'] === 'one_time'
                        ? (int) $this->weekdayIdForDate($event['occurs_on'])
                        : $weekdayId,
                    'start_time' => $event['start_time'],
                    'stop_time' => $event['stop_time'],
                    'recurrence' => $event['recurrence'],
                    'occurs_on' => $event['recurrence'] === 'one_time' ? $event['occurs_on'] : null,
                    'starts_on' => $event['recurrence'] === 'one_time' ? null : $event['starts_on'],
                    'recurrence_interval' => $event['recurrence'] === 'one_time' ? 1 : $event['recurrence_interval'],
                    'recurrence_weekdays' => $event['recurrence'] === 'weekly' ? $weekdayIds : null,
                    'type' => $event['type'],
                    'subject_id' => $event['subject_id'] ?? null,
                    'title' => $event['title'],
                    'audience_role' => $event['audience_role'] ?: null,
                ];
            }
        }

        $timetable = $timetables->createTimetableWithEvents([
            'name' => $this->name,
            'description' => $this->description ?: null,
            'academic_period_id' => $this->academicPeriodId,
            'academic_cycle_section_id' => $this->scope === 'section' ? $this->academicCycleSectionId : null,
        ], $expandedEvents);
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

    private function validateEventDate(?string $date, string $attribute): void
    {
        $this->resetErrorBag($attribute);
        $period = $this->selectedPeriod();

        if ($date === null || $period === null) {
            $this->addError($attribute, 'Choose a date inside the selected academic period.');

            return;
        }

        if ($period['starts_on'] === null || $period['ends_on'] === null) {
            return;
        }

        $date = Carbon::parse($date);

        if ($date->lt(Carbon::parse($period['starts_on'])) || $date->gt(Carbon::parse($period['ends_on']))) {
            $this->addError($attribute, 'The date must be inside the selected academic period.');
        }
    }

    private function weekdayIdForDate(?string $date = null): ?int
    {
        $date ??= $this->newEvent['occurs_on'];

        if ($date === null) {
            return null;
        }

        return Weekday::query()->where('name', Carbon::parse($date)->englishDayOfWeek)->value('id');
    }
}
