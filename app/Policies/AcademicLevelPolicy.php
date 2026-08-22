<?php

namespace App\Policies;

use App\Models\AcademicLevel;
use App\Models\User;

class AcademicLevelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read class');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AcademicLevel $academicLevel): bool
    {
        return $user->can('read class') && current_school_id() === $academicLevel->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create class');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AcademicLevel $academicLevel): bool
    {
        return $user->can('update class') && current_school_id() === $academicLevel->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AcademicLevel $academicLevel): bool
    {
        return $user->can('delete class') && current_school_id() === $academicLevel->school_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AcademicLevel $academicLevel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AcademicLevel $academicLevel): bool
    {
        return false;
    }
}
