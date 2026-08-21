<?php

namespace App\Policies;

use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may read and change employment records.
 *
 * A person always reads their own record, because it is about them.
 */
class StaffProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of staff.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read staff profile');
    }

    /**
     * Determine whether the user can read one record.
     */
    public function view(User $user, StaffProfile $profile): bool
    {
        if ($profile->school_id !== current_school_id()) {
            return false;
        }

        return $user->can('read staff profile') || $profile->user_id === $user->id;
    }

    /**
     * Determine whether the user can add a record.
     */
    public function create(User $user): bool
    {
        return $user->can('create staff profile');
    }

    /**
     * Determine whether the user can change the record.
     */
    public function update(User $user, StaffProfile $profile): bool
    {
        return $user->can('update staff profile') && $profile->school_id === current_school_id();
    }

    /**
     * Determine whether the user can remove the record.
     */
    public function delete(User $user, StaffProfile $profile): bool
    {
        return $user->can('delete staff profile') && $profile->school_id === current_school_id();
    }
}
