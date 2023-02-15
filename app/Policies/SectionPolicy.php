<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SectionPolicy
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
        if ($user->can('read section')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Section $section)
    {
        if ($user->can('read section') && $section->myClass->classGroup->school->id == $user->school->id) {
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
        if ($user->can('create section')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Section $section)
    {
        if ($user->can('update section') && $user->school_id == $section->myClass->classGroup->school->id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Section $section)
    {
        if ($user->can('delete section') && $user->school_id == $section->myClass->classGroup->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Section $section)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Section $section)
    {
        //
    }
}
