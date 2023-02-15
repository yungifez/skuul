<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if ($user->can('read subject')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Subject $subject)
    {
        if ($user->can('read subject') && $user->school_id == $subject->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->can('create subject')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Subject $subject)
    {
        if ($user->can('update subject') && $user->school_id == $subject->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Subject $subject)
    {
        if ($user->can('delete subject') && $user->school_id == $subject->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Subject $subject)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Subject $subject)
    {
        //
    }
}
