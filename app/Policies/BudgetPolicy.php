<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;

class BudgetPolicy
{
    /**
     * Determine whether the user can view any budgets.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read budget');
    }

    /**
     * Determine whether the user can view the budget.
     */
    public function view(User $user, Budget $budget): bool
    {
        return $user->can('read budget') && $budget->school_id === current_school_id();
    }

    /**
     * Determine whether the user can write a budget.
     */
    public function create(User $user): bool
    {
        return $user->can('manage budget');
    }

    /**
     * Determine whether the user can revise the budget.
     */
    public function update(User $user, Budget $budget): bool
    {
        return $user->can('manage budget') && $budget->school_id === current_school_id();
    }

    /**
     * Determine whether the user can drop the budget.
     */
    public function delete(User $user, Budget $budget): bool
    {
        return $user->can('manage budget') && $budget->school_id === current_school_id();
    }
}
