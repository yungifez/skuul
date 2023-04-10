<?php

namespace App\Policies;

use App\Models\User;

class GraduationPolicy
{
    public function viewAny(User $user)
    {
        if ($user->can('view graduations')) {
            return true;
        }
    }

    public function graduate(User $user)
    {
        if ($user->can('graduate student')) {
            return true;
        }
    }

    public function resetGraduation(User $user, User $model)
    {
        if (!$model->hasRole('student')) {
            return false;
        }

        if ($user->can('reset graduation') && $model->school_id == $user->school_id) {
            return true;
        }
    }
}
