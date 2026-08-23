<?php

namespace App\Policies;

use App\Models\LibraryCopy;
use App\Models\User;

class LibraryCopyPolicy
{
    /**
     * Determine whether the user can look at the library.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read library');
    }

    /**
     * Determine whether the user can look at the copy.
     */
    public function view(User $user, LibraryCopy $copy): bool
    {
        return $user->can('read library') && $copy->school_id === current_school_id();
    }

    /**
     * Determine whether the user can add a copy to the shelf.
     */
    public function create(User $user): bool
    {
        return $user->can('manage library');
    }

    /**
     * Determine whether the user can change the copy.
     */
    public function update(User $user, LibraryCopy $copy): bool
    {
        return $user->can('manage library') && $copy->school_id === current_school_id();
    }

    /**
     * Determine whether the user can take the copy off the shelf for good.
     */
    public function delete(User $user, LibraryCopy $copy): bool
    {
        return $user->can('manage library') && $copy->school_id === current_school_id();
    }
}
