<?php

namespace App\Services\Authorization;

use App\Enums\OrganizationPermission;
use App\Enums\PlatformPermission;
use App\Exceptions\InvalidValueException;
use App\Models\CampusRole;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * What a person may put inside a role they are writing.
 *
 * Nobody can hand out authority they do not hold themselves: a campus
 * administrator writing a Registrar role can only fill it with permissions
 * they already have at that campus. Permissions that belong above a campus
 * are never on the list at all.
 */
class RoleAuthority
{
    public function __construct(private SystemPermissionScope $systemPermissionScope) {}

    /**
     * Get the permissions this person may put in a role at this campus.
     *
     * @return Collection<int, string>
     */
    public function grantableBy(User $actor, School $school): Collection
    {
        $aboveTheCampus = $this->aboveTheCampus();

        /** @var Collection<int, string> $names */
        $names = Permission::query()
            ->orderBy('name')
            ->pluck('name')
            ->reject(fn (string $name): bool => in_array($name, $aboveTheCampus, true))
            ->values();

        if ($this->systemPermissionScope->allows($actor, PlatformPermission::ManagePlatform)) {
            return $names;
        }

        $held = $this->heldBy($actor, $school);

        return $names->filter(fn (string $name): bool => in_array($name, $held, true))->values();
    }

    /**
     * Refuse a role that would hand out more than its author holds.
     *
     * @param  array<int, string>  $permissions
     *
     * @throws InvalidValueException when a permission is not the author's to give
     */
    public function mustBeGrantable(array $permissions, User $actor, School $school): void
    {
        $grantable = $this->grantableBy($actor, $school)->all();
        $refused = array_values(array_diff($permissions, $grantable));

        if ($refused !== []) {
            throw new InvalidValueException(
                'You cannot put something in a role that you do not hold yourself: '.implode(', ', $refused).'.'
            );
        }
    }

    /**
     * Refuse a role that belongs to another campus.
     *
     * A null school id is a shared, non-built-in role template and can be
     * tailored from the campus where the manager is working.
     *
     * @throws InvalidValueException when the role is not this campus's to change
     */
    public function mustBelongTo(CampusRole $role, School $school): void
    {
        if ($role->school_id !== null && $role->school_id !== $school->id) {
            throw new InvalidValueException('That role belongs to another campus.');
        }
    }

    /**
     * Refuse a role that cannot be given out at this campus.
     *
     * A campus may give out its own roles and the shared roles every campus
     * can use. A role another campus wrote is not one of them.
     *
     * @throws InvalidValueException when the role belongs to another campus
     */
    public function mustBeAssignableAt(CampusRole $role, School $school): void
    {
        if ($role->school_id !== null && $role->school_id !== $school->id) {
            throw new InvalidValueException('That role belongs to another campus.');
        }
    }

    /**
     * Refuse a change to a role the application itself relies on.
     *
     * @throws InvalidValueException when the role is built in
     */
    public function mustNotBeBuiltIn(CampusRole $role): void
    {
        if ($role->isBuiltIn()) {
            throw new InvalidValueException("$role->name is a built-in role. Copy it and change the copy instead.");
        }
    }

    /**
     * Get the permissions the person holds at one campus.
     *
     * @return array<int, string>
     */
    private function heldBy(User $actor, School $school): array
    {
        $registrar = app(PermissionRegistrar::class);
        $before = $registrar->getPermissionsTeamId();

        try {
            $registrar->setPermissionsTeamId($school->id);
            $actor->unsetRelation('roles')->unsetRelation('permissions');

            return $actor->getAllPermissions()->pluck('name')->all();
        } finally {
            $registrar->setPermissionsTeamId($before);
            $actor->unsetRelation('roles')->unsetRelation('permissions');
        }
    }

    /**
     * Get the permissions that belong above a campus.
     *
     * @return array<int, string>
     */
    private function aboveTheCampus(): array
    {
        return [
            ...array_map(fn (PlatformPermission $permission): string => $permission->value, PlatformPermission::cases()),
            ...array_map(fn (OrganizationPermission $permission): string => $permission->value, OrganizationPermission::cases()),
        ];
    }
}
