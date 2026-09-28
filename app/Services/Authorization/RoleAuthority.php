<?php

namespace App\Services\Authorization;

use App\Enums\AccountStatus;
use App\Enums\OrganizationPermission;
use App\Enums\PlatformPermission;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\CampusRole;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
     * Get the people who hold a role at one campus.
     *
     * A shared role is one row for every campus, so its holders elsewhere are
     * never this campus's business.
     *
     * @return Builder<User>
     */
    public function holdersAt(CampusRole $role, School $school): Builder
    {
        return User::query()->whereIn('users.id', DB::table('model_has_roles')
            ->select('model_id')
            ->where('role_id', $role->id)
            ->where('school_id', $school->id)
            ->where('model_type', (new User)->getMorphClass()));
    }

    /**
     * Check whether anybody at the campus can still manage its roles.
     *
     * Read straight from the tables, so a change made inside the same
     * transaction counts before any cache catches up.
     */
    public function campusHasARoleManager(School $school): bool
    {
        $user = (new User)->getMorphClass();
        // A role outlives the membership, so somebody who left still holds it.
        // A suspended or archived account holds it too, but cannot sign in.
        $members = SchoolMembership::query()
            ->active()
            ->where('school_id', $school->id)
            ->whereIn('user_id', User::query()->whereNotIn('account_status', [AccountStatus::Suspended, AccountStatus::Archived])->select('id'))
            ->select('user_id');

        $throughARole = DB::table('model_has_roles')
            ->join('role_has_permissions', 'role_has_permissions.role_id', '=', 'model_has_roles.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('model_has_roles.school_id', $school->id)
            ->where('model_has_roles.model_type', $user)
            ->whereIn('model_has_roles.model_id', $members)
            ->where('permissions.name', 'manage role')
            ->exists();

        return $throughARole || DB::table('model_has_permissions')
            ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
            ->where('model_has_permissions.school_id', $school->id)
            ->where('model_has_permissions.model_type', $user)
            ->whereIn('model_has_permissions.model_id', $members)
            ->where('permissions.name', 'manage role')
            ->exists();
    }

    /**
     * Refuse a change that leaves the campus with nobody who can manage roles.
     *
     * @param  callable(): mixed  $change
     *
     * @throws InvalidValueException when the last way to manage roles would go
     */
    public function mustKeepARoleManager(School $school, callable $change): mixed
    {
        try {
            return DB::transaction(function () use ($school, $change): mixed {
                // Two managers taking the role from each other at once must
                // not both succeed, so the campus is held while this runs.
                School::query()->whereKey($school->id)->lockForUpdate()->first();
                $hadOne = $this->campusHasARoleManager($school);

                $result = $change();

                if ($hadOne && !$this->campusHasARoleManager($school)) {
                    throw new InvalidValueException('Nobody at this campus could manage roles after that. Give somebody else role management first.');
                }

                return $result;
            });
        } finally {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    /**
     * Find the campus's own copy of a shared role, when it made one.
     */
    public function tailoredCopyOf(CampusRole $role, School $school): ?CampusRole
    {
        if ($role->school_id !== null) {
            return null;
        }

        return CampusRole::query()
            ->inSchool($school)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($role->name)])
            ->first();
    }

    /**
     * Check whether the person holds staff power at the campus the actor could not give.
     *
     * Whoever can lock a person out should not reach somebody who holds more
     * than they do. What a learner or a family reads through the portal is
     * not power over the campus, so it is left out.
     */
    public function holdsMoreThan(User $person, User $actor, School $school): bool
    {
        $registrar = app(PermissionRegistrar::class);
        $before = $registrar->getPermissionsTeamId();

        try {
            $registrar->setPermissionsTeamId($school->id);
            $person->unsetRelation('roles')->unsetRelation('permissions');

            /** @var \Illuminate\Database\Eloquent\Collection<int, CampusRole> $roles */
            $roles = $person->roles;

            $staffPower = $roles
                ->reject(fn (CampusRole $role): bool => in_array($role->name, [Role::Student->value, Role::Parent->value], true))
                ->flatMap(fn (CampusRole $role) => $role->permissions->pluck('name'))
                ->merge($person->permissions->pluck('name'))
                ->unique()
                ->all();
        } finally {
            $registrar->setPermissionsTeamId($before);
            $person->unsetRelation('roles')->unsetRelation('permissions');
        }

        return array_diff($staffPower, $this->grantableBy($actor, $school)->all()) !== [];
    }

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
