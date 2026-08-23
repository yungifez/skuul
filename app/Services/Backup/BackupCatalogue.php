<?php

namespace App\Services\Backup;

use App\Exceptions\InvalidValueException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * What is on the backup disk, and what should stay there.
 *
 * Backups that are never removed cost money until somebody notices; backups
 * removed too eagerly are gone when they are wanted. The rule is written here
 * once, so the command and the check agree about it.
 */
class BackupCatalogue
{
    public function __construct(private BackupCipher $cipher) {}

    /**
     * Get every database backup, newest first.
     *
     * @return array<int, string>
     */
    public function databaseBackups(): array
    {
        $backups = [];

        foreach ($this->disk()->files($this->path()) as $file) {
            if (str_contains(basename($file), '.sql.gz')) {
                $backups[] = $file;
            }
        }

        usort($backups, fn (string $first, string $second): int => strcmp($second, $first));

        return $backups;
    }

    /**
     * Get the newest database backup, if there is one.
     */
    public function newest(): ?string
    {
        return $this->databaseBackups()[0] ?? null;
    }

    /**
     * Bring one backup down to this machine as a plain dump.
     *
     * The file is unlocked and unsqueezed on the way, so what comes back is
     * something the database tools can read.
     */
    public function pull(string $path, string $to): void
    {
        $working = $this->workingFile();

        try {
            $stream = $this->disk()->readStream($path);

            if ($stream === null) {
                throw new InvalidValueException("The backup [$path] could not be read.");
            }

            $file = fopen($working, 'wb');

            if ($file === false) {
                throw new RuntimeException('A working file for the restore could not be made.');
            }

            stream_copy_to_stream($stream, $file);
            fclose($file);
            fclose($stream);

            if (str_ends_with($path, '.enc')) {
                $unlocked = $this->workingFile();
                $this->cipher->decryptFile($working, $unlocked);
                unlink($working);
                $working = $unlocked;
            }

            $this->unsqueeze($working, $to);
        } finally {
            if (is_file($working)) {
                unlink($working);
            }
        }
    }

    /**
     * Work out which backups the rule says to remove.
     *
     * Everything from the last `keep_days` days stays. Older than that, the
     * first backup of each month stays for `keep_months` months, so a mistake
     * nobody noticed for a season can still be undone.
     *
     * @param  array<int, string>  $files
     * @return array<int, string>
     */
    public function expired(array $files): array
    {
        $keepDays = (int) config('monitoring.backup.keep_days');
        $keepMonths = (int) config('monitoring.backup.keep_months');
        $daily = now()->subDays($keepDays)->startOfDay();
        $monthly = now()->subMonths($keepMonths)->startOfMonth();

        $keptMonths = [];
        $expired = [];

        // Oldest first, so the first backup of a month is the one that stays.
        foreach (array_reverse($files) as $file) {
            $taken = $this->takenOn($file);

            if ($taken === null || $taken->greaterThanOrEqualTo($daily)) {
                continue;
            }

            $month = $taken->format('Y-m');

            if ($taken->greaterThanOrEqualTo($monthly) && !isset($keptMonths[$month])) {
                $keptMonths[$month] = true;

                continue;
            }

            $expired[] = $file;
        }

        return $expired;
    }

    /**
     * Remove backups the rule no longer keeps.
     *
     * @return array<int, string> what was removed
     */
    public function prune(): array
    {
        $removed = [];

        foreach ($this->expired($this->disk()->files($this->path())) as $file) {
            $this->disk()->delete($file);
            $removed[] = $file;
        }

        return $removed;
    }

    /**
     * Read the day a backup was taken out of its name.
     */
    public function takenOn(string $file): ?Carbon
    {
        if (preg_match('/(\d{4}-\d{2}-\d{2})-(\d{2})(\d{2})(\d{2})/', basename($file), $found) !== 1) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d His', "$found[1] $found[2]$found[3]$found[4]") ?: null;
    }

    /**
     * Get the disk the backups are kept on.
     */
    public function disk(): Filesystem
    {
        return Storage::disk((string) config('monitoring.backup.disk'));
    }

    /**
     * Get the folder the backups are kept in.
     */
    public function path(): string
    {
        return trim((string) config('monitoring.backup.path'), '/');
    }

    /**
     * Unsqueeze one file into another.
     */
    private function unsqueeze(string $from, string $to): void
    {
        $in = gzopen($from, 'rb');
        $out = fopen($to, 'wb');

        if ($in === false || $out === false) {
            throw new RuntimeException('The backup could not be opened.');
        }

        while (!gzeof($in)) {
            $piece = gzread($in, 1048576);

            if ($piece === false || $piece === '') {
                continue;
            }

            fwrite($out, $piece);
        }

        gzclose($in);
        fclose($out);
    }

    /**
     * Make a working file this machine can write to.
     */
    private function workingFile(): string
    {
        $file = tempnam(sys_get_temp_dir(), 'skuul-restore');

        if ($file === false) {
            throw new RuntimeException('A working file for the restore could not be made.');
        }

        return $file;
    }
}
