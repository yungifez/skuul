<?php

namespace App\Traits;

use App\Models\User;

trait FeatureTestTrait
{
    function unauthorized_user()
    {
        $user = User::factory()->create();
        return $this->actingAs($user);
    }
    function authorized_user(array $permission)
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permission);
        return $this->actingAs($user);
    }
}
