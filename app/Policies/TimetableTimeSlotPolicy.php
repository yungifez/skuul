<?php

namespace App\Policies;

use App\Models\TimetableTimeSlot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimetableTimeSlotPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TimetableTimeSlot $timetableTimeSlot)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('update timetable')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TimetableTimeSlot $timetableTimeSlot)
    {
        if ($user->can('update timetable')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TimetableTimeSlot $timetableTimeSlot)
    {
        if ($user->can('update timetable')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TimetableTimeSlot $timetableTimeSlot)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TimetableTimeSlot $timetableTimeSlot)
    {
        //
    }
}
