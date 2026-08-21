<?php

namespace Tests\Feature;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

/**
 * Slow work and lost work are reported instead of staying silent.
 */
class MonitoringTest extends TestCase
{
    public function test_a_slow_query_is_logged(): void
    {
        Log::spy();

        Event::dispatch($this->slowQueryEvent((float) config('monitoring.slow_query_ms') + 1));

        Log::shouldHaveReceived('warning')->withArgs(fn (string $message): bool => $message === 'Slow query')->once();
    }

    public function test_a_fast_query_is_not_logged(): void
    {
        Log::spy();

        Event::dispatch($this->slowQueryEvent(1.0));

        Log::shouldNotHaveReceived('warning');
    }

    public function test_a_failed_job_is_logged(): void
    {
        Log::spy();

        $job = Mockery::mock(Job::class);
        $job->shouldReceive('getQueue')->andReturn('default');
        $job->shouldReceive('resolveName')->andReturn('App\Jobs\SendReport');
        $job->shouldReceive('attempts')->andReturn(3);

        Event::dispatch(new JobFailed('redis', $job, new RuntimeException('Mail server refused the message')));

        Log::shouldHaveReceived('error')->withArgs(fn (string $message): bool => $message === 'Queue job failed')->once();
    }

    public function test_the_backup_check_passes_with_a_recent_backup(): void
    {
        Storage::fake('backups');
        config(['monitoring.backup.disk' => 'backups', 'monitoring.backup.path' => 'backups', 'monitoring.backup.max_age_hours' => 26]);

        Storage::disk('backups')->put('backups/skuul-2026-08-21.sql.gz', 'dump');

        $this->artisan('skuul:check-backup')->assertSuccessful();
    }

    public function test_the_backup_check_fails_when_no_backup_exists(): void
    {
        Storage::fake('backups');
        config(['monitoring.backup.disk' => 'backups', 'monitoring.backup.path' => 'backups']);

        $this->artisan('skuul:check-backup')->assertFailed();
    }

    public function test_the_backup_check_fails_when_the_newest_backup_is_too_old(): void
    {
        Storage::fake('backups');
        config(['monitoring.backup.disk' => 'backups', 'monitoring.backup.path' => 'backups', 'monitoring.backup.max_age_hours' => 26]);

        Storage::disk('backups')->put('backups/skuul-old.sql.gz', 'dump');
        touch(Storage::disk('backups')->path('backups/skuul-old.sql.gz'), now()->subDays(3)->getTimestamp());

        $this->artisan('skuul:check-backup')->assertFailed();
    }

    /**
     * Build a query event that took the given number of milliseconds.
     */
    private function slowQueryEvent(float $milliseconds): QueryExecuted
    {
        return new QueryExecuted('select * from users', [], $milliseconds, DB::connection());
    }

    public function test_the_backup_check_is_scheduled(): void
    {
        $commands = collect(Schedule::events())->map(fn ($event): string => $event->command ?? '');

        $this->assertTrue($commands->contains(fn (string $command): bool => str_contains($command, 'skuul:check-backup')));
    }
}
