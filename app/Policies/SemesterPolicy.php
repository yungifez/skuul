<?php

namespace App\Policies;

use App\Models\Semester;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SemesterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read semester')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Semester $semester)
    {
        if ($user->can('read semester') && $user->school_id == $semester->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('create semester')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Semester $semester)
    {
        if ($user->can('update semester') && $user->school_id == $semester->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Semester $semester)
    {
        if ($user->can('delete semester') && $user->school_id == $semester->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Semester $semester)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Semester $semester)
    {
        //
    }

    /**
     * Determine whether the user can set current semester.
     */
    public function setSemester(User $user)
    {
        if ($user->can('set semester')) {
            return true;
        }
    }
}
