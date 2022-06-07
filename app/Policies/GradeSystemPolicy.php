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
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if ($user->can('read grade system')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\GradeSystem $gradeSystem
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, GradeSystem $gradeSystem)
    {
        if ($user->can('read grade system') && $user->school_id == $gradeSystem->classGroup->school_id) {
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
        if ($user->can('create grade system')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\GradeSystem $gradeSystem
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, GradeSystem $gradeSystem)
    {
        if ($user->can('update grade system') && $user->school_id == $gradeSystem->classGroup->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\GradeSystem $gradeSystem
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, GradeSystem $gradeSystem)
    {
        if ($user->can('delete grade system') && $user->school_id == $gradeSystem->classGroup->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\GradeSystem $gradeSystem
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, GradeSystem $gradeSystem)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\GradeSystem $gradeSystem
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, GradeSystem $gradeSystem)
    {
        //
    }
}
