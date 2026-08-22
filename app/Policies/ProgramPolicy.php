<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may read and run programmes.
 *
 * A programme is a named activity a student takes part in, so it is kept by
 * the same people who keep cohorts and carries the same permissions.
 */
class ProgramPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of programmes.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read cohort');
    }

    /**
     * Determine whether the user can read one programme.
     */
    public function view(User $user, Program $program): bool
    {
        return $user->can('read cohort') && $program->school_id === current_school_id();
    }

    /**
     * Determine whether the user can open a programme.
     */
    public function create(User $user): bool
    {
        return $user->can('create cohort');
    }

    /**
     * Determine whether the user can change a programme or its places.
     */
    public function update(User $user, Program $program): bool
    {
        return $user->can('update cohort') && $program->school_id === current_school_id();
    }

    /**
     * Determine whether the user can remove a programme.
     */
    public function delete(User $user, Program $program): bool
    {
        return $user->can('delete cohort') && $program->school_id === current_school_id();
    }
}
