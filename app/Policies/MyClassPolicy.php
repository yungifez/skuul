<?php

namespace App\Policies;

use App\Models\MyClass;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MyClassPolicy
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
        if ($user->can('read class')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User    $user
     * @param \App\Models\MyClass $myClass
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, MyClass $myClass)
    {
        if ($user->can('read class') && $myClass->classGroup->school_id == $user->school_id) {
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
        if ($user->can('create class')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User    $user
     * @param \App\Models\MyClass $myClass
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, MyClass $myClass)
    {
        if ($user->can('update class') && $user->school_id == $myClass->classGroup->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User    $user
     * @param \App\Models\MyClass $myClass
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, MyClass $myClass)
    {
        if ($user->can('delete class') && $user->school_id == $myClass->classGroup->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User    $user
     * @param \App\Models\MyClass $myClass
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, MyClass $myClass)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User    $user
     * @param \App\Models\MyClass $myClass
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, MyClass $myClass)
    {
        //
    }
}
