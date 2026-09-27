<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Authorization\GrantSystemRole;
use App\Enums\AuditAction;
use App\Enums\OrganizationMembershipStatus;
use App\Enums\OrganizationPermission;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use App\Services\Authorization\OrganizationPermissionScope;
use Illuminate\Support\Facades\DB;

class GrantOrganizationMembership
{
    public function __construct(
        private RecordAuditEvent $recordAuditEvent,
        private GrantSystemRole $grantSystemRole,
        private OrganizationPermissionScope $organizationPermissionScope,
    ) {}

    /**
     * Grant or reactivate organization scope without granting campus access.
     *
     * A new membership starts with every organization permission. Reactivating
     * an ended membership keeps the permissions it was last given, so a
     * delegated member does not quietly widen on the way back in. Either way,
     * the member gets no permission the actor lacks. Change the delegation
     * with `SetOrganizationMemberPermissions`.
     */
    public function grant(
        User $user,
        Organization $organization,
        ?User $actor = null,
    ): OrganizationMembership {
        $membership = DB::transaction(function () use ($user, $organization, $actor): OrganizationMembership {
            $membership = $user->organizationMemberships()
                ->firstOrNew(['organization_id' => $organization->id]);

            $membership->permissions = $this->limitToWhatTheActorHolds($membership, $organization, $actor);
            $membership->status = OrganizationMembershipStatus::Active;
            $membership->joined_at ??= now();
            $membership->ended_at = null;
            $membership->save();

            $this->grantSystemRole->grant($user, Role::OrganizationAdmin);

            $this->recordAuditEvent->record(
                AuditAction::OrganizationMembershipGranted,
                $membership,
                [
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                ],
                $actor,
            );

            return $membership;
        }, attempts: 3);

        $this->organizationPermissionScope->forget($user);

        return $membership;
    }

    /**
     * Keep only the permissions the actor holds in this organization.
     *
     * An actor with every permission passes the membership through unchanged.
     *
     * @return list<string>|null
     */
    private function limitToWhatTheActorHolds(OrganizationMembership $membership, Organization $organization, ?User $actor): ?array
    {
        if ($actor === null) {
            return $membership->permissions;
        }

        $held = $this->organizationPermissionScope->permissionsFor($actor, $organization);

        if (count($held) === count(OrganizationPermission::all())) {
            return $membership->permissions;
        }

        $kept = array_filter(
            $membership->grantedPermissions(),
            fn (OrganizationPermission $permission): bool => in_array($permission, $held, true),
        );

        return array_values(array_unique(array_map(
            fn (OrganizationPermission $permission): string => $permission->value,
            [OrganizationPermission::Read, ...$kept],
        )));
    }
}
