<?php

namespace App\Policies;

use App\Enums\OrganizationPermission;
use App\Enums\PlatformPermission;
use App\Models\AcademicYear;
use App\Models\User;
use App\Services\Authorization\OrganizationPermissionScope;
use App\Services\Authorization\SystemPermissionScope;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicYearPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private SystemPermissionScope $systemPermissionScope,
        private OrganizationPermissionScope $organizationPermissionScope,
    ) {}

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read academic year') || $user->can('read academic period')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AcademicYear $academicYear)
    {
        if (($user->can('read academic year') || $user->can('read academic period')) && current_school_id() === $academicYear->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('create academic year')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AcademicYear $academicYear)
    {
        if ($user->can('update academic year') && current_school_id() == $academicYear->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AcademicYear $academicYear)
    {
        if ($user->can('delete academic year') && current_school_id() == $academicYear->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AcademicYear $academicYear)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AcademicYear $academicYear)
    {
        //
    }

    /**
     * Determine whether the user can set academic year.
     */
    public function setAcademicYear(User $user)
    {
        if ($user->can('set academic year')) {
            return true;
        }
    }

    /**
     * Determine whether the user can close the period.
     */
    public function close(User $user, AcademicYear $academicYear): ?bool
    {
        if ($user->can('close academic period') && current_school_id() === $academicYear->school_id) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can reopen the period.
     */
    public function reopen(User $user, AcademicYear $academicYear): ?bool
    {
        if ($user->can('reopen academic period') && current_school_id() === $academicYear->school_id) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can read how the campus teaches the cycle.
     */
    public function viewInstructionalModel(User $user, AcademicYear $academicYear): ?bool
    {
        if ($this->setInstructionalModel($user, $academicYear)
            || $this->migrateInstructionalModel($user, $academicYear)) {
            return true;
        }

        if ($user->can('read academic year') && current_school_id() === $academicYear->school_id) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can choose how the campus teaches the cycle.
     *
     * A campus administrator holds `manage school settings` in the campus
     * being worked in. An organization administrator who manages campuses may
     * set it up before anybody works there.
     */
    public function setInstructionalModel(User $user, AcademicYear $academicYear): ?bool
    {
        if ($this->administersCampus($user, $academicYear)) {
            return true;
        }

        if ($user->can('manage school settings') && current_school_id() === $academicYear->school_id) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can move a running cycle to another model.
     *
     * This is deliberately not the settings permission. Choosing the model of
     * a cycle that has not started is setup; moving one learners already work
     * in changes what staff are asked for mid-year, so it is held separately.
     */
    public function migrateInstructionalModel(User $user, AcademicYear $academicYear): ?bool
    {
        if ($user->can('migrate instructional model') && current_school_id() === $academicYear->school_id) {
            return true;
        }

        if ($this->administersCampus($user, $academicYear)) {
            return true;
        }

        return null;
    }

    /**
     * Check if the person administers the campus that owns the cycle.
     */
    private function administersCampus(User $user, AcademicYear $academicYear): bool
    {
        if ($this->systemPermissionScope->allows($user, PlatformPermission::AccessAllSchools)) {
            return true;
        }

        $organizationId = $academicYear->school?->organization_id;

        return $organizationId !== null
            && $this->organizationPermissionScope->allows($user, $organizationId, OrganizationPermission::ManageCampuses);
    }
}
