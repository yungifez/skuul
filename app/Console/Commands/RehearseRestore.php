<?php

namespace App\Console\Commands;

use App\Services\Backup\BackupCatalogue;
use App\Services\Backup\DatabaseDumperRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Restore the newest backup into an isolated database and record verification results.
 * Without a restore connection, inspect the dump without claiming a completed restore.
 */
class RehearseRestore extends Command
{
    /**
     * The tables a restored database must hold something in.
     *
     * @var array<int, string>
     */
    private const REQUIRED = ['migrations', 'users', 'schools'];

    /**
     * The tables a restored database must hold rows in.
     *
     * A young installation has no users yet, but a database restored from a
     * backup always knows which migrations it ran.
     *
     * @var array<int, string>
     */
    private const REQUIRED_ROWS = ['migrations'];

    /**
     * @var string
     */
    protected $signature = 'skuul:rehearse-restore
        {--file= : Rehearse this backup instead of the newest one}
        {--into= : The connection to restore into}
        {--check-only : Look inside the backup without restoring it}';

    /**
     * @var string
     */
    protected $description = 'Restore the newest backup into a separate database and check what came back';

    public function handle(BackupCatalogue $catalogue, DatabaseDumperRegistry $dumpers): int
    {
        $started = microtime(true);
        $backup = $this->option('file') ?? $catalogue->newest();

        if (!is_string($backup) || $backup === '') {
            return $this->reportFailure('There is no backup to rehearse.', []);
        }

        $dump = tempnam(sys_get_temp_dir(), 'skuul-rehearsal');

        if ($dump === false) {
            return $this->reportFailure('A working file for the rehearsal could not be made.', ['backup' => $backup]);
        }

        try {
            $catalogue->pull($backup, $dump);
            $this->info("Unlocked $backup.");

            $missing = $this->missingTables($dump);

            if ($missing !== []) {
                return $this->reportFailure(
                    'The backup does not hold '.implode(', ', $missing).'.',
                    ['backup' => $backup],
                );
            }

            $rows = $this->option('check-only') ? null : $this->restore($dump, $dumpers);
        } catch (Throwable $exception) {
            return $this->reportFailure($exception->getMessage(), ['backup' => $backup]);
        } finally {
            if (is_file($dump)) {
                unlink($dump);
            }
        }

        $seconds = round(microtime(true) - $started, 1);
        $this->record($catalogue, [
            'backup' => $backup,
            'rehearsed_at' => now()->toIso8601String(),
            'seconds' => $seconds,
            'restored' => $rows !== null,
            'rows' => $rows,
        ]);

        $this->info("The rehearsal took $seconds seconds.");

        if ($rows !== null) {
            foreach ($rows as $table => $count) {
                $this->line("$table: $count rows.");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Load the dump into the rehearsal database and count what came back.
     *
     * Without a rehearsal connection nothing is restored, so the result is null
     * and the record does not claim a restore.
     *
     * @return array<string, int>|null
     */
    private function restore(string $dump, DatabaseDumperRegistry $dumpers): ?array
    {
        $connection = $this->option('into') ?? config('monitoring.backup.rehearsal.connection');

        if (!is_string($connection) || $connection === '') {
            $this->warn('No rehearsal connection is set, so the backup was only looked inside.');

            return null;
        }

        $defaultConnection = (string) config('database.default');

        if ($connection === $defaultConnection) {
            throw new RuntimeException('The restore rehearsal cannot use the application database connection.');
        }

        $settings = (array) DB::connection($connection)->getConfig();
        $applicationDatabase = (string) DB::connection($defaultConnection)->getConfig('database');
        $targetDatabase = $settings['database'] ?? null;

        if (!is_string($targetDatabase) || trim($targetDatabase) === '') {
            throw new RuntimeException('The restore rehearsal needs a named, isolated database.');
        }

        if (mb_strtolower($targetDatabase) === mb_strtolower($applicationDatabase)) {
            throw new RuntimeException('The restore rehearsal database must have a different name from the application database.');
        }

        $dumper = $dumpers->forConnection($connection);

        $this->info('Restoring into '.$settings['database'].'.');
        $dumper->restoreFrom($dump, $settings);

        $rows = [];

        foreach (self::REQUIRED as $table) {
            $rows[$table] = DB::connection($connection)->table($table)->count();
        }

        foreach (self::REQUIRED_ROWS as $table) {
            if ($rows[$table] === 0) {
                throw new RuntimeException("The restored database has no $table, so the backup is not usable.");
            }
        }

        return $rows;
    }

    /**
     * Say which of the required tables the dump does not hold.
     *
     * @return array<int, string>
     */
    private function missingTables(string $dump): array
    {
        $contents = (string) file_get_contents($dump);
        $missing = [];

        foreach (self::REQUIRED as $table) {
            if (!str_contains($contents, "CREATE TABLE `$table`")) {
                $missing[] = $table;
            }
        }

        return $missing;
    }

    /**
     * Write down what this rehearsal found.
     *
     * @param  array<string, mixed>  $outcome
     */
    private function record(BackupCatalogue $catalogue, array $outcome): void
    {
        $path = trim((string) config('monitoring.backup.rehearsal.path'), '/');
        $catalogue->disk()->put(
            $path.'/'.now()->format('Y-m-d-His').'.json',
            (string) json_encode($outcome, JSON_PRETTY_PRINT),
        );

        Log::info('A restore was rehearsed', $outcome);
    }

    /**
     * Say plainly that the rehearsal did not work, and write it down.
     *
     * @param  array<string, mixed>  $context
     */
    private function reportFailure(string $message, array $context): int
    {
        Log::error('The restore rehearsal failed', ['message' => $message] + $context);
        $this->error($message);

        return self::FAILURE;
    }
}
