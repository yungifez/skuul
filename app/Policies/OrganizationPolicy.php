<?php

namespace App\Policies;

use App\Enums\OrganizationPermission;
use App\Enums\PlatformPermission;
use App\Models\Organization;
use App\Models\User;
use App\Services\Authorization\OrganizationPermissionScope;
use App\Services\Authorization\SystemPermissionScope;

class OrganizationPolicy
{
    public function __construct(
        private SystemPermissionScope $systemPermissionScope,
        private OrganizationPermissionScope $organizationPermissionScope,
    ) {}

    public function viewAny(User $user): bool
    {
        return $this->systemPermissionScope->allows($user, PlatformPermission::AccessAllOrganizations)
            || ($this->systemPermissionScope->allows($user, OrganizationPermission::Read)
                && $user->organizationMemberships()->active()->exists());
    }

    public function view(User $user, Organization $organization): bool
    {
        return $this->organizationPermissionScope->allows($user, $organization, OrganizationPermission::Read);
    }

    public function create(User $user): bool
    {
        return $this->systemPermissionScope->allows($user, PlatformPermission::AccessAllOrganizations);
    }

    public function update(User $user, Organization $organization): bool
    {
        return $this->organizationPermissionScope->allows($user, $organization, OrganizationPermission::Manage);
    }

    /**
     * Determine whether the user can list members and change their scope.
     */
    public function manageMembers(User $user, Organization $organization): bool
    {
        return $this->organizationPermissionScope->allows($user, $organization, OrganizationPermission::ManageMembers);
    }

    /**
     * Determine whether the user can add campuses and edit them.
     */
    public function manageCampuses(User $user, Organization $organization): bool
    {
        return $this->organizationPermissionScope->allows($user, $organization, OrganizationPermission::ManageCampuses);
    }

    /**
     * Determine whether the user can configure the organization's calendar
     * templates and generate campus cycles from them.
     */
    public function manageCalendar(User $user, Organization $organization): bool
    {
        return $this->organizationPermissionScope->allows($user, $organization, OrganizationPermission::Manage);
    }

    /**
     * Determine whether the user can manage the organization's web addresses.
     */
    public function manageDomains(User $user, Organization $organization): bool
    {
        return $this->organizationPermissionScope->allows($user, $organization, OrganizationPermission::Manage);
    }

    /**
     * Determine whether the user can read the organization overview totals.
     */
    public function viewReports(User $user, Organization $organization): bool
    {
        return $this->organizationPermissionScope->allows($user, $organization, OrganizationPermission::ReadReports);
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $this->systemPermissionScope->allows($user, PlatformPermission::AccessAllOrganizations);
    }

    public function restore(User $user, Organization $organization): bool
    {
        return false;
    }

    public function forceDelete(User $user, Organization $organization): bool
    {
        return false;
    }
}
