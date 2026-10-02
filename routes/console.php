<?php

use App\Console\Commands\AdvanceAcademicCalendar;
use App\Console\Commands\CheckBackup;
use App\Console\Commands\CreateBackup;
use App\Console\Commands\EndLeaversAccess;
use App\Console\Commands\GenerateUpcomingAcademicCycles;
use App\Console\Commands\ProcessLibraryHolds;
use App\Console\Commands\ProcessNotices;
use App\Console\Commands\PruneExpiredInvitations;
use App\Console\Commands\RehearseRestore;
use App\Console\Commands\SendAcademicCalendarReminders;
use App\Console\Commands\SendSyllabusBehindReminders;
use App\Http\Controllers\HealthController;
use App\Jobs\RecordQueueHeartbeat;
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

// Every job below runs on one server only, so a second app server never
// sends the same reminder or takes the same backup twice. The heartbeat is
// the exception: each server says for itself that its scheduler is alive.

// Say the scheduler is alive, so the health endpoint can see it.
Schedule::call(function (): void {
    Cache::forever(HealthController::SCHEDULER_KEY, now());
})->everyMinute()->name('scheduler-heartbeat');

// Give the queue workers a job to prove they are running.
Schedule::job(new RecordQueueHeartbeat)->everyMinute()->name('queue-heartbeat')->onOneServer();

// Close invitation links nobody used.
Schedule::command(PruneExpiredInvitations::class)->hourly()->withoutOverlapping()->onOneServer();

// End library holds nobody came for, so the copy reaches the next person in
// the queue instead of waiting behind the desk.
Schedule::command(ProcessLibraryHolds::class)->dailyAt('06:00')->withoutOverlapping()->onOneServer();

// Put scheduled notices on the board and take finished ones down.
Schedule::command(ProcessNotices::class)->everyFifteenMinutes()->withoutOverlapping()->onOneServer();

// Staff whose last day has passed no longer sign in to that campus.
Schedule::command(EndLeaversAccess::class)->dailyAt('00:15')->withoutOverlapping()->onOneServer();

// Open the periods whose first day has arrived. This never closes one:
// closing freezes records, so a person confirms it.
Schedule::command(AdvanceAcademicCalendar::class)->dailyAt('00:30')->withoutOverlapping()->onOneServer();

// Draft next year's calendar before this one runs out.
Schedule::command(GenerateUpcomingAcademicCycles::class)->weeklyOn(1, '01:00')->withoutOverlapping()->onOneServer();

// Remind the staff who can prepare or close a period. The command remembers
// each deadline, so a scheduler retry cannot send the same reminder twice.
Schedule::command(SendAcademicCalendarReminders::class)->dailyAt('07:15')->withoutOverlapping()->onOneServer();

// Tell teachers on Monday which classes fell behind their syllabus last week.
Schedule::command(SendSyllabusBehindReminders::class)->weeklyOn(1, '07:30')->withoutOverlapping()->onOneServer();

// Take the nightly backup, locked, and remove the ones the rule no longer
// keeps. The uploaded files go with it, because a database without the files
// it names is only half a school.
Schedule::command(CreateBackup::class, ['--with-files'])->dailyAt('01:30')->withoutOverlapping()->onOneServer();

// Prove the backups can be restored. A backup nobody has restored is not a
// backup. This runs where a rehearsal connection is set up and does nothing
// but read the backup anywhere else.
Schedule::command(RehearseRestore::class)->weeklyOn(7, '03:00')->withoutOverlapping()->onOneServer();

// Say early when the backups stopped arriving, or when nobody has restored
// one for too long.
Schedule::command(CheckBackup::class)->dailyAt('07:00')->onOneServer();

// Keep the failed job table and old batches from growing without limit.
Schedule::command('queue:prune-failed --hours=336')->daily()->onOneServer();
Schedule::command('queue:prune-batches --hours=336')->daily()->onOneServer();
