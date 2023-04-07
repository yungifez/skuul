<?php

namespace App\Policies;

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
        if ($user->can('read timetable') && $user->school_id == $timetable->myClass->classGroup->school->id) {
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
        if ($user->can('update timetable') && $user->school_id == $timetable->myClass->classGroup->school->id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Timetable $timetable)
    {
        if ($user->can('delete timetable') && $user->school_id == $timetable->myClass->classGroup->school->id) {
            return true;
        }
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
