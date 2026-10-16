<?php

namespace App\Policies;

use App\Models\IncidentNote;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Keep private case notes inside the people handling the case.
 */
class IncidentNotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user may read a note.
     */
    public function view(User $user, IncidentNote $note): bool
    {
        if ($note->school_id !== current_school_id()) {
            return false;
        }

        $incident = $note->incident;

        if ($incident === null || !$user->can('view', $incident)) {
            return false;
        }

        return !$note->is_restricted
            || $user->can('read safeguarding case')
            || $incident->assigned_to === $user->id
            || $incident->reported_by === $user->id;
    }
}
