<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Authorization\RevokeSystemRole;
use App\Enums\AuditAction;
use App\Enums\OrganizationMembershipStatus;
use App\Enums\OrganizationPermission;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use App\Services\Authorization\OrganizationPermissionScope;
use Illuminate\Support\Facades\DB;

/**
 * End organization scope while preserving the membership history and campus access.
 * Keep the global role while another active organization membership needs it.
 */
class RevokeOrganizationMembership
{
    public function __construct(
        private RecordAuditEvent $recordAuditEvent,
        private RevokeSystemRole $revokeSystemRole,
        private OrganizationPermissionScope $organizationPermissionScope,
    ) {}

    /**
     * End the scope and return the membership, or null when there was none.
     */
    public function revoke(
        User $user,
        Organization $organization,
        ?User $actor = null,
    ): ?OrganizationMembership {
        $membership = DB::transaction(function () use ($user, $organization, $actor): ?OrganizationMembership {
            // Two managers taking each other's access at the same moment
            // would each count the other. The lock makes the second wait.
            Organization::query()->whereKey($organization->getKey())->lockForUpdate()->first();

            $membership = $user->organizationMemberships()
                ->where('organization_id', $organization->id)
                ->first();

            if ($membership === null || $membership->status === OrganizationMembershipStatus::Ended) {
                return $membership;
            }

            $this->failIfTheMemberHoldsMore($membership, $organization, $actor);

            if ($membership->grants(OrganizationPermission::ManageMembers)
                && !$organization->hasAnotherMemberManager($user)) {
                throw new InvalidValueException(
                    "{$organization->name} would be left with nobody who can manage its members. Grant a replacement first."
                );
            }

            $membership->status = OrganizationMembershipStatus::Ended;
            $membership->ended_at = now();
            $membership->save();

            $administersAnother = $user->organizationMemberships()->active()->exists();

            if (!$administersAnother) {
                $this->revokeSystemRole->revoke($user, Role::OrganizationAdmin);
            }

            $this->recordAuditEvent->record(
                AuditAction::OrganizationMembershipRevoked,
                $membership,
                [
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                    'kept_organization_admin_role' => $administersAnother,
                ],
                $actor,
            );

            return $membership;
        }, attempts: 3);

        $this->organizationPermissionScope->forget($user);

        return $membership;
    }

    /**
     * Refuse to end the scope of somebody who holds more than the actor.
     *
     * Otherwise a person trusted only with members could remove everybody
     * above them and be left running the organization. Anybody may still
     * give up their own scope.
     *
     * @throws InvalidValueException
     */
    private function failIfTheMemberHoldsMore(OrganizationMembership $membership, Organization $organization, ?User $actor): void
    {
        if ($actor === null || $actor->id === $membership->user_id) {
            return;
        }

        $values = fn (array $permissions): array => array_map(
            fn (OrganizationPermission $permission): string => $permission->value,
            $permissions,
        );

        $held = $values($this->organizationPermissionScope->permissionsFor($actor, $organization));

        if (array_diff($values($membership->grantedPermissions()), $held) !== []) {
            throw new InvalidValueException('This person holds more of the organization than you do, so only somebody holding as much can remove them.');
        }
    }
}
