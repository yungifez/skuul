<?php

namespace App\Traits;

use App\Models\User;

trait FeatureTestTrait
{
    public function unauthorized_user()
    {
        $user = User::factory()->create();

        return $this->actingAs($user);
    }

    public function authorized_user(array $permission)
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        return $this->actingAs($user);
    }
}
