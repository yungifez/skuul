<?php

namespace App\Policies;

use App\Models\GradingScale;
use App\Models\User;

class GradingScalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage grading scale');
    }

    public function view(User $user, GradingScale $gradingScale): bool
    {
        return $user->can('manage grading scale') && current_school_id() === $gradingScale->school_id;
    }

    public function create(User $user): bool
    {
        return $user->can('manage grading scale');
    }

    public function update(User $user, GradingScale $gradingScale): bool
    {
        return $this->view($user, $gradingScale);
    }

    public function delete(User $user, GradingScale $gradingScale): bool
    {
        return $this->view($user, $gradingScale);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GradingScale $gradingScale): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GradingScale $gradingScale): bool
    {
        return false;
    }
}
