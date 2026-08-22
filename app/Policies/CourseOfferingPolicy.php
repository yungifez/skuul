<?php

namespace App\Policies;

use App\Models\CourseOffering;
use App\Models\User;

class CourseOfferingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read subject');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CourseOffering $courseOffering): bool
    {
        return $user->can('read subject') && current_school_id() === $courseOffering->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create subject');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CourseOffering $courseOffering): bool
    {
        return $user->can('update subject') && current_school_id() === $courseOffering->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CourseOffering $courseOffering): bool
    {
        return $user->can('delete subject') && current_school_id() === $courseOffering->school_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CourseOffering $courseOffering): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CourseOffering $courseOffering): bool
    {
        return false;
    }

    /**
     * Determine whether the user can read the offering's gradebook.
     */
    public function viewGradebook(User $user, CourseOffering $courseOffering): bool
    {
        return $this->canWorkInGradebook($user, $courseOffering, 'read gradebook');
    }

    /**
     * Determine whether the user can configure or record grades.
     */
    public function manageGradebook(User $user, CourseOffering $courseOffering): bool
    {
        return $this->canWorkInGradebook($user, $courseOffering, 'manage gradebook');
    }

    /**
     * Determine whether the user can make an official result visible.
     */
    public function publishResult(User $user, CourseOffering $courseOffering): bool
    {
        return $this->canWorkInGradebook($user, $courseOffering, 'publish result');
    }

    private function canWorkInGradebook(User $user, CourseOffering $courseOffering, string $permission): bool
    {
        if (!$user->can($permission) || current_school_id() !== $courseOffering->school_id) {
            return false;
        }

        return $user->can('update subject')
            || $courseOffering->teachingAssignments()->where('user_id', $user->id)->exists();
    }
}
