<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Report whether a recent backup exists, and whether one was restored lately.
 *
 * A backup job that stopped working looks exactly like one that never ran, so
 * the age of the newest file is checked on a schedule and reported as a
 * failure when it gets too old. A backup nobody has restored is not a backup
 * either, so the date of the last rehearsal is checked the same way.
 */
class CheckBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:check-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check that a recent backup exists';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $disk = Storage::disk(config('monitoring.backup.disk'));
        $path = (string) config('monitoring.backup.path');
        $maxAgeHours = (int) config('monitoring.backup.max_age_hours');

        $newest = null;

        foreach ($disk->files($path) as $file) {
            $modified = Carbon::createFromTimestamp($disk->lastModified($file));

            if ($newest === null || $modified->greaterThan($newest)) {
                $newest = $modified;
            }
        }

        if ($newest === null) {
            return $this->reportFailure("No backup was found in [$path].", ['path' => $path]);
        }

        $ageHours = $newest->diffInHours(now());

        if ($ageHours > $maxAgeHours) {
            return $this->reportFailure(
                "The newest backup is $ageHours hours old, which is older than $maxAgeHours hours.",
                ['path' => $path, 'newest_backup_at' => $newest->toIso8601String(), 'age_hours' => $ageHours],
            );
        }

        $this->info("The newest backup is $ageHours hours old.");

        return $this->checkRehearsal($disk);
    }

    /**
     * Say whether a restore was rehearsed recently enough.
     */
    private function checkRehearsal(Filesystem $disk): int
    {
        $path = trim((string) config('monitoring.backup.rehearsal.path'), '/');
        $maxAgeDays = (int) config('monitoring.backup.rehearsal.max_age_days');
        $newest = null;

        foreach ($disk->files($path) as $file) {
            $rehearsed = Carbon::createFromTimestamp($disk->lastModified($file));

            if ($newest === null || $rehearsed->greaterThan($newest)) {
                $newest = $rehearsed;
            }
        }

        if ($newest === null) {
            return $this->reportFailure(
                'No restore has ever been rehearsed. Run skuul:rehearse-restore.',
                ['path' => $path],
            );
        }

        $ageDays = (int) $newest->diffInDays(now());

        if ($ageDays > $maxAgeDays) {
            return $this->reportFailure(
                "The last restore was rehearsed $ageDays days ago, which is longer ago than $maxAgeDays days.",
                ['path' => $path, 'last_rehearsal_at' => $newest->toIso8601String(), 'age_days' => $ageDays],
            );
        }

        $this->info("A restore was rehearsed $ageDays days ago.");

        return self::SUCCESS;
    }

    /**
     * Log the problem and end with a failure.
     *
     * @param  array<string, mixed>  $context
     */
    private function reportFailure(string $message, array $context): int
    {
        Log::error('Backup check failed', ['message' => $message] + $context);

        $this->error($message);

        return self::FAILURE;
    }
}
