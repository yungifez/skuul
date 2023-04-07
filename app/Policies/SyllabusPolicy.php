<?php

namespace App\Policies;

use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SyllabusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read syllabus')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Syllabus $syllabus)
    {
        if ($user->can('read syllabus') && $user->school_id == $syllabus->subject->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('create syllabus')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Syllabus $syllabus)
    {
        if ($user->can('update syllabus') && $user->school_id == $syllabus->subject->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Syllabus $syllabus)
    {
        if ($user->can('delete syllabus') && $user->school_id == $syllabus->subject->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Syllabus $syllabus)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Syllabus $syllabus)
    {
        //
    }
}
