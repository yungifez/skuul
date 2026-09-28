<?php

namespace App\Policies;

use App\Models\User;
use App\Services\Authorization\RoleAuthority;
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

        if ($user->can("read $role")) {
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

        // A learner who moved keeps their membership here so this campus can
        // still read what they did here. Their details now belong to the
        // campus they attend.
        if ($role === 'student' && !$model->studentRecords()->where('school_id', current_school_id())->exists()) {
            return false;
        }

        if ($user->can("update $role")) {
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

        if ($user->can("delete $role")) {
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
     * This covers suspend, reinstate, archive, invite, and revoke. A person
     * who holds more at the campus than the manager could give is out of
     * reach, so a custom role cannot lock out the people above it.
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

        if ($user->can('manage account access')
            && $model->belongsToCurrentSchool()
            && !$model->holdsPowerBeyond(current_school_id())
            && !app(RoleAuthority::class)->holdsMoreThan($model, $user, current_school())) {
            return true;
        }

        return null;
    }
}
