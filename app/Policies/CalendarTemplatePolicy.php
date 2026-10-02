<?php

namespace App\Policies;

use App\Enums\OrganizationPermission;
use App\Models\CalendarTemplate;
use App\Models\User;
use App\Services\Authorization\OrganizationPermissionScope;

class CalendarTemplatePolicy
{
    public function __construct(private OrganizationPermissionScope $organizationPermissionScope) {}

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->organizationMemberships()->active()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CalendarTemplate $calendarTemplate): bool
    {
        return $this->organizationPermissionScope->allows($user, $calendarTemplate->organization, OrganizationPermission::Read);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CalendarTemplate $calendarTemplate): bool
    {
        return $this->organizationPermissionScope->allows($user, $calendarTemplate->organization, OrganizationPermission::Manage);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CalendarTemplate $calendarTemplate): bool
    {
        return $this->organizationPermissionScope->allows($user, $calendarTemplate->organization, OrganizationPermission::Manage);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CalendarTemplate $calendarTemplate): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CalendarTemplate $calendarTemplate): bool
    {
        return false;
    }
}
