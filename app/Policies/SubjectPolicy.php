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
     */
    public function viewAny(User $user)
    {
        if ($user->can('read subject')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Subject $subject)
    {
        if ($user->can('read subject') && current_school_id() == $subject->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('create subject')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subject $subject)
    {
        if ($user->can('update subject') && current_school_id() == $subject->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subject $subject)
    {
        if ($user->can('delete subject') && current_school_id() == $subject->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Subject $subject)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Subject $subject)
    {
        //
    }

    /**
     * Determine whether the user can assign teachers to subjects.
     */
    public function assignTeacher(User $user): ?bool
    {
        if ($user->can('update subject')) {
            return true;
        }

        return null;
    }
}
