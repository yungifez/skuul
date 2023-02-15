<?php

namespace App\Policies;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PromotionPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given user can view the promotion.
     *
     *
     * @return bool
     */
    public function viewAny(User $user)
    {
        if ($user->can('read promotion')) {
            return true;
        }
    }

    /**
     * Determine if the given user can create promotions.
     *
     *
     * @return bool
     */
    public function promote(User $user)
    {
        if ($user->can('promote student')) {
            return true;
        }
    }

    /**
     * Determine if the given user can reset promotion.
     *
     *
     * @return bool
     */
    public function reset(User $user)
    {
        if ($user->can('reset promotion')) {
            return true;
        }
    }

    /**
     * Determine if the given user can view the promotion.
     *
     *
     * @return bool
     */
    public function view(User $user, Promotion $promotion)
    {
        if ($user->can('read promotion') && $promotion->school_id == auth()->user()->school_id) {
            return true;
        }
    }
}
