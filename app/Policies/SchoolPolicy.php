<?php

namespace App\Policies;

use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read school')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, School $school)
    {
        if (!$user->isPlatformAdmin() && !$user->belongsToSchool($school)) {
            return false;
        }

        if ($user->can('read school')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('create school')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, School $school)
    {
        if ($user->can('update school')) {
            return true;
        }

        if ($user->can('manage school settings')) {
            return current_school_id() === $school->id;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, School $school)
    {
        if ($user->can('delete school')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, School $school)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, School $school)
    {
        //
    }

    /**
     * Determine whether the user can change the school they are working in.
     *
     * A platform administrator may open any school. Everyone else may only
     * open a school they hold a membership in.
     */
    public function setSchool(User $user): ?bool
    {
        if ($user->isPlatformAdmin()) {
            return true;
        }

        return $user->schoolMemberships()->active()->exists() ? true : null;
    }
}
