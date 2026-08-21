<?php

namespace Database\Factories;

use App\Actions\Authorization\GrantSystemRole;
use App\Actions\School\GrantSchoolMembership;
use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\School;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(10)),
            'remember_token' => Str::random(10),
            'address' => $this->faker->address(),
            'birthday' => $this->faker->date(),
            'address' => $this->faker->address(),
            'nationality' => $this->faker->country(),
            'state' => 'wyoming',
            'city' => $this->faker->city(),
            'gender' => 'male',
            'account_status' => AccountStatus::Active,
        ];
    }

    /**
     * Give every created person access to the school being worked in.
     *
     * School access is a membership record now, so a user with no membership
     * can reach nothing. Tests that need that case delete the membership.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            if ($user->schoolMemberships()->exists()) {
                return;
            }

            $school = current_school() ?? School::first();

            if ($school !== null) {
                app(GrantSchoolMembership::class)->grant($user, $school, primary: true);
            }
        });
    }

    /**
     * Indicate that this person administers the whole platform.
     */
    public function platformAdmin(): static
    {
        return $this->afterCreating(fn (User $user) => app(GrantSystemRole::class)->grant($user, Role::PlatformAdmin));
    }

    /**
     * Indicate that the account is provisioned but has no password yet.
     */
    public function invited(): static
    {
        return $this->state(fn (array $attributes): array => [
            'password' => null,
            'account_status' => AccountStatus::Invited,
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that an administrator stopped access to the account.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes): array => [
            'account_status' => AccountStatus::Suspended,
        ]);
    }

    /**
     * Indicate that the account is closed but readable for history.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes): array => [
            'account_status' => AccountStatus::Archived,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user should have a personal team.
     *
     * @return $this
     */
    public function withPersonalTeam()
    {
        if (!Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(function (array $attributes, User $user) {
                    return ['name' => $user->name.'\'s Team', 'user_id' => $user->id, 'personal_team' => true];
                }),
            'ownedTeams'
        );
    }
}
