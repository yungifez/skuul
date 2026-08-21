<?php

namespace Database\Factories;

use App\Models\AccountInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AccountInvitation>
 */
class AccountInvitationFactory extends Factory
{
    protected $model = AccountInvitation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'    => User::factory()->invited(),
            'invited_by' => null,
            'token_hash' => AccountInvitation::hashToken(Str::random(64)),
            'expires_at' => now()->addHours(72),
        ];
    }

    /**
     * Indicate that the invitation passed its expiry time.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'expires_at' => now()->subHour(),
        ]);
    }

    /**
     * Indicate that an administrator stopped the invitation.
     */
    public function revoked(): static
    {
        return $this->state(fn (array $attributes): array => [
            'revoked_at' => now(),
        ]);
    }

    /**
     * Indicate that the person already used the invitation.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'accepted_at' => now(),
        ]);
    }
}
