<?php

namespace Tests\Feature;

use App\Http\Controllers\HealthController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * The health endpoint reports the parts the application depends on.
 */
class HealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_health_endpoint_is_open_and_reports_every_check(): void
    {
        $this->get('/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('checks.database', 'ok')
            ->assertJsonPath('checks.cache', 'ok')
            ->assertJsonPath('checks.queue', 'ok')
            ->assertJsonPath('checks.storage', 'ok')
            ->assertJsonPath('checks.scheduler', 'ok');
    }

    public function test_a_stale_scheduler_makes_the_check_fail(): void
    {
        Cache::put(HealthController::SCHEDULER_KEY, now()->subHour());

        $this->get('/health')
            ->assertStatus(503)
            ->assertJsonPath('status', 'failed')
            ->assertJsonPath('checks.scheduler', 'failed');
    }
}
