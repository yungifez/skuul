<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may read and work on cases.
 *
 * A safeguarding case is readable only by the people who handle it: the
 * permission holders, the person it is assigned to, and the person who
 * reported it.
 */
class IncidentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of cases.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read incident') || $user->can('read safeguarding case');
    }

    /**
     * Determine whether the user can read one case.
     */
    public function view(User $user, Incident $incident): bool
    {
        if ($incident->school_id !== current_school_id()) {
            return false;
        }

        if (!$incident->is_restricted) {
            return $user->can('read incident');
        }

        return $user->can('read safeguarding case')
            || $incident->assigned_to === $user->id
            || $incident->reported_by === $user->id;
    }

    /**
     * Determine whether the user can record a case.
     */
    public function create(User $user): bool
    {
        return $user->can('create incident');
    }

    /**
     * Determine whether the user can work on the case.
     */
    public function update(User $user, Incident $incident): bool
    {
        return $user->can('update incident') && $this->view($user, $incident);
    }

    /**
     * Determine whether the user can remove the case.
     */
    public function delete(User $user, Incident $incident): bool
    {
        return $user->can('delete incident') && $this->view($user, $incident);
    }
}
