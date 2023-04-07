<?php

namespace App\Policies;

use App\Models\GradeSystem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GradeSystemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read grade system')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GradeSystem $gradeSystem)
    {
        if ($user->can('read grade system') && $user->school_id == $gradeSystem->classGroup->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('create grade system')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GradeSystem $gradeSystem)
    {
        if ($user->can('update grade system') && $user->school_id == $gradeSystem->classGroup->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GradeSystem $gradeSystem)
    {
        if ($user->can('delete grade system') && $user->school_id == $gradeSystem->classGroup->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GradeSystem $gradeSystem)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GradeSystem $gradeSystem)
    {
        //
    }
}
