<?php

namespace App\Policies;

use App\Models\CustomTimetableItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomTimetableItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->can('read custom timetable item')) {
            return true;
        }
    }

    public function view(User $user, CustomTimetableItem $customTimetableItem)
    {
        //
    }

    public function create(User $user)
    {
        if ($user->can('create custom timetable item')) {
            return true;
        }
    }

    public function update(User $user, CustomTimetableItem $customTimetableItem)
    {
        if ($user->can('update custom timetable item') && current_school_id() == $customTimetableItem->school->id) {
            return true;
        }
    }

    public function delete(User $user, CustomTimetableItem $customTimetableItem)
    {
        if ($user->can('delete custom timetable item') && current_school_id() == $customTimetableItem->school->id) {
            return true;
        }
    }

    public function restore(User $user, CustomTimetableItem $customTimetableItem)
    {
        //
    }

    public function forceDelete(User $user, CustomTimetableItem $customTimetableItem)
    {
        //
    }
}
