<?php

namespace App\Policies;

use App\Models\CurriculumOutline;
use App\Models\User;

class CurriculumOutlinePolicy
{
    /**
     * Determine whether the user can browse the curriculum library.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read syllabus') && !$user->isPortalOnly();
    }

    /**
     * Determine whether the user can add outlines to the library.
     */
    public function create(User $user): bool
    {
        return $user->can('approve syllabus') && !$user->isPortalOnly();
    }

    /**
     * Determine whether the user can remove an outline from the library.
     */
    public function delete(User $user, CurriculumOutline $curriculumOutline): bool
    {
        return $this->create($user) && current_school_id() === $curriculumOutline->school_id;
    }
}
