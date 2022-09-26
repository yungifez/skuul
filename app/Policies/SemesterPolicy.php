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
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if ($user->can('read semester')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\Semester $semester
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Semester $semester)
    {
        if ($user->can('read semester') && $user->school_id == $semester->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->can('create semester')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\Semester $semester
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Semester $semester)
    {
        if ($user->can('update semester') && $user->school_id == $semester->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\Semester $semester
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Semester $semester)
    {
        if ($user->can('delete semester') && $user->school_id == $semester->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\Semester $semester
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Semester $semester)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\Semester $semester
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Semester $semester)
    {
        //
    }

    /**
     * Determine whether the user can set current semester.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function setSemester(User $user)
    {
        if ($user->can('set semester')) {
            return true;
        }
    }
}
