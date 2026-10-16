<?php

namespace App\Policies;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NoticePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->can('read notice')) {
            return true;
        }
    }

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

    public function create(User $user)
    {
        if ($user->can('create notice') && !$user->isPortalOnly()) {
            return true;
        }
    }

    public function update(User $user, Notice $notice)
    {
        if ($user->can('update notice') && !$user->isPortalOnly() && current_school_id() == $notice->school_id) {
            return true;
        }
    }

    public function delete(User $user, Notice $notice)
    {
        if ($user->can('delete notice') && !$user->isPortalOnly() && current_school_id() == $notice->school_id) {
            return true;
        }
    }

    public function restore(User $user, Notice $notice)
    {
        //
    }

    public function forceDelete(User $user, Notice $notice)
    {
        //
    }
}
