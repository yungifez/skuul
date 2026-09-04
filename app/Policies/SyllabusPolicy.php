<?php

namespace App\Policies;

use App\Enums\CourseOfferingStatus;
use App\Enums\Role;
use App\Enums\SyllabusStatus;
use App\Models\Syllabus;
use App\Models\User;
use App\Services\Gradebook\CourseOfferingRoster;
use Illuminate\Auth\Access\HandlesAuthorization;

class SyllabusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read syllabus') && !$user->isParentPortalOnly();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Syllabus $syllabus): bool
    {
        if (!$user->can('read syllabus')
            || $user->isParentPortalOnly()
            || current_school_id() !== $syllabus->courseOffering->school_id
        ) {
            return false;
        }

        if (!$user->hasRole(Role::Student)) {
            return true;
        }

        $enrollment = $user->studentRecord()->attending()->first();

        return $syllabus->status === SyllabusStatus::Published
            && $syllabus->courseOffering->status === CourseOfferingStatus::Active
            && $enrollment !== null
            && app(CourseOfferingRoster::class)->includes($syllabus->courseOffering, $enrollment);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create syllabus') && !$user->isPortalOnly();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Syllabus $syllabus): bool
    {
        return $user->can('update syllabus')
            && !$user->isPortalOnly()
            && current_school_id() === $syllabus->courseOffering->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Syllabus $syllabus): bool
    {
        return $user->can('delete syllabus')
            && !$user->isPortalOnly()
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
