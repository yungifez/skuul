<?php

namespace App\Policies;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PromotionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can view the promotion.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read promotion')) {
            return true;
        }
    }

    /**
     * Determine if the given user can create promotions.
     */
    public function promote(User $user)
    {
        if ($user->can('promote student')) {
            return true;
        }
    }

    /**
     * Determine if the given user can reset promotion.
     */
    public function reset(User $user)
    {
        if ($user->can('reset promotion')) {
            return true;
        }
    }

    /**
     * Determine if the given user can view the promotion.
     */
    public function view(User $user, Promotion $promotion)
    {
        if ($user->can('read promotion') && $promotion->school_id == auth()->user()->school_id) {
            return true;
        }
    }
}
