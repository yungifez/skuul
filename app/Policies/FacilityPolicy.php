<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;

class FacilityPolicy
{
    /**
     * Determine whether the user can see what the school shares.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read facility');
    }

    /**
     * Determine whether the user can see this one.
     */
    public function view(User $user, Facility $facility): bool
    {
        return $user->can('read facility') && $facility->school_id === current_school_id();
    }

    /**
     * Determine whether the user can add something to the catalogue.
     */
    public function create(User $user): bool
    {
        return $user->can('manage facility');
    }

    /**
     * Determine whether the user can change it.
     */
    public function update(User $user, Facility $facility): bool
    {
        return $user->can('manage facility') && $facility->school_id === current_school_id();
    }

    /**
     * Determine whether the user can take it out of use.
     */
    public function delete(User $user, Facility $facility): bool
    {
        return $user->can('manage facility') && $facility->school_id === current_school_id();
    }

    /**
     * Determine whether the user can claim it for a stretch of time.
     */
    public function book(User $user, Facility $facility): bool
    {
        return $user->can('book facility') && $facility->school_id === current_school_id();
    }
}
