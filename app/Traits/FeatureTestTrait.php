<?php

namespace App\Traits;

use App\Models\User;

trait FeatureTestTrait
{
    /**
     * Create an unauthorized user
     */
    public function unauthorized_user(): Object
    {
        $user = User::factory()->create();

        return $this->actingAs($user);
    }

    /**
     * Create an authorized user
     *
     * @param array $permission
     */
    public function authorized_user(array $permission): Object
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        return $this->actingAs($user);
    }
}
