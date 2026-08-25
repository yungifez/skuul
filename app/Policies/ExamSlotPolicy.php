<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\ExamSlot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamSlotPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read exam slot')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExamSlot $examSlot)
    {
        if ($user->can('read exam slot') && $examSlot->exam->academicPeriod->school_id == current_school_id()) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->can('create exam slot');
    }

    /**
     * Determine whether the user can create a slot in this exam.
     */
    public function createForExam(User $user, Exam $exam): bool
    {
        return $user->can('create exam slot')
            && $exam->academicPeriod->status->acceptsExamPlanning()
            && $exam->academicPeriod->academicYear?->status->acceptsExamPlanning() === true
            && $exam->academicPeriod->school_id === current_school_id();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExamSlot $examSlot)
    {
        if ($user->can('update exam slot')
            && $examSlot->exam->academicPeriod->status->acceptsExamPlanning()
            && $examSlot->exam->academicPeriod->academicYear->status->acceptsExamPlanning()
            && $examSlot->exam->academicPeriod->school_id == current_school_id()
        ) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExamSlot $examSlot)
    {
        if ($user->can('delete exam slot')
            && $examSlot->exam->academicPeriod->status->acceptsExamPlanning()
            && $examSlot->exam->academicPeriod->academicYear->status->acceptsExamPlanning()
            && $examSlot->exam->academicPeriod->school_id == current_school_id()
        ) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExamSlot $examSlot)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExamSlot $examSlot)
    {
        //
    }
}
