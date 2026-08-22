<?php

namespace App\Policies;

use App\Models\AcademicCycleSection;
use App\Models\User;

class AcademicCycleSectionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read section');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AcademicCycleSection $academicCycleSection): bool
    {
        return $user->can('read section') && current_school_id() === $academicCycleSection->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create section');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AcademicCycleSection $academicCycleSection): bool
    {
        return $user->can('update section') && current_school_id() === $academicCycleSection->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AcademicCycleSection $academicCycleSection): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AcademicCycleSection $academicCycleSection): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AcademicCycleSection $academicCycleSection): bool
    {
        return false;
    }
}
