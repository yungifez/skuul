<?php

namespace App\Policies;

use App\Models\ReportRun;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may ask for reports and read the files they produce.
 */
class ReportRunPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of reports.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read report');
    }

    /**
     * Determine whether the user can read one report.
     *
     * A report holds whole-school data, so it never leaves its school.
     */
    public function view(User $user, ReportRun $reportRun): bool
    {
        return $user->can('read report') && $reportRun->school_id === current_school_id();
    }

    /**
     * Determine whether the user can ask for a report.
     */
    public function create(User $user): bool
    {
        return $user->can('create report');
    }

    /**
     * Determine whether the user can download the file.
     */
    public function download(User $user, ReportRun $reportRun): bool
    {
        return $this->view($user, $reportRun);
    }
}
