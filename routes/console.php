<?php

use App\Console\Commands\AdvanceAcademicCalendar;
use App\Console\Commands\CheckBackup;
use App\Console\Commands\CreateBackup;
use App\Console\Commands\GenerateUpcomingAcademicCycles;
use App\Console\Commands\ProcessLibraryHolds;
use App\Console\Commands\ProcessNotices;
use App\Console\Commands\PruneExpiredInvitations;
use App\Console\Commands\RehearseRestore;
use App\Console\Commands\SendAcademicCalendarReminders;
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

// End library holds nobody came for, so the copy reaches the next person in
// the queue instead of waiting behind the desk.
Schedule::command(ProcessLibraryHolds::class)->dailyAt('06:00')->withoutOverlapping();

// Put scheduled notices on the board and take finished ones down.
Schedule::command(ProcessNotices::class)->everyFifteenMinutes()->withoutOverlapping();

// Open the periods whose first day has arrived. This never closes one:
// closing freezes records, so a person confirms it.
Schedule::command(AdvanceAcademicCalendar::class)->dailyAt('00:30')->withoutOverlapping();

// Draft next year's calendar before this one runs out.
Schedule::command(GenerateUpcomingAcademicCycles::class)->weeklyOn(1, '01:00')->withoutOverlapping();

// Remind the staff who can prepare or close a period. The command remembers
// each deadline, so a scheduler retry cannot send the same reminder twice.
Schedule::command(SendAcademicCalendarReminders::class)->dailyAt('07:15')->withoutOverlapping();

// Take the nightly backup, locked, and remove the ones the rule no longer
// keeps. The uploaded files go with it, because a database without the files
// it names is only half a school.
Schedule::command(CreateBackup::class, ['--with-files'])->dailyAt('01:30')->withoutOverlapping();

// Prove the backups can be restored. A backup nobody has restored is not a
// backup. This runs where a rehearsal connection is set up and does nothing
// but read the backup anywhere else.
Schedule::command(RehearseRestore::class)->weeklyOn(7, '03:00')->withoutOverlapping();

// Say early when the backups stopped arriving, or when nobody has restored
// one for too long.
Schedule::command(CheckBackup::class)->dailyAt('07:00');

// Keep the failed job table and old batches from growing without limit.
Schedule::command('queue:prune-failed --hours=336')->daily();
Schedule::command('queue:prune-batches --hours=336')->daily();
