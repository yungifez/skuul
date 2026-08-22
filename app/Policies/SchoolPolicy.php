<?php

namespace App\Policies;

use App\Enums\OrganizationPermission;
use App\Enums\PlatformPermission;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\OrganizationPermissionScope;
use App\Services\Authorization\SystemPermissionScope;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private SystemPermissionScope $systemPermissionScope,
        private OrganizationPermissionScope $organizationPermissionScope,
    ) {
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read school')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, School $school)
    {
        if ($this->canManageOrganization($user, $school->organization)) {
            return true;
        }

        if (!$user->belongsToSchool($school)) {
            return false;
        }

        if ($user->can('read school')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($this->systemPermissionScope->allows($user, PlatformPermission::AccessAllSchools)
            || ($this->systemPermissionScope->allows($user, OrganizationPermission::Manage)
                && $user->organizationMemberships()->active()->exists())
            || $user->can('create school')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, School $school)
    {
        if ($this->canManageOrganization($user, $school->organization)) {
            return true;
        }

        if ($user->can('update school') && current_school_id() === $school->id) {
            return true;
        }

        if ($user->can('manage school settings')) {
            return current_school_id() === $school->id;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, School $school)
    {
        if ($user->can('delete school')
            && ($this->systemPermissionScope->allows($user, PlatformPermission::AccessAllSchools) || current_school_id() === $school->id)
        ) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, School $school)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, School $school)
    {
        //
    }

    /**
     * Determine whether the user can change the school they are working in.
     *
     * A platform administrator may open any school. Everyone else may only
     * open a school they hold a membership in.
     */
    public function setSchool(User $user): ?bool
    {
        if ($this->systemPermissionScope->allows($user, PlatformPermission::AccessAllSchools)) {
            return true;
        }

        return $user->schoolMemberships()->active()->exists() ? true : null;
    }

    /**
     * Determine whether the user may add a campus to an organization.
     *
     * Adding a campus is its own organization permission, so it can be
     * delegated without handing over the whole organization.
     */
    public function createForOrganization(User $user, Organization $organization): bool
    {
        return $this->systemPermissionScope->allows($user, PlatformPermission::AccessAllSchools)
            || $this->organizationPermissionScope->allows($user, $organization, OrganizationPermission::ManageCampuses);
    }

    private function canManageOrganization(User $user, Organization $organization): bool
    {
        return $this->systemPermissionScope->allows($user, PlatformPermission::AccessAllSchools)
            || ($this->systemPermissionScope->allows($user, OrganizationPermission::Manage)
                && $user->administersOrganization($organization));
    }
}
