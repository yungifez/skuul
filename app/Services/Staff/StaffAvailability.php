<?php

namespace App\Services\Staff;

use App\Models\StaffLeaveRequest;
use App\Models\StaffProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * Answer whether a member of staff can be given work at a time.
 *
 * Three things stop them: they do not work here any more, they are away, or
 * the time falls outside the hours they said they can work. A person who
 * lists no hours is treated as free, because a school should not have to fill
 * in a timetable of hours before it can plan.
 */
class StaffAvailability
{
    /**
     * Check if the person can take work at the given time.
     */
    public function isFree(StaffProfile $profile, mixed $date, ?string $startsAt = null, ?string $endsAt = null): bool
    {
        if (!$profile->status->canBeGivenWork()) {
            return false;
        }

        $day = Carbon::parse($date)->startOfDay();

        if ($this->isAway($profile, $day)) {
            return false;
        }

        if ($startsAt === null || $endsAt === null) {
            return true;
        }

        $hours = $profile->availabilities()->where('day_of_week', $day->dayOfWeekIso)->get();

        if ($hours->isEmpty()) {
            return $profile->availabilities()->count() === 0;
        }

        return $hours->contains(fn ($block): bool => $block->covers($startsAt, $endsAt));
    }

    /**
     * Check if the person is away on the given day.
     */
    public function isAway(StaffProfile $profile, mixed $date): bool
    {
        return StaffLeaveRequest::query()
            ->where('staff_profile_id', $profile->id)
            ->holding()
            ->overlapping($date, $date)
            ->exists();
    }

    /**
     * Get the people away on the given day.
     *
     * @return Collection<int, StaffProfile>
     */
    public function awayOn(mixed $date): Collection
    {
        return StaffProfile::query()->inSchool()->awayOn($date)->get();
    }

    /**
     * Get the people who can take work on the given day.
     *
     * @return Collection<int, StaffProfile>
     */
    public function freeOn(mixed $date, ?string $startsAt = null, ?string $endsAt = null): Collection
    {
        return StaffProfile::query()
            ->inSchool()
            ->employed()
            ->get()
            ->filter(fn (StaffProfile $profile): bool => $this->isFree($profile, $date, $startsAt, $endsAt))
            ->values();
    }
}
