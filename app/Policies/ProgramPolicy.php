<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may read and run programmes.
 *
 * A programme is a named activity a student takes part in. It carries its own
 * permissions, because the people who run a club are not always the people who
 * keep the school's cohorts.
 */
class ProgramPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of programmes.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read program');
    }

    /**
     * Determine whether the user can read one programme.
     */
    public function view(User $user, Program $program): bool
    {
        return $user->can('read program') && $program->school_id === current_school_id();
    }

    /**
     * Determine whether the user can open a programme.
     */
    public function create(User $user): bool
    {
        return $user->can('create program');
    }

    /**
     * Determine whether the user can change a programme or its places.
     */
    public function update(User $user, Program $program): bool
    {
        return $user->can('update program') && $program->school_id === current_school_id();
    }

    /**
     * Determine whether the user can remove a programme.
     */
    public function delete(User $user, Program $program): bool
    {
        return $user->can('delete program') && $program->school_id === current_school_id();
    }
}
