<?php

namespace App\Policies;

use App\Models\ReportCardSnapshot;
use App\Models\User;

class ReportCardSnapshotPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read report');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReportCardSnapshot $reportCardSnapshot): bool
    {
        return $user->can('read report') && $reportCardSnapshot->school_id === current_school_id();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create report');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ReportCardSnapshot $reportCardSnapshot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReportCardSnapshot $reportCardSnapshot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ReportCardSnapshot $reportCardSnapshot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ReportCardSnapshot $reportCardSnapshot): bool
    {
        return false;
    }
}
