<?php

namespace App\Policies;

use App\Models\FeeCategory;
use App\Models\User;

class FeeCategoryPolicy
{
    public function viewAny(User $user): ?bool
    {
        if ($user->can('read fee category')) {
            return true;
        }

        return null;
    }

    public function view(User $user, FeeCategory $feeCategory): ?bool
    {
        if ($user->can('read fee category') && $feeCategory->school->id == current_school_id()) {
            return true;
        }

        return null;
    }

    public function create(User $user): ?bool
    {
        if ($user->can('create fee category')) {
            return true;
        }

        return null;
    }

    public function update(User $user, FeeCategory $feeCategory): ?bool
    {
        if ($user->can('update fee category') && $feeCategory->school->id == current_school_id()) {
            return true;
        }

        return null;
    }

    public function delete(User $user, FeeCategory $feeCategory): ?bool
    {
        if ($user->can('delete fee category') && $feeCategory->school->id == current_school_id()) {
            return true;
        }

        return null;
    }

    public function restore(User $user, FeeCategory $feeCategory): ?bool
    {
        return null;
    }

    public function forceDelete(User $user, FeeCategory $feeCategory)
    {
        //
    }
}
