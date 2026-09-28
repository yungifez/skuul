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

    public function test_every_job_but_the_heartbeat_runs_on_one_server(): void
    {
        $events = collect(Schedule::events());
        $heartbeat = $events->filter(fn ($event): bool => $event->description === 'scheduler-heartbeat');

        $this->assertCount(1, $heartbeat);
        $this->assertFalse($heartbeat->first()->onOneServer, 'Each server reports its own scheduler.');
        $events->reject(fn ($event): bool => $event->description === 'scheduler-heartbeat')
            ->each(fn ($event) => $this->assertTrue($event->onOneServer, "$event->command runs on every server."));
    }
}
