<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, $role)
    {
        if ($user->can("read $role")) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model, $role)
    {
        if ($user->school_id != $model->school_id) {
            return false;
        }

        if ($user->can("read $role") && $user->school_id == $model->school_id) {
            return true;
        }
        // user can view his own profile
        if ($user->id == $model->id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, $role)
    {
        if ($user->can("create $role")) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model, $role)
    {
        if ($user->school_id != $model->school_id) {
            return false;
        }

        if ($user->can("update $role") && $user->school_id == $model->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model, $role)
    {
        if ($user->school_id != $model->school_id) {
            return false;
        }

        if ($user->can("delete $role") && $user->school_id == $model->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }

    public function lockAccount(User $user, User $model)
    {
        if ($user->can('lock user account') && $model->school_id = $user->school_id) {
            return true;
        }
    }
}
