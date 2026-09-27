<?php

namespace App\Actions\Authorization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\CampusRole;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\RoleAuthority;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Write the roles a campus offers.
 *
 * A role is a named set of permissions and nothing else. Nobody can write a
 * role that hands out more than they hold themselves, and the roles the
 * application relies on cannot be rewritten or retired.
 *
 * A shared role such as Librarian is one row every campus uses. A campus that
 * changes one gets its own copy, holding its own people, so the change never
 * reaches another campus or another organization.
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

        try {
            return DB::transaction(function () use ($school, $name, $permissions, $description, $actor): CampusRole {
                // Holding the campus keeps two people from taking one name at once.
                School::query()->whereKey($school->id)->lockForUpdate()->first();
                $this->mustBeAFreeName($name, $school);

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
        } catch (UniqueConstraintViolationException) {
            throw new InvalidValueException("This campus already has a role called $name.");
        }
    }

    /**
     * Change what a role holds.
     *
     * @param  array<int, string>  $permissions
     * @return CampusRole the role that changed, which is the campus's own copy when the role was shared
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

        return $this->authority->mustKeepARoleManager($school, function () use ($role, $school, $permissions, $description, $actor): CampusRole {
            $role = $this->ownCopy($role, $school, $actor);
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

        return DB::transaction(function () use ($role, $school, $actor): CampusRole {
            $role = $this->ownCopy($role, $school, $actor);

            if ($role->isArchived()) {
                return $role;
            }

            $role->archived_at = now();
            $role->save();

            $this->auditor->record(
                AuditAction::RoleArchived,
                $role,
                ['name' => $role->name, 'holders' => $this->authority->holdersAt($role, $school)->count()],
                $actor,
                $school,
            );

            return $role;
        });
    }

    /**
     * Offer an archived role again.
     *
     * @throws InvalidValueException when the role is built in or belongs elsewhere
     */
    public function restore(CampusRole $role, School $school, ?User $actor = null): CampusRole
    {
        $actor ??= $this->actor();

        $this->authority->mustNotBeBuiltIn($role);
        $this->authority->mustBelongTo($role, $school);

        return DB::transaction(function () use ($role, $school, $actor): CampusRole {
            $role = $this->ownCopy($role, $school, $actor);

            if (!$role->isArchived()) {
                return $role;
            }

            $role->archived_at = null;
            $role->save();

            $this->auditor->record(AuditAction::RoleUpdated, $role, ['name' => $role->name, 'restored' => true], $actor, $school);

            return $role;
        });
    }

    /**
     * Get the campus's own copy of a shared role, making it when needed.
     *
     * The copy starts with everything the shared role holds, and the people
     * holding the shared role at this campus move onto it. Nobody gains or
     * loses anything by the copy itself, and no other campus notices.
     */
    private function ownCopy(CampusRole $role, School $school, User $actor): CampusRole
    {
        if ($role->school_id !== null) {
            return $role;
        }

        $copy = $this->authority->tailoredCopyOf($role, $school);

        if ($copy === null) {
            $copy = CampusRole::query()->create([
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'school_id' => $school->id,
                'description' => $role->description,
            ]);

            $this->syncWithin($copy, $role->permissions->pluck('name')->all(), $school);

            $this->auditor->record(
                AuditAction::RoleCreated,
                $copy,
                ['name' => $copy->name, 'tailored_from' => $role->id],
                $actor,
                $school,
            );
        }

        $table = config('permission.table_names.model_has_roles');
        $campusColumn = config('permission.column_names.team_foreign_key');
        $holdingTheCopy = DB::table($table)
            ->where('role_id', $copy->id)
            ->where($campusColumn, $school->id)
            ->pluck('model_id');

        DB::table($table)
            ->where('role_id', $role->id)
            ->where($campusColumn, $school->id)
            ->whereIn('model_id', $holdingTheCopy)
            ->delete();

        DB::table($table)
            ->where('role_id', $role->id)
            ->where($campusColumn, $school->id)
            ->update(['role_id' => $copy->id]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $copy->refresh();
    }

    /**
     * Refuse a name the campus already uses, in any capitals.
     *
     * The shared roles every campus sees count too, so a campus never ends up
     * with two roles people cannot tell apart.
     *
     * @throws InvalidValueException when the name is taken
     */
    private function mustBeAFreeName(string $name, School $school): void
    {
        $isTaken = CampusRole::query()
            ->where(fn ($query) => $query->inSchool($school)->orWhereNull('school_id'))
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        if ($isTaken) {
            throw new InvalidValueException("This campus already has a role called $name.");
        }
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
