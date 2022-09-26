<?php

namespace App\Policies;

use App\Models\ExamSlot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamSlotPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if ($user->can('read exam slot')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\ExamSlot $examSlot
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ExamSlot $examSlot)
    {
        if ($user->can('read exam slot') && $examSlot->exam->semester->school_id == $user->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->can('create exam slot')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\ExamSlot $examSlot
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ExamSlot $examSlot)
    {
        if ($user->can('update exam slot') && $examSlot->exam->semester->school_id == $user->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\ExamSlot $examSlot
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ExamSlot $examSlot)
    {
        if ($user->can('delete exam slot') && $examSlot->exam->semester->school_id == $user->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\ExamSlot $examSlot
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ExamSlot $examSlot)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\ExamSlot $examSlot
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ExamSlot $examSlot)
    {
        //
    }
}
