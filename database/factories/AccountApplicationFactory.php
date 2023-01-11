<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountApplication>
 */
class AccountApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $applicant = User::factory()->create();
        $applicant->assignRole('applicant');
        $role = Role::whereIn('name', ['teacher', 'parent', 'student'])->inRandomOrder()->first();

        return [
            'user_id' => $applicant->id,
            'role_id' => $role->id,
        ];
    }
}
