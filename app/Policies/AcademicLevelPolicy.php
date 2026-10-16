<?php

namespace App\Policies;

use App\Models\AcademicLevel;
use App\Models\User;

class AcademicLevelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('read class');
    }

    public function view(User $user, AcademicLevel $academicLevel): bool
    {
        return $user->can('read class') && current_school_id() === $academicLevel->school_id;
    }

    public function create(User $user): bool
    {
        return $user->can('create class');
    }

    public function update(User $user, AcademicLevel $academicLevel): bool
    {
        return $user->can('update class') && current_school_id() === $academicLevel->school_id;
    }

    public function delete(User $user, AcademicLevel $academicLevel): bool
    {
        return $user->can('delete class') && current_school_id() === $academicLevel->school_id;
    }

    public function restore(User $user, AcademicLevel $academicLevel): bool
    {
        return false;
    }

    public function forceDelete(User $user, AcademicLevel $academicLevel): bool
    {
        return false;
    }
}
