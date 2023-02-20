<?php

namespace App\Traits;

use App\Models\User;

trait FeatureTestTrait
{
    /**
     * Create an unauthorized user.
     */
    public function unauthorized_user(): object
    {
        $user = User::factory()->create();

        return $this->actingAs($user);
    }

    /**
     * Create an authorized user.
     */
    public function authorized_user(array $permissions): object
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        return $this->actingAs($user);
    }
}
