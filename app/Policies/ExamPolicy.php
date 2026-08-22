<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read exam')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Exam $exam)
    {
        if ($user->can('read exam') && $exam->academicPeriod->school_id == current_school_id()) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        $academicPeriod = current_academic_period();

        if ($user->can('create exam')
            && $academicPeriod?->isOpen()
            && $academicPeriod->academicYear?->isOpen()
        ) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Exam $exam)
    {
        if ($user->can('update exam')
            && $exam->academicPeriod->isOpen()
            && $exam->academicPeriod->academicYear->isOpen()
            && $exam->academicPeriod->school_id == current_school_id()
        ) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Exam $exam)
    {
        if ($user->can('delete exam')
            && $exam->academicPeriod->isOpen()
            && $exam->academicPeriod->academicYear->isOpen()
            && $exam->academicPeriod->school_id == current_school_id()
        ) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Exam $exam)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Exam $exam)
    {
        //
    }
}
