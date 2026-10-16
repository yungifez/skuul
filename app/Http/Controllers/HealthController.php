<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Report whether the parts the application depends on are working.
 *
 * The answer says only "ok" or "failed" for each part. Details stay in the
 * logs, because this endpoint is open to the load balancer.
 */
class HealthController extends Controller
{
    /**
     * The cache key the scheduler refreshes on every run.
     */
    public const SCHEDULER_KEY = 'health:scheduler-last-run';

    /**
     * The cache key a queue worker refreshes each time it takes the heartbeat job.
     */
    public const QUEUE_KEY = 'health:queue-last-run';

    /**
     * The number of minutes a heartbeat stays fresh.
     */
    private const FRESH_MINUTES = 5;

    /**
     * Run every check and report the result.
     */
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => $this->check(function (): bool {
                DB::connection()->getPdo();

                return true;
            }),
            'cache' => $this->check(function (): bool {
                Cache::put('health:check', 'ok', 10);

                return Cache::get('health:check') === 'ok';
            }),
            'queue' => $this->check(fn (): bool => Queue::size() >= 0 && $this->workersAreTakingJobs()),
            'storage' => $this->check(fn () => Storage::disk('public')->directoryExists('') || is_writable(storage_path())),
            'scheduler' => $this->check(fn () => $this->isFresh(self::SCHEDULER_KEY)),
        ];

        $healthy = !in_array('failed', $checks, true);

        return response()->json([
            'status' => $healthy ? 'ok' : 'failed',
            'checks' => $checks,
            'time' => now()->toIso8601String(),
        ], $healthy ? 200 : 503);
    }

    /**
     * Run one check and turn any failure into a plain result.
     */
    private function check(callable $probe): string
    {
        try {
            return $probe() ? 'ok' : 'failed';
        } catch (Throwable) {
            return 'failed';
        }
    }

    /**
     * Check that a queue worker took a job recently.
     *
     * The sync connection runs every job in the request, so it has no workers.
     */
    private function workersAreTakingJobs(): bool
    {
        if (config('queue.default') === 'sync') {
            return true;
        }

        return $this->isFresh(self::QUEUE_KEY);
    }

    /**
     * Check that a heartbeat was written recently.
     *
     * A missing heartbeat fails too. It means the scheduler or the workers
     * never started, which is as broken as having stopped.
     */
    private function isFresh(string $key): bool
    {
        $lastRun = Cache::get($key);

        if ($lastRun === null) {
            return false;
        }

        return now()->diffInMinutes($lastRun, absolute: true) <= self::FRESH_MINUTES;
    }
}
