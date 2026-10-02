<?php

namespace App\Providers;

use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

/**
 * Report the two failures that stay invisible without help.
 *
 * A slow query and a failed job both let the application keep answering while
 * the work behind it gets worse, so both are written to the log with enough
 * detail to find them again.
 */
class MonitoringServiceProvider extends ServiceProvider
{
    /**
     * Register the listeners.
     */
    public function boot(): void
    {
        $this->reportSlowQueries();
        $this->reportSlowRequests();
        $this->reportFailedJobs();
    }

    /**
     * Write a warning for every query that runs too long.
     */
    private function reportSlowQueries(): void
    {
        $threshold = (int) config('monitoring.slow_query_ms');

        if ($threshold <= 0) {
            return;
        }

        DB::listen(function (QueryExecuted $query) use ($threshold): void {
            if ($query->time < $threshold) {
                return;
            }

            Log::warning('Slow query', [
                'connection' => $query->connectionName,
                'milliseconds' => $query->time,
                'sql' => $query->sql,
                'bindings' => $query->bindings,
            ]);
        });
    }

    /**
     * Write a warning when one request spends too long in the database.
     *
     * Many fast queries can be slower than one slow query, and only this
     * total shows it.
     */
    private function reportSlowRequests(): void
    {
        $threshold = (int) config('monitoring.slow_request_query_ms');

        if ($threshold <= 0) {
            return;
        }

        DB::whenQueryingForLongerThan($threshold, function (Connection $connection): void {
            Log::warning('Slow request', [
                'connection' => $connection->getName(),
                'milliseconds' => $connection->totalQueryDuration(),
                'path' => request()->path(),
            ]);
        });
    }

    /**
     * Write an error for every job the queue gives up on.
     */
    private function reportFailedJobs(): void
    {
        Queue::failing(function (JobFailed $event): void {
            Log::error('Queue job failed', [
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'job' => $event->job->resolveName(),
                'attempts' => $event->job->attempts(),
                'exception' => $event->exception->getMessage(),
            ]);
        });
    }
}
