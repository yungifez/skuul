<?php

namespace App\Actions\Authorization;

use App\Enums\Role;
use App\Models\User;
use App\Services\Authorization\SystemPermissionScope;

class GrantSystemRole
{
    public function __construct(private SystemPermissionScope $systemPermissionScope)
    {
    }

    /**
     * Assign a globally-scoped Spatie role without changing the active school.
     */
    public function grant(User $user, Role $role): void
    {
        if (!$role->isSystemScoped()) {
            throw new \InvalidArgumentException("{$role->value} is not a system-scoped role.");
        }

        try {
            $this->systemPermissionScope->withinUserScope($user, fn () => $user->assignRole($role));
        } finally {
            $this->systemPermissionScope->forget($user);
        }
    }
}
