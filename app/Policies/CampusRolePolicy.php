<?php

namespace App\Policies;

use App\Models\CampusRole;
use App\Models\User;

/**
 * Who may write the roles a campus offers.
 *
 * Role management is campus work: a person may only write roles for the campus
 * they are working in, and only with permissions they already hold there.
 */
class CampusRolePolicy
{
    /**
     * Determine whether the user can see the roles of this campus.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read role');
    }

    /**
     * Determine whether the user can write a role.
     */
    public function create(User $user): bool
    {
        return $user->can('manage role');
    }

    /**
     * Determine whether the user can change this role.
     *
     * A role of another campus is never theirs to change. Shared roles that
     * are not application built-ins may be tailored by a campus manager.
     */
    public function update(User $user, CampusRole $role): bool
    {
        return $user->can('manage role')
            && ($role->school_id === null || $role->school_id === current_school_id())
            && !$role->isBuiltIn();
    }

    /**
     * Determine whether the user can stop offering this role.
     */
    public function archive(User $user, CampusRole $role): bool
    {
        return $this->update($user, $role);
    }

    /**
     * Determine whether the user can give and take away this role.
     *
     * A built-in role can be given out; it just cannot be rewritten.
     */
    public function assign(User $user, CampusRole $role): bool
    {
        return $user->can('manage role')
            && ($role->school_id === null || $role->school_id === current_school_id());
    }
}
