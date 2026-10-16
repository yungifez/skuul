<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->can('read subject')) {
            return true;
        }
    }

    public function view(User $user, Subject $subject)
    {
        if ($user->can('read subject') && current_school_id() == $subject->school_id) {
            return true;
        }
    }

    public function create(User $user)
    {
        if ($user->can('create subject')) {
            return true;
        }
    }

    public function update(User $user, Subject $subject)
    {
        if ($user->can('update subject') && current_school_id() == $subject->school_id) {
            return true;
        }
    }

    public function delete(User $user, Subject $subject)
    {
        if ($user->can('delete subject') && current_school_id() == $subject->school_id) {
            return true;
        }
    }

    public function restore(User $user, Subject $subject)
    {
        //
    }

    public function forceDelete(User $user, Subject $subject)
    {
        //
    }
}
