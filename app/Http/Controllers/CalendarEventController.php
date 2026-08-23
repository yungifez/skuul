<?php

namespace App\Http\Controllers;

use App\Enums\CalendarEventType;
use App\Http\Requests\StoreCalendarEventRequest;
use App\Http\Requests\UpdateCalendarEventRequest;
use App\Models\AcademicCycleSection;
use App\Models\CalendarEvent;
use App\Models\CalendarEventAudience;
use App\Models\User;
use App\Services\Calendar\SchoolCalendar;
use App\Traits\ReadsCalendarMonths;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * The school calendar: what is on, and whether the school is open.
 *
 * A draft is not a promise. Only a published event reaches the people it
 * names, and only a published holiday or closure shuts the school for
 * attendance and the timetable.
 */
class CalendarEventController extends Controller
{
    use ReadsCalendarMonths;

    public function __construct(private SchoolCalendar $calendar) {}

    /**
     * Show one month of the calendar.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CalendarEvent::class);

        $month = $this->monthFrom($request);
        $selectedType = CalendarEventType::tryFrom($request->string('type')->toString());
        $draftsOnly = $request->boolean('drafts');

        $events = CalendarEvent::query()
            ->inSchool()
            ->with(['audiences.academicCycleSection:id,name', 'audiences.user:id,name'])
            ->between($month->copy()->startOfMonth(), $month->copy()->endOfMonth())
            ->when(!$request->user()->can('update calendar event'), function (Builder $query): void {
                $query->published();
            })
            ->when($selectedType !== null, function (Builder $query) use ($selectedType): void {
                $query->where('type', $selectedType);
            })
            ->when($draftsOnly, function (Builder $query): void {
                $query->where('is_published', false);
            })
            ->orderBy('starts_at')
            ->get();

        return view('pages.calendar-event.index', [
            'events' => $events,
            'month' => $month,
            'days' => $this->daysOf($month),
            'types' => CalendarEventType::cases(),
            'selectedType' => $selectedType,
            'draftsOnly' => $draftsOnly,
            'closures' => $this->calendar->closures($month->copy()->startOfMonth(), $month->copy()->endOfMonth()),
            'draftCount' => CalendarEvent::query()->inSchool()->where('is_published', false)->count(),
        ]);
    }

    /**
     * Show the form that adds a day to the calendar.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', CalendarEvent::class);

        return view('pages.calendar-event.create', [
            'types' => CalendarEventType::cases(),
            'sections' => $this->sections(),
            'people' => $this->people(),
            'day' => $request->date('day') ?? now(),
        ]);
    }

    /**
     * Add a day to the calendar, as a draft.
     */
    public function store(StoreCalendarEventRequest $request): RedirectResponse
    {
        $event = DB::transaction(function () use ($request): CalendarEvent {
            $event = CalendarEvent::create([
                'school_id' => current_school_id(),
                'academic_year_id' => current_academic_year_id(),
                'academic_period_id' => current_academic_period_id(),
                'created_by' => $request->user()->id,
                ...$this->attributesFrom($request),
            ]);

            $this->saveAudience($event, $request);

            return $event;
        });

        return redirect()
            ->route('calendar-events.edit', $event)
            ->with('success', 'The event was saved as a draft. Publish it when it is ready.');
    }

    /**
     * Show one event and the form that changes it.
     */
    public function edit(CalendarEvent $calendarEvent): View
    {
        $this->authorize('view', $calendarEvent);

        $calendarEvent->load(['audiences.academicCycleSection:id,name', 'audiences.user:id,name', 'createdBy:id,name']);

        return view('pages.calendar-event.edit', [
            'event' => $calendarEvent,
            'types' => CalendarEventType::cases(),
            'sections' => $this->sections(),
            'people' => $this->people(),
        ]);
    }

    /**
     * Change an event.
     */
    public function update(UpdateCalendarEventRequest $request, CalendarEvent $calendarEvent): RedirectResponse
    {
        DB::transaction(function () use ($request, $calendarEvent): void {
            $calendarEvent->update($this->attributesFrom($request));

            $calendarEvent->audiences()->delete();
            $this->saveAudience($calendarEvent, $request);
        });

        return back()->with('success', 'The event was saved.');
    }

    /**
     * Put the event in front of the school, or take it back to a draft.
     */
    public function changePublication(Request $request, CalendarEvent $calendarEvent): RedirectResponse
    {
        $this->authorize('publish', $calendarEvent);

        $calendarEvent->update(['is_published' => $request->boolean('is_published')]);

        return back()->with('success', $calendarEvent->is_published
            ? 'The event is on the calendar the school reads.'
            : 'The event is a draft again, so nobody else sees it.');
    }

    /**
     * Take the event off the calendar.
     */
    public function destroy(CalendarEvent $calendarEvent): RedirectResponse
    {
        $this->authorize('delete', $calendarEvent);

        $month = $calendarEvent->starts_at->format('Y-m');
        $calendarEvent->delete();

        return redirect()
            ->route('calendar-events.index', ['month' => $month])
            ->with('success', 'The event was removed from the calendar.');
    }

    /**
     * Read the values a form sent, with the times an all-day event needs.
     *
     * @return array<string, mixed>
     */
    private function attributesFrom(StoreCalendarEventRequest|UpdateCalendarEventRequest $request): array
    {
        $isAllDay = $request->boolean('is_all_day');
        $startsAt = Carbon::parse($request->string('starts_at')->toString());
        $endsAt = Carbon::parse($request->string('ends_at')->toString());

        return [
            'title' => $request->string('title')->toString(),
            'type' => CalendarEventType::from($request->string('type')->toString()),
            'description' => $request->string('description')->toString() ?: null,
            'location' => $request->string('location')->toString() ?: null,
            'is_all_day' => $isAllDay,
            // An all-day event covers whole days, so the times never make it
            // start halfway through the morning.
            'starts_at' => $isAllDay ? $startsAt->startOfDay() : $startsAt,
            'ends_at' => $isAllDay ? $endsAt->endOfDay() : $endsAt,
        ];
    }

    /**
     * Save who the event is for.
     *
     * An event with no audience is for the whole school, so an empty list
     * writes no rows at all.
     */
    private function saveAudience(CalendarEvent $event, StoreCalendarEventRequest|UpdateCalendarEventRequest $request): void
    {
        /** @var array<int, int|string> $sectionIds */
        $sectionIds = $request->input('academic_cycle_section_ids', []);

        foreach ($sectionIds as $sectionId) {
            CalendarEventAudience::create([
                'calendar_event_id' => $event->id,
                'academic_cycle_section_id' => (int) $sectionId,
            ]);
        }

        /** @var array<int, int|string> $userIds */
        $userIds = $request->input('user_ids', []);

        foreach ($userIds as $userId) {
            CalendarEventAudience::create([
                'calendar_event_id' => $event->id,
                'user_id' => (int) $userId,
            ]);
        }
    }

    /**
     * Get the people an event can name.
     *
     * An appointment names people, and a family reads an event that names
     * their child, so learners belong on the list beside the staff.
     *
     * @return Collection<int, User>
     */
    private function people(): Collection
    {
        return User::query()
            ->whereHas('schoolMemberships', function (Builder $query): void {
                $query->where('school_id', current_school_id());
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Get the home groups an event can name.
     *
     * @return Collection<int, AcademicCycleSection>
     */
    private function sections(): Collection
    {
        return AcademicCycleSection::query()
            ->inSchool()
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
