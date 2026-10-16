<?php

namespace App\Policies;

use App\Models\ReportCardSnapshot;
use App\Models\User;

class ReportCardSnapshotPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('read report');
    }

    public function view(User $user, ReportCardSnapshot $reportCardSnapshot): bool
    {
        return $user->can('read report') && $reportCardSnapshot->school_id === current_school_id();
    }

    public function create(User $user): bool
    {
        return $user->can('create report');
    }

    public function update(User $user, ReportCardSnapshot $reportCardSnapshot): bool
    {
        return false;
    }

    public function delete(User $user, ReportCardSnapshot $reportCardSnapshot): bool
    {
        return false;
    }

    public function restore(User $user, ReportCardSnapshot $reportCardSnapshot): bool
    {
        return false;
    }

    public function forceDelete(User $user, ReportCardSnapshot $reportCardSnapshot): bool
    {
        return false;
    }
}
