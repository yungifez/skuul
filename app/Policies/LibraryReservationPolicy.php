<?php

namespace App\Policies;

use App\Models\LibraryReservation;
use App\Models\User;

/**
 * Who may see and work the queue.
 */
class LibraryReservationPolicy
{
    /**
     * Determine whether the user can see the queue.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read library');
    }

    /**
     * Determine whether the user can see one reservation.
     */
    public function view(User $user, LibraryReservation $reservation): bool
    {
        return $user->can('read library') && $reservation->school_id === current_school_id();
    }

    /**
     * Determine whether the user can put somebody in the queue.
     */
    public function create(User $user): bool
    {
        return $user->can('lend library item');
    }

    /**
     * Determine whether the user can take a reservation off.
     */
    public function delete(User $user, LibraryReservation $reservation): bool
    {
        return $user->can('lend library item') && $reservation->school_id === current_school_id();
    }
}
