<?php

namespace App\Policies;

use App\Enums\TimetableStatus;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimetablePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read timetable')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Timetable $timetable)
    {
        if ($user->can('read timetable') && $this->belongsToWorkingSchool($timetable)) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('create timetable')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create a schoolwide timetable.
     */
    public function createSchoolwide(User $user): bool
    {
        return $user->can('create timetable') && $user->can('create schoolwide timetable');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Timetable $timetable)
    {
        if ($user->can('update timetable')
            && $timetable->acceptsChanges()
            && $timetable->academicPeriod->isOpen()
            && $timetable->academicPeriod->academicYear->isOpen()
            && $this->belongsToWorkingSchool($timetable)
        ) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Timetable $timetable)
    {
        if ($user->can('delete timetable')
            && !$timetable->isPublished()
            && $this->belongsToWorkingSchool($timetable)
        ) {
            return true;
        }
    }

    /**
     * Determine whether the user can publish the timetable.
     */
    public function publish(User $user, Timetable $timetable): ?bool
    {
        if ($user->can('update timetable')
            && $timetable->status === TimetableStatus::Draft
            && $timetable->academicPeriod->isOpen()
            && $timetable->academicPeriod->academicYear->isOpen()
            && $this->belongsToWorkingSchool($timetable)
        ) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can start a timetable revision.
     */
    public function revise(User $user, Timetable $timetable): ?bool
    {
        if ($user->can('update timetable')
            && $timetable->status === TimetableStatus::Published
            && $timetable->academicPeriod->isOpen()
            && $timetable->academicPeriod->academicYear->isOpen()
            && $this->belongsToWorkingSchool($timetable)
        ) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can create a section-specific draft from a published template.
     */
    public function override(User $user, Timetable $timetable): ?bool
    {
        return $this->canManagePublishedSchedule($user, $timetable);
    }

    /**
     * Determine whether the user can record a date-specific teacher replacement.
     */
    public function substitute(User $user, Timetable $timetable): ?bool
    {
        return $this->canManagePublishedSchedule($user, $timetable);
    }

    /**
     * Check shared authority for work alongside a published timetable.
     */
    private function canManagePublishedSchedule(User $user, Timetable $timetable): ?bool
    {
        if ($user->can('update timetable')
            && $timetable->status === TimetableStatus::Published
            && $timetable->academicPeriod->acceptsNewWork()
            && $timetable->academicPeriod->academicYear->acceptsNewWork()
            && $this->belongsToWorkingSchool($timetable)
        ) {
            return true;
        }

        return null;
    }

    private function belongsToWorkingSchool(Timetable $timetable): bool
    {
        return current_school_id() === $timetable->academicPeriod?->school_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Timetable $timetable)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Timetable $timetable)
    {
        //
    }
}
