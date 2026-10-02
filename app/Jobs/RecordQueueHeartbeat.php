<?php

namespace App\Jobs;

use App\Http\Controllers\HealthController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

/**
 * Say that a queue worker is taking jobs.
 *
 * The scheduler puts this job on the queue every minute. Only a running worker
 * can take it off, so a stale heartbeat means no worker is running.
 */
class RecordQueueHeartbeat implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::forever(HealthController::QUEUE_KEY, now());
    }
}
