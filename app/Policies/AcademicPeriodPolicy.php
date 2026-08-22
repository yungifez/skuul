<?php

namespace App\Policies;

use App\Models\AcademicPeriod;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicPeriodPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read academic period')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AcademicPeriod $academicPeriod)
    {
        if ($user->can('read academic period') && current_school_id() == $academicPeriod->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('create academic period')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AcademicPeriod $academicPeriod)
    {
        if ($user->can('update academic period') && current_school_id() == $academicPeriod->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AcademicPeriod $academicPeriod)
    {
        if ($user->can('delete academic period') && current_school_id() == $academicPeriod->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AcademicPeriod $academicPeriod)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AcademicPeriod $academicPeriod)
    {
        //
    }

    /**
     * Determine whether the user can set current academic period.
     */
    public function setAcademicPeriod(User $user)
    {
        if ($user->can('set academic period')) {
            return true;
        }
    }

    /**
     * Determine whether the user can close the period.
     */
    public function close(User $user, AcademicPeriod $academicPeriod): ?bool
    {
        if ($user->can('close academic period') && current_school_id() === $academicPeriod->school_id) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can reopen the period.
     */
    public function reopen(User $user, AcademicPeriod $academicPeriod): ?bool
    {
        if ($user->can('reopen academic period') && current_school_id() === $academicPeriod->school_id) {
            return true;
        }

        return null;
    }
}
