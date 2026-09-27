<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\OrganizationPermission;
use App\Exceptions\InvalidValueException;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use App\Services\Authorization\OrganizationPermissionScope;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Say how much of the organization one member may run.
 *
 * Delegation only narrows. A member can never be given more than the global
 * organization-admin role already carries, and every member keeps the read
 * permission so the organization screens still open.
 *
 * The last person who can manage members cannot give that permission away,
 * so an organization always keeps somebody who can hand it back.
 */
class SetOrganizationMemberPermissions
{
    public function __construct(
        private RecordAuditEvent $recordAuditEvent,
        private OrganizationPermissionScope $organizationPermissionScope,
    ) {}

    /**
     * Store the delegated permissions, or full authority when given null.
     *
     * @param  list<OrganizationPermission>|null  $permissions  null gives every permission
     */
    public function set(
        User $user,
        Organization $organization,
        ?array $permissions,
        ?User $actor = null,
    ): OrganizationMembership {
        $membership = DB::transaction(function () use ($user, $organization, $permissions, $actor): OrganizationMembership {
            $membership = $user->organizationMemberships()
                ->active()
                ->where('organization_id', $organization->id)
                ->first();

            if ($membership === null) {
                throw new RuntimeException("{$user->name} does not administer {$organization->name}.");
            }

            $previous = $membership->permissions;
            $next = $permissions === null ? null : $this->normalize($permissions);

            $this->failIfActorLacksAChangedPermission($membership, $next, $organization, $actor);

            $keepsMemberManagement = $next === null
                || in_array(OrganizationPermission::ManageMembers->value, $next, true);

            if (!$keepsMemberManagement
                && $membership->grants(OrganizationPermission::ManageMembers)
                && !$organization->hasAnotherMemberManager($user)) {
                throw new InvalidValueException(
                    "{$organization->name} would be left with nobody who can manage its members. Give somebody else that permission first."
                );
            }

            $membership->permissions = $next;
            $membership->save();

            $this->recordAuditEvent->record(
                AuditAction::OrganizationMembershipPermissionsChanged,
                $membership,
                [
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                    'previous_permissions' => $previous,
                    'permissions' => $membership->permissions,
                ],
                $actor,
            );

            return $membership;
        }, attempts: 3);

        $this->organizationPermissionScope->forget($user);

        return $membership;
    }

    /**
     * Refuse to give or take away a permission the actor does not hold.
     *
     * Without this, a person trusted only with members could hand themself,
     * or anybody else, the rest of the organization.
     *
     * @param  list<string>|null  $next
     *
     * @throws InvalidValueException
     */
    private function failIfActorLacksAChangedPermission(
        OrganizationMembership $membership,
        ?array $next,
        Organization $organization,
        ?User $actor,
    ): void {
        if ($actor === null) {
            return;
        }

        $values = fn (array $permissions): array => array_map(
            fn (OrganizationPermission $permission): string => $permission->value,
            $permissions,
        );

        $before = $values($membership->grantedPermissions());
        $after = $next ?? $values(OrganizationPermission::all());
        $held = $values($this->organizationPermissionScope->permissionsFor($actor, $organization));

        $changed = array_merge(array_diff($before, $after), array_diff($after, $before));

        if (array_diff($changed, $held) !== []) {
            throw new InvalidValueException('You can only give or take away permissions you hold yourself.');
        }
    }

    /**
     * Keep the read permission, drop repeats, and store plain strings.
     *
     * @param  list<OrganizationPermission>  $permissions
     * @return list<string>
     */
    private function normalize(array $permissions): array
    {
        $values = array_map(
            fn (OrganizationPermission $permission): string => $permission->value,
            [OrganizationPermission::Read, ...$permissions],
        );

        return array_values(array_unique($values));
    }
}
