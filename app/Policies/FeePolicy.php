<?php

namespace App\Policies;

use App\Models\Fee;
use App\Models\User;

class FeePolicy
{
    public function viewAny(User $user)
    {
        if ($user->can('read fee')) {
            return true;
        }
    }

    public function view(User $user, Fee $fee)
    {
        if ($user->can('read fee') && $fee->feeCategory->school_id == current_school_id()) {
            return true;
        }
    }

    public function create(User $user)
    {
        if ($user->can('create fee')) {
            return true;
        }
    }

    public function update(User $user, Fee $fee)
    {
        if ($user->can('update fee') && $fee->feeCategory->school_id == current_school_id()) {
            return true;
        }
    }

    public function delete(User $user, Fee $fee)
    {
        if ($user->can('delete fee') && $fee->feeCategory->school_id == current_school_id()) {
            return true;
        }
    }

    public function restore(User $user, Fee $fee)
    {
        //
    }

    public function forceDelete(User $user, Fee $fee)
    {
        //
    }
}
