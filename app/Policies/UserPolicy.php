<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, $role)
    {
        if ($user->can("read $role")) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model, $role)
    {
        if (!$model->belongsToCurrentSchool()) {
            return false;
        }

        if ($user->can("read $role") && $model->belongsToCurrentSchool()) {
            return true;
        }
        // user can view his own profile
        if ($user->id == $model->id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $role)
    {
        if ($user->can("create $role")) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model, $role)
    {
        if (!$model->belongsToCurrentSchool()) {
            return false;
        }

        if ($user->can("update $role") && $model->belongsToCurrentSchool()) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model, $role)
    {
        if (!$model->belongsToCurrentSchool()) {
            return false;
        }

        if ($user->can("delete $role") && $model->belongsToCurrentSchool()) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can change another account's access state.
     *
     * This covers suspend, reinstate, archive, invite, and revoke.
     *
     * Nobody may change their own account access, not even a super
     * administrator. Returning null for the other cases lets the super
     * administrator gate in AppServiceProvider apply.
     */
    public function manageAccountAccess(User $user, User $model): ?bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->can('manage account access') && $model->belongsToCurrentSchool()) {
            return true;
        }

        return null;
    }
}
