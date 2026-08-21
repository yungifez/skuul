<?php

namespace App\Services\Calendar;

use App\Enums\CalendarEventType;
use App\Models\CalendarEvent;
use App\Models\StudentRecord;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * Read the school calendar.
 *
 * The calendar answers two everyday questions: what is on, and is the school
 * even open. Attendance and timetables both need the second one.
 */
class SchoolCalendar
{
    /**
     * Get the published events between two days.
     *
     * @return Collection<int, CalendarEvent>
     */
    public function between(DateTimeInterface|string $from, DateTimeInterface|string $to, ?User $person = null): Collection
    {
        return CalendarEvent::query()
            ->inSchool()
            ->published()
            ->between($from, $to)
            ->when($person !== null, fn (Builder $query) => $this->limitToPerson($query, $person))
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * Check if the school teaches on the given day.
     */
    public function isTeachingDay(DateTimeInterface|string|null $date = null): bool
    {
        $day = Carbon::parse($date ?? now());

        return !CalendarEvent::query()
            ->inSchool()
            ->published()
            ->covering($day)
            ->whereIn('type', [CalendarEventType::Holiday, CalendarEventType::Closure])
            ->exists();
    }

    /**
     * Get the days the school is shut between two days.
     *
     * @return Collection<int, CalendarEvent>
     */
    public function closures(DateTimeInterface|string $from, DateTimeInterface|string $to): Collection
    {
        return CalendarEvent::query()
            ->inSchool()
            ->published()
            ->between($from, $to)
            ->whereIn('type', [CalendarEventType::Holiday, CalendarEventType::Closure])
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * Keep only the events a person is part of.
     *
     * @param Builder<CalendarEvent> $query
     *
     * @return Builder<CalendarEvent>
     */
    private function limitToPerson(Builder $query, User $person): Builder
    {
        /** @var StudentRecord|null $enrollment */
        $enrollment = $person->studentRecord;

        return $query->where(function (Builder $query) use ($person, $enrollment): void {
            // An event with no audience is for the whole school.
            $query->whereDoesntHave('audiences')
                ->orWhereHas('audiences', function (Builder $audience) use ($person, $enrollment): void {
                    $audience->where('user_id', $person->id);

                    if ($enrollment !== null) {
                        $audience->orWhere('my_class_id', $enrollment->my_class_id)
                            ->orWhere('section_id', $enrollment->section_id);
                    }
                });
        });
    }
}
