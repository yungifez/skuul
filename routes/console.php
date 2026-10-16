<?php

use App\Console\Commands\AdvanceAcademicCalendar;
use App\Console\Commands\CheckBackup;
use App\Console\Commands\CreateBackup;
use App\Console\Commands\EndLeaversAccess;
use App\Console\Commands\GenerateUpcomingAcademicCycles;
use App\Console\Commands\ProcessLibraryHolds;
use App\Console\Commands\ProcessNotices;
use App\Console\Commands\PruneExpiredInvitations;
use App\Console\Commands\RefreshDemoData;
use App\Console\Commands\RehearseRestore;
use App\Console\Commands\SendAcademicCalendarReminders;
use App\Console\Commands\SendSyllabusBehindReminders;
use App\Http\Controllers\HealthController;
use App\Jobs\RecordQueueHeartbeat;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Use one server for scheduled work; each server records its own scheduler heartbeat.
Schedule::call(function (): void {
    Cache::forever(HealthController::SCHEDULER_KEY, now());
})->everyMinute()->name('scheduler-heartbeat');

Schedule::job(new RecordQueueHeartbeat)->everyMinute()->name('queue-heartbeat')->onOneServer();

Schedule::command(PruneExpiredInvitations::class)->hourly()->withoutOverlapping()->onOneServer();

Schedule::command(ProcessLibraryHolds::class)->dailyAt('06:00')->withoutOverlapping()->onOneServer();

Schedule::command(ProcessNotices::class)->everyFifteenMinutes()->withoutOverlapping()->onOneServer();

Schedule::command(EndLeaversAccess::class)->dailyAt('00:15')->withoutOverlapping()->onOneServer();

// Automatic advancement only opens periods. Closure requires confirmation.
Schedule::command(AdvanceAcademicCalendar::class)->dailyAt('00:30')->withoutOverlapping()->onOneServer();

Schedule::command(GenerateUpcomingAcademicCycles::class)->weeklyOn(1, '01:00')->withoutOverlapping()->onOneServer();

Schedule::command(SendAcademicCalendarReminders::class)->dailyAt('07:15')->withoutOverlapping()->onOneServer();

Schedule::command(SendSyllabusBehindReminders::class)->weeklyOn(1, '07:30')->withoutOverlapping()->onOneServer();

// Include uploaded files so restored database references have matching files.
Schedule::command(CreateBackup::class, ['--with-files'])->dailyAt('01:30')->withoutOverlapping()->onOneServer();

// Restore only with a rehearsal connection; otherwise inspect the backup.
Schedule::command(RehearseRestore::class)->weeklyOn(7, '03:00')->withoutOverlapping()->onOneServer();

Schedule::command(CheckBackup::class)->dailyAt('07:00')->onOneServer();

Schedule::command(RefreshDemoData::class)->hourly()->when(fn (): bool => (bool) config('demo.enabled'))->withoutOverlapping()->onOneServer();

Schedule::command('queue:prune-failed --hours=336')->daily()->onOneServer();
Schedule::command('queue:prune-batches --hours=336')->daily()->onOneServer();
