<?php

namespace App\Actions\Authorization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\CampusRole;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\RoleAuthority;
use Spatie\Permission\PermissionRegistrar;

/**
 * Give and take away a campus role.
 *
 * A role can only be given to somebody who works at that campus, and only a
 * role the campus still offers. Both directions are written to the audit log,
 * because a role is how a person got whatever they were able to do.
 */
class AssignCampusRole
{
    public function __construct(
        private RoleAuthority $authority,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Give the role to somebody who works at the campus.
     *
     * @throws InvalidValueException when the person is not a member, the role
     *                               is retired, or it holds more than the giver does
     */
    public function give(User $person, CampusRole $role, School $school, ?User $actor = null): void
    {
        $this->authority->mustBeAssignableAt($role, $school);

        // Giving a role hands out everything inside it, so the same rule holds
        // as when writing one: nobody gives away what they do not hold.
        if ($actor !== null) {
            $this->authority->mustBeGrantable($role->permissions->pluck('name')->all(), $actor, $school);
        }

        if (!$person->belongsToSchool($school->id)) {
            throw new InvalidValueException('That person does not work at this campus.');
        }

        if ($role->isArchived()) {
            throw new InvalidValueException("$role->name is no longer offered at this campus.");
        }

        $this->within($school, function () use ($person, $role): void {
            $person->assignRole($role);
        });

        $this->auditor->record(
            AuditAction::RoleAttached,
            $person,
            ['role' => $role->name, 'role_id' => $role->id],
            $actor,
            $school,
        );
    }

    /**
     * Take the role away again.
     */
    public function take(User $person, CampusRole $role, School $school, ?User $actor = null): void
    {
        $this->authority->mustBeAssignableAt($role, $school);

        $this->within($school, function () use ($person, $role): void {
            $person->removeRole($role);
        });

        $this->auditor->record(
            AuditAction::RoleDetached,
            $person,
            ['role' => $role->name, 'role_id' => $role->id],
            $actor,
            $school,
        );
    }

    /**
     * Do the work with the campus named, so the role lands in the right place.
     */
    private function within(School $school, callable $work): void
    {
        $registrar = app(PermissionRegistrar::class);
        $before = $registrar->getPermissionsTeamId();

        try {
            $registrar->setPermissionsTeamId($school->id);
            $work();
        } finally {
            $registrar->setPermissionsTeamId($before);
        }
    }
}
