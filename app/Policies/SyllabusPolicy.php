<?php

namespace App\Policies;

use App\Enums\SyllabusStatus;
use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SyllabusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read syllabus');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Syllabus $syllabus): bool
    {
        return $user->can('read syllabus') && current_school_id() === $syllabus->courseOffering->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create syllabus');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Syllabus $syllabus): bool
    {
        return $user->can('update syllabus') && current_school_id() === $syllabus->courseOffering->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Syllabus $syllabus): bool
    {
        return $user->can('delete syllabus')
            && $syllabus->status === SyllabusStatus::Draft
            && current_school_id() === $syllabus->courseOffering->school_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Syllabus $syllabus)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Syllabus $syllabus)
    {
        //
    }
}
