<?php

namespace App\Policies;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NoticePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read notice')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Notice $notice)
    {
        if (!$user->can('read notice') || current_school_id() !== $notice->school_id) {
            return false;
        }

        if (!$notice->isPublished()) {
            return !$user->isPortalOnly() && ($user->can('update notice') || $user->can('delete notice'));
        }

        if ($user->isPortalOnly()) {
            return $notice->recipients()->where('user_id', $user->id)->exists();
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('create notice') && !$user->isPortalOnly()) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Notice $notice)
    {
        if ($user->can('update notice') && !$user->isPortalOnly() && current_school_id() == $notice->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Notice $notice)
    {
        if ($user->can('delete notice') && !$user->isPortalOnly() && current_school_id() == $notice->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Notice $notice)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Notice $notice)
    {
        //
    }
}
