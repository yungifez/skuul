<?php

namespace App\Policies;

use App\Models\GraduationPlan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may read and keep graduation plans.
 *
 * Reading a plan says what a learner must finish. Keeping one says what the
 * school will accept, which is a decision fewer people make.
 */
class GraduationPlanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of plans.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read graduation plan');
    }

    /**
     * Determine whether the user can read one plan.
     */
    public function view(User $user, GraduationPlan $plan): bool
    {
        return $user->can('read graduation plan') && $plan->school_id === current_school_id();
    }

    /**
     * Determine whether the user can write a plan.
     */
    public function create(User $user): bool
    {
        return $user->can('manage graduation plan');
    }

    /**
     * Determine whether the user can change a plan or its requirements.
     */
    public function update(User $user, GraduationPlan $plan): bool
    {
        return $user->can('manage graduation plan') && $plan->school_id === current_school_id();
    }

    /**
     * Determine whether the user can remove a plan.
     */
    public function delete(User $user, GraduationPlan $plan): bool
    {
        return $this->update($user, $plan);
    }
}
