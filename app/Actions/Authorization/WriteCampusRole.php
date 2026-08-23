<?php

namespace App\Actions\Authorization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\CampusRole;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\RoleAuthority;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Write the roles a campus offers.
 *
 * A role is a named set of permissions and nothing else. Nobody can write a
 * role that hands out more than they hold themselves, and the roles the
 * application relies on cannot be rewritten or retired.
 */
class WriteCampusRole
{
    public function __construct(
        private RoleAuthority $authority,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Start a role at one campus.
     *
     * @param  array<int, string>  $permissions
     *
     * @throws InvalidValueException when the name is taken or a permission is not the author's to give
     */
    public function create(
        School $school,
        string $name,
        array $permissions = [],
        ?string $description = null,
        ?User $actor = null,
    ): CampusRole {
        $actor ??= $this->actor();
        $name = trim($name);

        $this->authority->mustBeGrantable($permissions, $actor, $school);

        if (CampusRole::query()->inSchool($school)->where('name', $name)->exists()) {
            throw new InvalidValueException("This campus already has a role called $name.");
        }

        return DB::transaction(function () use ($school, $name, $permissions, $description, $actor): CampusRole {
            $role = CampusRole::query()->create([
                'name' => $name,
                'guard_name' => 'web',
                'school_id' => $school->id,
                'description' => $description,
            ]);

            $this->syncWithin($role, $permissions, $school);

            $this->auditor->record(
                AuditAction::RoleCreated,
                $role,
                ['name' => $name, 'permissions' => $permissions],
                $actor,
                $school,
            );

            return $role;
        });
    }

    /**
     * Change what a role holds.
     *
     * @param  array<int, string>  $permissions
     *
     * @throws InvalidValueException when the role is built in, belongs elsewhere, or would hand out too much
     */
    public function update(
        CampusRole $role,
        School $school,
        array $permissions,
        ?string $description = null,
        ?User $actor = null,
    ): CampusRole {
        $actor ??= $this->actor();

        $this->authority->mustNotBeBuiltIn($role);
        $this->authority->mustBelongTo($role, $school);
        $this->authority->mustBeGrantable($permissions, $actor, $school);

        return DB::transaction(function () use ($role, $school, $permissions, $description, $actor): CampusRole {
            $before = $role->permissions->pluck('name')->all();
            $role->description = $description;
            $role->save();

            $this->syncWithin($role, $permissions, $school);

            $this->auditor->record(
                AuditAction::RoleUpdated,
                $role,
                ['name' => $role->name, 'permissions' => $permissions, 'previous_permissions' => $before],
                $actor,
                $school,
            );

            return $role;
        });
    }

    /**
     * Copy a role, so a campus can start from one it already trusts.
     *
     * The copy holds only what the person copying it holds, so copying is
     * never a way around the rule about handing out authority.
     *
     * @throws InvalidValueException when the new name is taken
     */
    public function duplicate(CampusRole $role, School $school, string $name, ?User $actor = null): CampusRole
    {
        $actor ??= $this->actor();
        $this->authority->mustBeAssignableAt($role, $school);
        $grantable = $this->authority->grantableBy($actor, $school)->all();

        $permissions = $role->permissions
            ->pluck('name')
            ->filter(fn (string $permission): bool => in_array($permission, $grantable, true))
            ->values()
            ->all();

        return $this->create($school, $name, $permissions, $role->description, $actor);
    }

    /**
     * Stop offering a role, without taking anything from the people holding it.
     *
     * @throws InvalidValueException when the role is built in or belongs elsewhere
     */
    public function archive(CampusRole $role, School $school, ?User $actor = null): CampusRole
    {
        $actor ??= $this->actor();

        $this->authority->mustNotBeBuiltIn($role);
        $this->authority->mustBelongTo($role, $school);

        if ($role->isArchived()) {
            return $role;
        }

        $role->archived_at = now();
        $role->save();

        $this->auditor->record(
            AuditAction::RoleArchived,
            $role,
            ['name' => $role->name, 'holders' => $role->users()->count()],
            $actor,
            $school,
        );

        return $role;
    }

    /**
     * Offer an archived role again.
     */
    public function restore(CampusRole $role, School $school, ?User $actor = null): CampusRole
    {
        $actor ??= $this->actor();

        $this->authority->mustBelongTo($role, $school);

        $role->archived_at = null;
        $role->save();

        $this->auditor->record(AuditAction::RoleUpdated, $role, ['name' => $role->name, 'restored' => true], $actor, $school);

        return $role;
    }

    /**
     * Give the role its permissions inside the campus it belongs to.
     *
     * Permissions are campus-scoped, so the campus must be named before they
     * are written or they land against whichever campus the request happened
     * to be working in.
     *
     * @param  array<int, string>  $permissions
     */
    private function syncWithin(CampusRole $role, array $permissions, School $school): void
    {
        $registrar = app(PermissionRegistrar::class);
        $before = $registrar->getPermissionsTeamId();

        try {
            $registrar->setPermissionsTeamId($school->id);
            $role->syncPermissions($permissions);
        } finally {
            $registrar->setPermissionsTeamId($before);
        }
    }

    /**
     * Get the person doing this, when the caller did not name one.
     */
    private function actor(): User
    {
        $actor = auth()->user();

        if (!$actor instanceof User) {
            throw new InvalidValueException('A role must be written by somebody.');
        }

        return $actor;
    }
}
