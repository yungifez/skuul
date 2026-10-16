<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('read expense');
    }

    public function view(User $user, Expense $expense): bool
    {
        return $user->can('read expense') && $expense->school_id === current_school_id();
    }

    public function create(User $user): bool
    {
        return $user->can('create expense');
    }
}
