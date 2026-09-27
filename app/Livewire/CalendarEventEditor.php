<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Enums\CalendarEventType;
use App\Enums\SchoolMembershipStatus;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\AcademicCycleSection;
use App\Models\CalendarEvent;
use App\Models\CalendarEventAudience;
use App\Models\User;
use App\Traits\ValidatesSchoolMembership;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Add a day to the school calendar, or change, publish and remove one.
 *
 * A new day is always a draft. Only a published holiday or closure shuts the
 * school for attendance and the timetable. Naming nobody makes the day for
 * the whole school.
 */
class CalendarEventEditor extends Component
{
    use DispatchesStatusNotifications;
    use ValidatesSchoolMembership;

    public ?CalendarEvent $event = null;

    public string $title = '';

    public string $type = '';

    public string $location = '';

    public string $description = '';

    public bool $isAllDay = true;

    public string $startsAt = '';

    public string $endsAt = '';

    /** @var list<int> */
    public array $sectionIds = [];

    /** @var list<int> */
    public array $userIds = [];

    public string $personSearch = '';

    public function mount(?CalendarEvent $event = null, ?string $day = null): void
    {
        if ($event?->exists) {
            Gate::authorize('update', $event);

            $this->event = $event;
            $this->title = $event->title;
            $this->type = $event->type->value;
            $this->location = (string) $event->location;
            $this->description = (string) $event->description;
            $this->isAllDay = $event->is_all_day;
            $this->startsAt = $event->starts_at->format($this->isAllDay ? 'Y-m-d' : 'Y-m-d\TH:i');
            $this->endsAt = $event->ends_at->format($this->isAllDay ? 'Y-m-d' : 'Y-m-d\TH:i');
            $this->sectionIds = $event->audiences->pluck('academic_cycle_section_id')->filter()->map(fn ($id): int => (int) $id)->values()->all();
            $this->userIds = $event->audiences->pluck('user_id')->filter()->map(fn ($id): int => (int) $id)->values()->all();

            return;
        }

        Gate::authorize('create', CalendarEvent::class);

        $this->event = null;
        $this->type = CalendarEventType::cases()[0]->value;
        $date = rescue(fn () => Carbon::parse((string) $day), now(), report: false);
        $this->startsAt = $date->format('Y-m-d');
        $this->endsAt = $date->format('Y-m-d');
    }

    /**
     * Keep the chosen days when the times are switched on or off.
     */
    public function updatedIsAllDay(): void
    {
        $this->startsAt = $this->reformat($this->startsAt, $this->isAllDay ? 'Y-m-d' : 'Y-m-d\T08:00');
        $this->endsAt = $this->reformat($this->endsAt, $this->isAllDay ? 'Y-m-d' : 'Y-m-d\T15:00');
    }

    public function addPerson(int $userId): void
    {
        if ($this->people()->whereKey($userId)->exists() && !in_array($userId, $this->userIds, true)) {
            $this->userIds[] = $userId;
        }

        $this->personSearch = '';
    }

    public function removePerson(int $userId): void
    {
        $this->userIds = array_values(array_filter($this->userIds, fn (int $id): bool => $id !== $userId));
    }

