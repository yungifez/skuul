<?php

namespace Tests\Feature;

use App\Http\Controllers\HealthController;
use App\Jobs\RecordQueueHeartbeat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * The health endpoint reports the parts the application depends on.
 */
class HealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_health_endpoint_is_open_and_reports_every_check(): void
    {
        Cache::forever(HealthController::SCHEDULER_KEY, now());

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

    public function test_a_scheduler_that_never_ran_makes_the_check_fail(): void
    {
        Cache::forget(HealthController::SCHEDULER_KEY);

        $this->get('/health')
            ->assertStatus(503)
            ->assertJsonPath('checks.scheduler', 'failed');
    }

    public function test_the_scheduler_heartbeat_does_not_expire_into_a_pass(): void
    {
        $this->travel(-3)->hours();
        Cache::forget(HealthController::SCHEDULER_KEY);
        $this->artisan('schedule:run');
        $this->travelBack();

        $this->assertNotNull(Cache::get(HealthController::SCHEDULER_KEY));
        $this->get('/health')->assertJsonPath('checks.scheduler', 'failed');
    }

    public function test_a_queue_with_no_worker_taking_jobs_makes_the_check_fail(): void
    {
        $this->useAQueueThatNeedsWorkers();
        Cache::forever(HealthController::SCHEDULER_KEY, now());

        $this->get('/health')
            ->assertStatus(503)
            ->assertJsonPath('checks.queue', 'failed');

        Cache::forever(HealthController::QUEUE_KEY, now()->subMinutes(10));

        $this->get('/health')->assertJsonPath('checks.queue', 'failed');
    }

    public function test_a_worker_taking_the_heartbeat_job_keeps_the_queue_healthy(): void
    {
        $this->useAQueueThatNeedsWorkers();
        Cache::forever(HealthController::SCHEDULER_KEY, now());

        (new RecordQueueHeartbeat)->handle();

        $this->get('/health')
            ->assertOk()
            ->assertJsonPath('checks.queue', 'ok');
    }

    public function test_the_scheduler_puts_the_heartbeat_job_on_the_queue(): void
    {
        Queue::fake();

        $this->artisan('schedule:run');

        Queue::assertPushed(RecordQueueHeartbeat::class);
    }

    /**
     * Switch to a queue connection that needs workers but has no backend to reach.
     */
    private function useAQueueThatNeedsWorkers(): void
    {
        config(['queue.connections.workers' => ['driver' => 'null'], 'queue.default' => 'workers']);
    }
}
