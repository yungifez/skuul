<?php

use App\Console\Commands\CheckBackup;
use App\Console\Commands\ProcessNotices;
use App\Console\Commands\PruneExpiredInvitations;
use App\Http\Controllers\HealthController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands and the work the scheduler runs.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled work
|--------------------------------------------------------------------------
|
| Run `php artisan schedule:work` in development and a cron entry calling
| `php artisan schedule:run` every minute in production.
|
*/

// Say the scheduler is alive, so the health endpoint can see it.
Schedule::call(function (): void {
    Cache::put(HealthController::SCHEDULER_KEY, now(), now()->addHour());
})->everyMinute()->name('scheduler-heartbeat');

// Close invitation links nobody used.
Schedule::command(PruneExpiredInvitations::class)->hourly()->withoutOverlapping();

// Put scheduled notices on the board and take finished ones down.
Schedule::command(ProcessNotices::class)->everyFifteenMinutes()->withoutOverlapping();

// Say early when the backups stopped arriving.
Schedule::command(CheckBackup::class)->dailyAt('07:00');

// Keep the failed job table and old batches from growing without limit.
Schedule::command('queue:prune-failed --hours=336')->daily();
Schedule::command('queue:prune-batches --hours=336')->daily();
