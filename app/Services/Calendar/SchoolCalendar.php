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
     * Get the published events one enrollment reads.
     *
     * The enrollment names its own school. A family holds no working school,
     * so the portal cannot use `inSchool()` here.
     *
     * @return Collection<int, CalendarEvent>
     */
    public function forEnrollment(StudentRecord $enrollment, DateTimeInterface|string $from, DateTimeInterface|string $to): Collection
    {
        $query = CalendarEvent::query()
            ->where('school_id', $enrollment->school_id)
            ->published()
            ->between($from, $to);

        $this->limitToAudience($query, $enrollment->user_id, $enrollment->academic_cycle_section_id);

        return $query->orderBy('starts_at')->get();
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

        $this->limitToAudience($query, $person->id, $enrollment?->academic_cycle_section_id);

        return $query;
    }

    /**
     * Keep only the events a reader of this shape is part of.
     *
     * An event with no audience is for the whole school. An event that names
     * an audience reaches the reader only when it names them or their home
     * group.
     *
     * The names are grouped inside their own closure on purpose. An `or`
     * written straight into a `whereHas` closure sits beside the relation's
     * own join condition, and `and` binds tighter than `or`, so the name
     * escapes the join and matches audience rows of every other event.
     *
     * @param  Builder<CalendarEvent>  $query
     */
    private function limitToAudience(Builder $query, ?int $userId, ?int $sectionId): void
    {
        if ($userId === null && $sectionId === null) {
            $query->whereDoesntHave('audiences');

            return;
        }

        $query->where(function (Builder $query) use ($userId, $sectionId): void {
            $query->whereDoesntHave('audiences')
                ->orWhereHas('audiences', function (Builder $audience) use ($userId, $sectionId): void {
                    $audience->where(function (Builder $named) use ($userId, $sectionId): void {
                        if ($userId !== null) {
                            $named->orWhere('user_id', $userId);
                        }

                        if ($sectionId !== null) {
                            $named->orWhere('academic_cycle_section_id', $sectionId);
                        }
                    });
                });
        });
    }
}
