<?php

namespace App\Services\Authorization;

use App\Models\User;
use BackedEnum;
use Closure;
use Illuminate\Support\Arr;
use Spatie\Permission\PermissionRegistrar;

class SystemPermissionScope
{
    /**
     * A non-school team reserved for global Spatie role assignments.
     *
     * Real school IDs begin at one. Team zero never represents a school.
     */
    public const SystemTeamId = 0;

    /**
     * @var array<string, list<string>>
     */
    private array $permissionsByUser = [];

    public function __construct(private PermissionRegistrar $permissionRegistrar) {}

    /**
     * Check a permission through global Spatie roles without changing the
     * request's active school context.
     */
    public function allows(User $user, BackedEnum|string $permission): bool
    {
        $permissionName = $permission instanceof BackedEnum ? $permission->value : $permission;

        return in_array($permissionName, $this->permissionsFor($user), true);
    }

    /**
     * Forget cached global permissions after an in-request role change.
     */
    public function forget(User $user): void
    {
        unset($this->permissionsByUser[(string) $user->getAuthIdentifier()]);
    }

    /**
     * Run one role operation against the global Spatie team while preserving
     * roles and direct permissions loaded for the active school.
     *
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    public function withinUserScope(User $user, Closure $callback): mixed
    {
        $schoolRelations = Arr::only($user->getRelations(), ['roles', 'permissions']);

        $user->unsetRelation('roles')->unsetRelation('permissions');

        try {
            return $this->within($callback);
        } finally {
            $user->unsetRelation('roles')->unsetRelation('permissions');

            foreach ($schoolRelations as $relation => $value) {
                $user->setRelation($relation, $value);
            }
        }
    }

    /**
     * @return list<string>
     */
    private function permissionsFor(User $user): array
    {
        $userId = (string) $user->getAuthIdentifier();

        if (array_key_exists($userId, $this->permissionsByUser)) {
            return $this->permissionsByUser[$userId];
        }

        return $this->permissionsByUser[$userId] = $this->withinUserScope(
            $user,
            fn (): array => $user->getAllPermissions()->pluck('name')->all(),
        );
    }

    /**
     * Run one role operation against the global Spatie team.
     *
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    public function within(Closure $callback): mixed
    {
        $previousTeamId = $this->permissionRegistrar->getPermissionsTeamId();

        try {
            $this->permissionRegistrar->setPermissionsTeamId(self::SystemTeamId);

            return $callback();
        } finally {
            $this->permissionRegistrar->setPermissionsTeamId($previousTeamId);
        }
    }
}
