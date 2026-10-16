<?php

namespace App\Policies;

use App\Models\AcademicCycleSection;
use App\Models\User;

class AcademicCycleSectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('read section');
    }

    public function view(User $user, AcademicCycleSection $academicCycleSection): bool
    {
        return $user->can('read section') && current_school_id() === $academicCycleSection->school_id;
    }

    public function create(User $user): bool
    {
        return $user->can('create section');
    }

    public function update(User $user, AcademicCycleSection $academicCycleSection): bool
    {
        return $user->can('update section') && current_school_id() === $academicCycleSection->school_id;
    }

    public function delete(User $user, AcademicCycleSection $academicCycleSection): bool
    {
        return false;
    }

    public function restore(User $user, AcademicCycleSection $academicCycleSection): bool
    {
        return false;
    }

    public function forceDelete(User $user, AcademicCycleSection $academicCycleSection): bool
    {
        return false;
    }
}
