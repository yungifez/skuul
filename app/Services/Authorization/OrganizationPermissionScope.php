<?php

namespace App\Services\Authorization;

use App\Enums\OrganizationPermission;
use App\Enums\PlatformPermission;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;

/**
 * Answer "may this person do this in this organization?".
 *
 * An organization permission needs two things at once: the global Spatie role
 * must carry it, and the person must hold an active membership in that one
 * organization that also carries it. The role says what the profile may ever
 * do; the membership says where, and how much of it was delegated.
 *
 * None of this opens campus records. Operational access still needs an active
 * school membership and a school-scoped permission.
 */
class OrganizationPermissionScope
{
    /**
     * @var array<string, OrganizationMembership|null>
     */
    private array $membershipsByUserAndOrganization = [];

    public function __construct(private SystemPermissionScope $systemPermissionScope)
    {
    }

    /**
     * Check one organization permission for one organization.
     */
    public function allows(User $user, Organization|int $organization, OrganizationPermission $permission): bool
    {
        if ($this->systemPermissionScope->allows($user, PlatformPermission::AccessAllOrganizations)) {
            return true;
        }

        if (!$this->systemPermissionScope->allows($user, $permission)) {
            return false;
        }

        return $this->membershipFor($user, $organization)?->grants($permission) ?? false;
    }

    /**
     * Get the permissions this person may use in this organization.
     *
     * @return list<OrganizationPermission>
     */
    public function permissionsFor(User $user, Organization|int $organization): array
    {
        return array_values(array_filter(
            OrganizationPermission::all(),
            fn (OrganizationPermission $permission): bool => $this->allows($user, $organization, $permission),
        ));
    }

    /**
     * Forget cached memberships after a change made in the same request.
     */
    public function forget(?User $user = null): void
    {
        if ($user === null) {
            $this->membershipsByUserAndOrganization = [];

            return;
        }

        $prefix = $user->getAuthIdentifier().':';

        foreach (array_keys($this->membershipsByUserAndOrganization) as $key) {
            if (str_starts_with($key, $prefix)) {
                unset($this->membershipsByUserAndOrganization[$key]);
            }
        }
    }

    /**
     * Read the active membership once per request.
     */
    private function membershipFor(User $user, Organization|int $organization): ?OrganizationMembership
    {
        $organizationId = $organization instanceof Organization ? $organization->id : $organization;
        $key = $user->getAuthIdentifier().':'.$organizationId;

        if (array_key_exists($key, $this->membershipsByUserAndOrganization)) {
            return $this->membershipsByUserAndOrganization[$key];
        }

        return $this->membershipsByUserAndOrganization[$key] = $user->organizationMemberships()
            ->active()
            ->where('organization_id', $organizationId)
            ->first();
    }
}