    public function save(): void
    {
        $this->event === null
            ? Gate::authorize('create', CalendarEvent::class)
            : Gate::authorize('update', $this->event);

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(CalendarEventType::class)],
            'description' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'startsAt' => ['required', 'date'],
            'endsAt' => ['required', 'date', 'after_or_equal:startsAt', function (string $attribute, mixed $value, \Closure $fail): void {
                if (Carbon::parse($this->startsAt)->addYear()->lt(Carbon::parse((string) $value))) {
                    $fail('A day on the calendar cannot last more than a year. Check the year.');
                }
            }],
            'sectionIds' => ['array'],
            'sectionIds.*' => ['integer', 'distinct', Rule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id())],
            'userIds' => ['array'],
            'userIds.*' => ['integer', 'distinct', $this->memberOfWorkingSchool()],
        ], [
            'endsAt.after_or_equal' => 'A day on the calendar cannot end before it starts.',
        ], [
            'startsAt' => 'start',
            'endsAt' => 'end',
            'sectionIds.*' => school_term('section', 'section'),
            'userIds.*' => 'person',
        ]);

        $startsAt = Carbon::parse($this->startsAt);
        $endsAt = Carbon::parse($this->endsAt);
        $attributes = [
            'title' => trim($this->title),
            'type' => CalendarEventType::from($this->type),
            'description' => trim($this->description) === '' ? null : trim($this->description),
            'location' => trim($this->location) === '' ? null : trim($this->location),
            'is_all_day' => $this->isAllDay,
            // An all-day event covers whole days, so it never starts halfway
            // through the morning.
            'starts_at' => $this->isAllDay ? $startsAt->startOfDay() : $startsAt,
            'ends_at' => $this->isAllDay ? $endsAt->endOfDay() : $endsAt,
        ];

        $event = DB::transaction(function () use ($attributes): CalendarEvent {
            if ($this->event === null) {
                $event = CalendarEvent::create([
                    'school_id' => current_school_id(),
                    'academic_year_id' => current_academic_year_id(),
                    'academic_period_id' => current_academic_period_id(),
                    'created_by' => auth()->id(),
                    ...$attributes,
                ]);
            } else {
                $event = $this->event;
                $event->update($attributes);
                $event->audiences()->delete();
            }

            foreach (array_unique($this->sectionIds) as $sectionId) {
                CalendarEventAudience::create(['calendar_event_id' => $event->id, 'academic_cycle_section_id' => (int) $sectionId]);
            }

            foreach (array_unique($this->userIds) as $userId) {
                CalendarEventAudience::create(['calendar_event_id' => $event->id, 'user_id' => (int) $userId]);
            }

            return $event;
        });

        if ($this->event === null) {
            session()->flash('success', 'Saved as a draft. Publish it when it is ready.');
            $this->redirectRoute('calendar-events.edit', $event);

            return;
        }

        $this->event = $event->refresh();
        $this->notify('Saved.');
    }

    public function publish(): void
    {
        $this->changePublication(true);
    }

    public function unpublish(): void
    {
        $this->changePublication(false);
    }

    public function remove(): void
    {
        abort_if($this->event === null, 404);
        Gate::authorize('delete', $this->event);

        $month = $this->event->starts_at->format('Y-m');
        $this->event->delete();

        session()->flash('success', 'Removed from the calendar.');
        $this->redirectRoute('calendar-events.index', ['month' => $month]);
    }

    public function render(): View
    {
        $search = trim($this->personSearch);

        return view('livewire.calendar-event-editor', [
            'types' => CalendarEventType::cases(),
            'sections' => $this->sections(),
            'chosenPeople' => User::query()->whereKey($this->userIds)->orderBy('name')->get(['id', 'name']),
            'matches' => mb_strlen($search) < 2 ? collect() : $this->people()
                ->where('name', 'like', '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%')
                ->whereKeyNot($this->userIds)
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name']),
        ]);
    }

    private function changePublication(bool $isPublished): void
    {
        abort_if($this->event === null, 404);
        Gate::authorize('publish', $this->event);

        $this->event->update(['is_published' => $isPublished]);

        $this->notify($isPublished
            ? 'Published. The school can read it now.'
            : 'A draft again, so nobody else sees it.');
    }

    /**
     * Get the people of this school who can be named.
     *
     * @return Builder<User>
     */
    private function people(): Builder
    {
        return User::query()->whereHas('schoolMemberships', fn (Builder $query) => $query
            ->where('school_id', current_school_id())
            ->where('status', SchoolMembershipStatus::Active));
    }

    /**
     * Get this year's home groups, and any the event already names.
     *
     * @return Collection<int, AcademicCycleSection>
     */
    private function sections(): Collection
    {
        return AcademicCycleSection::query()
            ->inSchool()
            ->where(fn (Builder $query) => $query
                ->where(fn (Builder $current) => $current
                    ->where('academic_year_id', current_academic_year_id())
                    ->where('status', '!=', AcademicStructureStatus::Archived))
                ->orWhereKey($this->sectionIds))
            ->with('academicLevel:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'label', 'academic_level_id']);
    }

    private function reformat(string $value, string $format): string
    {
        return rescue(fn () => Carbon::parse($value)->format($format), $value, report: false);
    }
}
