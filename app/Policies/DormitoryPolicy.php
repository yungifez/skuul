<?php

namespace App\Policies;

use App\Models\Dormitory;
use App\Models\User;

class DormitoryPolicy
{
    /**
     * Determine whether the user can view any boarding houses.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read boarding');
    }

    /**
     * Determine whether the user can view the house.
     */
    public function view(User $user, Dormitory $dormitory): bool
    {
        return $user->can('read boarding') && $dormitory->school_id === current_school_id();
    }

    /**
     * Determine whether the user can open a house.
     */
    public function create(User $user): bool
    {
        return $user->can('manage boarding');
    }

    /**
     * Determine whether the user can change the house.
     */
    public function update(User $user, Dormitory $dormitory): bool
    {
        return $user->can('manage boarding') && $dormitory->school_id === current_school_id();
    }

    /**
     * Determine whether the user can close the house.
     */
    public function delete(User $user, Dormitory $dormitory): bool
    {
        return $user->can('manage boarding') && $dormitory->school_id === current_school_id();
    }
}
