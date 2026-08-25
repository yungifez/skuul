<?php

namespace App\Policies;

use App\Models\AcademicPeriod;
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
        if ($user->can('create exam')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create an exam in a chosen period.
     */
    public function createForAcademicPeriod(User $user, AcademicPeriod $academicPeriod): bool
    {
        return $user->can('create exam')
            && $academicPeriod->school_id === current_school_id()
            && $academicPeriod->academicYear?->isOpen() === true;
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
