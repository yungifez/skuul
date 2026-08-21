<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Report whether a recent backup exists.
 *
 * A backup job that stopped working looks exactly like one that never ran, so
 * the age of the newest file is checked on a schedule and reported as a
 * failure when it gets too old.
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
