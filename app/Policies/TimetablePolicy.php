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
        if ($user->can('read timetable') && current_school_id() == $timetable->myClass->classGroup->school->id) {
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
     * Determine whether the user can update the model.
     */
    public function update(User $user, Timetable $timetable)
    {
        if ($user->can('update timetable')
            && $timetable->acceptsChanges()
            && $timetable->semester->isOpen()
            && $timetable->semester->academicYear->isOpen()
            && current_school_id() == $timetable->myClass->classGroup->school->id
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
            && current_school_id() == $timetable->myClass->classGroup->school->id
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
            && $timetable->semester->isOpen()
            && $timetable->semester->academicYear->isOpen()
            && current_school_id() == $timetable->myClass->classGroup->school->id
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
            && $timetable->semester->isOpen()
            && $timetable->semester->academicYear->isOpen()
            && current_school_id() == $timetable->myClass->classGroup->school->id
        ) {
            return true;
        }

        return null;
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
