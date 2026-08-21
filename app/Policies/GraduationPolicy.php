<?php

namespace App\Policies;

use App\Enums\Role;
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
        if (!$model->hasRole(Role::Student)) {
            return false;
        }

        if ($user->can('reset graduation') && $model->belongsToCurrentSchool()) {
            return true;
        }
    }
}
