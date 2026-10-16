<?php

namespace App\Policies;

use App\Models\User;
use App\Services\Authorization\RoleAuthority;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user, $role)
    {
        if ($user->can("read $role")) {
            return true;
        }
    }

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

    public function create(User $user, $role)
    {
        if ($user->can("create $role")) {
            return true;
        }
    }

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
     *
     * Removing somebody who holds more here than the remover could give
     * would lock out the people above them, so that person is out of reach.
     */
    public function delete(User $user, User $model, $role)
    {
        if (!$model->belongsToCurrentSchool()) {
            return false;
        }

        if ($user->id !== $model->id && app(RoleAuthority::class)->holdsMoreThan($model, $user, current_school())) {
            return false;
        }

        if ($user->can("delete $role")) {
            return true;
        }
    }

    public function restore(User $user, User $model)
    {
        //
    }

    public function forceDelete(User $user, User $model)
    {
        //
    }

    /**
     * Hide how to reach a learner from a campus they have left.
     *
     * The old campus keeps the learner's history, but their contact details
     * now belong to the campus they attend.
     */
    public function viewContactDetails(User $user, User $model): bool
    {
        return $user->id === $model->id || !$model->isEnrolledOnlyElsewhere(current_school_id());
    }

    /**
     * Reject self-directed access changes and targets above the actor's authority.
     * Return null otherwise so the platform administrator gate can apply.
     */
    public function manageAccountAccess(User $user, User $model): ?bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->can('manage account access')
            && $model->belongsToCurrentSchool()
            && !$model->holdsPowerBeyond(current_school_id())
            && !$model->isEnrolledOnlyElsewhere(current_school_id())
            && !app(RoleAuthority::class)->holdsMoreThan($model, $user, current_school())) {
            return true;
        }

        return null;
    }
}
