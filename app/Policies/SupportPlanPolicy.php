<?php

namespace App\Policies;

use App\Models\SupportPlan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may read and work on support plans.
 *
 * A health or counselling plan is readable only by the people who run it: the
 * permission holders, the person it is assigned to, and the person who
 * wrote it.
 */
class SupportPlanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of plans.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read support plan') || $user->can('read confidential support plan');
    }

    /**
     * Determine whether the user can read one plan.
     */
    public function view(User $user, SupportPlan $plan): bool
    {
        if ($plan->school_id !== current_school_id()) {
            return false;
        }

        if (!$plan->is_confidential) {
            return $user->can('read support plan');
        }

        return $user->can('read confidential support plan')
            || $plan->assigned_to === $user->id
            || $plan->created_by === $user->id;
    }

    /**
     * Determine whether the user can write a plan.
     */
    public function create(User $user): bool
    {
        return $user->can('create support plan');
    }

    /**
     * Determine whether the user can work on the plan.
     */
    public function update(User $user, SupportPlan $plan): bool
    {
        return $user->can('update support plan') && $this->view($user, $plan);
    }

    /**
     * Determine whether the user can remove the plan.
     */
    public function delete(User $user, SupportPlan $plan): bool
    {
        return $user->can('delete support plan') && $this->view($user, $plan);
    }
}
