<?php

namespace App\Policies;

use App\Models\Cohort;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may read and keep groups.
 *
 * A watchlist is private, because the reason a child is on one is not
 * ordinary information.
 */
class CohortPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of groups.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read cohort');
    }

    /**
     * Determine whether the user can read one group.
     */
    public function view(User $user, Cohort $cohort): bool
    {
        if ($cohort->school_id !== current_school_id()) {
            return false;
        }

        if ($cohort->is_restricted) {
            return $user->can('read restricted cohort');
        }

        return $user->can('read cohort');
    }

    /**
     * Determine whether the user can make a group.
     */
    public function create(User $user): bool
    {
        return $user->can('create cohort');
    }

    /**
     * Determine whether the user can change the group.
     */
    public function update(User $user, Cohort $cohort): bool
    {
        return $user->can('update cohort') && $this->view($user, $cohort);
    }

    /**
     * Determine whether the user can remove the group.
     */
    public function delete(User $user, Cohort $cohort): bool
    {
        return $user->can('delete cohort') && $this->view($user, $cohort);
    }
}
