<?php

namespace App\Policies;

use App\Models\OvernightLeave;
use App\Models\User;

class OvernightLeavePolicy
{
    /**
     * Determine whether the user can view the nights away.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read boarding');
    }

    /**
     * Determine whether the user can view the request.
     */
    public function view(User $user, OvernightLeave $leave): bool
    {
        return $user->can('read boarding') && $leave->school_id === current_school_id();
    }

    /**
     * Determine whether the user can ask for a night away.
     */
    public function create(User $user): bool
    {
        return $user->can('manage boarding');
    }

    /**
     * Determine whether the user can answer the request.
     *
     * Answering is a separate permission, because letting a child out of the
     * house overnight is not the same as keeping the bed list tidy.
     */
    public function decide(User $user, OvernightLeave $leave): bool
    {
        return $user->can('decide overnight leave') && $leave->school_id === current_school_id();
    }
}
