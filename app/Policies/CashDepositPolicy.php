<?php

namespace App\Policies;

use App\Models\CashDeposit;
use App\Models\User;

class CashDepositPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('read cash deposit');
    }

    public function view(User $user, CashDeposit $deposit): bool
    {
        return $user->can('read cash deposit') && $deposit->school_id === current_school_id();
    }

    public function create(User $user): bool
    {
        return $user->can('create cash deposit');
    }
}
