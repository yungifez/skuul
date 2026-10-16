<?php

namespace App\Policies;

use App\Models\CourseOffering;
use App\Models\User;

class CourseOfferingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('read subject');
    }

    /**
     * Determine whether the user can open the gradebook workspace.
     */
    public function viewAnyGradebooks(User $user): bool
    {
        return $user->can('read gradebook');
    }

    public function view(User $user, CourseOffering $courseOffering): bool
    {
        return $user->can('read subject') && current_school_id() === $courseOffering->school_id;
    }

    public function create(User $user): bool
    {
        return $user->can('create subject');
    }

    public function update(User $user, CourseOffering $courseOffering): bool
    {
        return $user->can('update subject') && current_school_id() === $courseOffering->school_id;
    }

    public function delete(User $user, CourseOffering $courseOffering): bool
    {
        return $user->can('delete subject') && current_school_id() === $courseOffering->school_id;
    }

    public function restore(User $user, CourseOffering $courseOffering): bool
    {
        return false;
    }

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
        return $this->canWorkInGradebook($user, $courseOffering, 'manage gradebook', stillTeaching: true);
    }

    /**
     * Determine whether the user can make an official result visible.
     */
    public function publishResult(User $user, CourseOffering $courseOffering): bool
    {
        return $this->canWorkInGradebook($user, $courseOffering, 'publish result', stillTeaching: true);
    }

    /**
     * Determine whether the user may approve a submitted result.
     */
    public function approveResult(User $user, CourseOffering $courseOffering): bool
    {
        return $user->can('approve result')
            && current_school_id() === $courseOffering->school_id;
    }

    /**
     * Past teaching assignments permit reads; only current assignments permit edits.
     */
    private function canWorkInGradebook(User $user, CourseOffering $courseOffering, string $permission, bool $stillTeaching = false): bool
    {
        if (!$user->can($permission) || current_school_id() !== $courseOffering->school_id) {
            return false;
        }

        return $user->can('update subject')
            || $courseOffering->teachingAssignments()
                ->where('user_id', $user->id)
                ->when($stillTeaching, fn ($assignments) => $assignments->where(
                    fn ($running) => $running->whereNull('ends_on')->orWhereDate('ends_on', '>=', school_today())
                ))
                ->exists();
    }
}
