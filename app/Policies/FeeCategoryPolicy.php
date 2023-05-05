<?php

namespace App\Policies;

use App\Models\FeeCategory;
use App\Models\User;

class FeeCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): ?bool
    {
        if ($user->can('read fee category')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FeeCategory $feeCategory): ?bool
    {
        if ($user->can('read fee category') && $feeCategory->school->id == auth()->user()->school->id) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): ?bool
    {
        if ($user->can('create fee category')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FeeCategory $feeCategory): ?bool
    {
        if ($user->can('update fee category') && $feeCategory->school->id == auth()->user()->school->id) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FeeCategory $feeCategory): ?bool
    {
        if ($user->can('delete fee category') && $feeCategory->school->id == auth()->user()->school->id) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FeeCategory $feeCategory): ?bool
    {
        return null;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FeeCategory $feeCategory)
    {
        //
    }
}
