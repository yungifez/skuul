<?php

namespace App\Policies;

use App\Models\TimetableTimeSlot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimetableTimeSlotPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, TimetableTimeSlot $timetableTimeSlot)
    {
        //
    }

    public function create(User $user)
    {
        $academicPeriod = current_academic_period();

        if ($user->can('update timetable')
            && $academicPeriod?->isOpen()
            && $academicPeriod->academicYear?->isOpen()
        ) {
            return true;
        }
    }

    public function update(User $user, TimetableTimeSlot $timetableTimeSlot)
    {
        if ($user->can('update timetable')
            && $timetableTimeSlot->timetable->acceptsChanges()
            && $timetableTimeSlot->timetable->academicPeriod->isOpen()
            && $timetableTimeSlot->timetable->academicPeriod->academicYear->isOpen()
            && current_school_id() === $timetableTimeSlot->timetable->academicCycleSection->school_id
        ) {
            return true;
        }
    }

    public function delete(User $user, TimetableTimeSlot $timetableTimeSlot)
    {
        if ($user->can('update timetable')
            && $timetableTimeSlot->timetable->acceptsChanges()
            && $timetableTimeSlot->timetable->academicPeriod->isOpen()
            && $timetableTimeSlot->timetable->academicPeriod->academicYear->isOpen()
            && current_school_id() === $timetableTimeSlot->timetable->academicCycleSection->school_id
        ) {
            return true;
        }
    }

    public function restore(User $user, TimetableTimeSlot $timetableTimeSlot)
    {
        //
    }

    public function forceDelete(User $user, TimetableTimeSlot $timetableTimeSlot)
    {
        //
    }
}
