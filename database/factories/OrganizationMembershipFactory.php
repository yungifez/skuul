<?php

namespace Database\Factories;

use App\Enums\OrganizationMembershipStatus;
use App\Enums\OrganizationPermission;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizationMembership>
 */
class OrganizationMembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'status' => OrganizationMembershipStatus::Active,
            'permissions' => null,
            'joined_at' => now(),
            'ended_at' => null,
        ];
    }

    /**
     * Give the member only the permissions named.
     *
     * @param  list<OrganizationPermission>  $permissions
     */
    public function delegated(array $permissions): static
    {
        return $this->state(fn (): array => [
            'permissions' => array_values(array_unique(array_map(
                fn (OrganizationPermission $permission): string => $permission->value,
                [OrganizationPermission::Read, ...$permissions],
            ))),
        ]);
    }

    /**
     * Make a membership that no longer grants access.
     */
    public function ended(): static
    {
        return $this->state(fn (): array => [
            'status' => OrganizationMembershipStatus::Ended,
            'ended_at' => now(),
        ]);
    }
}
