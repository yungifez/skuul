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
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if ($user->can('read notice')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Notice $notice
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Notice $notice)
    {
        if ($user->can('read notice') && $user->school_id == $notice->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->can('create notice')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Notice $notice
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Notice $notice)
    {
        if ($user->can('update notice') && $user->school_id == $notice->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Notice $notice
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Notice $notice)
    {
        if ($user->can('delete notice') && $user->school_id == $notice->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Notice $notice
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Notice $notice)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Notice $notice
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Notice $notice)
    {
        //
    }
}
