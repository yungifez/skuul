<?php

namespace App\Policies;

use App\Enums\OrganizationPermission;
use App\Models\CalendarTemplate;
use App\Models\User;
use App\Services\Authorization\OrganizationPermissionScope;

class CalendarTemplatePolicy
{
    public function __construct(private OrganizationPermissionScope $organizationPermissionScope) {}

    public function viewAny(User $user): bool
    {
        return $user->organizationMemberships()->active()->exists();
    }

    public function view(User $user, CalendarTemplate $calendarTemplate): bool
    {
        return $this->organizationPermissionScope->allows($user, $calendarTemplate->organization, OrganizationPermission::Read);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, CalendarTemplate $calendarTemplate): bool
    {
        return $this->organizationPermissionScope->allows($user, $calendarTemplate->organization, OrganizationPermission::Manage);
    }

    public function delete(User $user, CalendarTemplate $calendarTemplate): bool
    {
        return $this->organizationPermissionScope->allows($user, $calendarTemplate->organization, OrganizationPermission::Manage);
    }

    public function restore(User $user, CalendarTemplate $calendarTemplate): bool
    {
        return false;
    }

    public function forceDelete(User $user, CalendarTemplate $calendarTemplate): bool
    {
        return false;
    }
}
