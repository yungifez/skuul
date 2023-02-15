<?php

namespace App\Policies;

use App\Models\CustomTimetableItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomTimetableItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if ($user->can('read custom timetable item')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CustomTimetableItem $customTimetableItem)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->can('create custom timetable item')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CustomTimetableItem $customTimetableItem)
    {
        if ($user->can('update custom timetable item') && $user->school_id == $customTimetableItem->school->id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CustomTimetableItem $customTimetableItem)
    {
        if ($user->can('delete custom timetable item') && $user->school_id == $customTimetableItem->school->id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, CustomTimetableItem $customTimetableItem)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, CustomTimetableItem $customTimetableItem)
    {
        //
    }
}
