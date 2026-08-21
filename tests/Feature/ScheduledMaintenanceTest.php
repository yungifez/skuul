<?php

namespace Tests\Feature;

use App\Models\AccountInvitation;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schedule;
use Tests\TestCase;

/**
 * Recurring work keeps invitations and queue tables tidy.
 */
class ScheduledMaintenanceTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_expired_invitations_are_revoked(): void
    {
        $user = $this->memberOf($this->workingSchool());

        $expired = AccountInvitation::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->subDay(),
        ]);
        $pending = AccountInvitation::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->addDay(),
        ]);

        $this->artisan('skuul:prune-expired-invitations')->assertSuccessful();

        $this->assertNotNull($expired->fresh()->revoked_at);
        $this->assertNull($pending->fresh()->revoked_at);
    }

    public function test_an_accepted_invitation_is_left_alone(): void
    {
        $user = $this->memberOf($this->workingSchool());

        $accepted = AccountInvitation::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->subDay(),
            'accepted_at' => now()->subDays(2),
        ]);

        $this->artisan('skuul:prune-expired-invitations')->assertSuccessful();

        $this->assertNull($accepted->fresh()->revoked_at);
    }

    public function test_the_maintenance_work_is_scheduled(): void
    {
        $commands = collect(Schedule::events())->map(fn ($event): string => $event->command ?? '');

        $this->assertTrue($commands->contains(fn (string $command): bool => str_contains($command, 'skuul:prune-expired-invitations')));
        $this->assertTrue($commands->contains(fn (string $command): bool => str_contains($command, 'queue:prune-failed')));
    }
}
