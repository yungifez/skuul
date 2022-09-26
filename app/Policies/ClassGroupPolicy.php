<?php

namespace App\Policies;

use App\Models\ClassGroup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassGroupPolicy
{
    use HandlesAuthorization;

    public function __construct(ClassGroup $classGroup)
    {
        return true;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if ($user->can('read class group')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\ClassGroup $classGroup
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ClassGroup $classGroup)
    {
        if ($user->can('read class group') && $user->school_id == $classGroup->school_id) {
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
        if ($user->can('create class group')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\ClassGroup $classGroup
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ClassGroup $classGroup)
    {
        if ($user->can('update class group') && $user->school_id == $classGroup->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\ClassGroup $classGroup
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ClassGroup $classGroup)
    {
        if ($user->can('delete class group') && $user->school_id == $classGroup->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\ClassGroup $classGroup
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ClassGroup $classGroup)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\ClassGroup $classGroup
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ClassGroup $classGroup)
    {
        //
    }
}
