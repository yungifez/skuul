<?php

namespace App\Console\Commands;

use App\Services\Backup\BackupCatalogue;
use App\Services\Backup\BackupCipher;
use App\Services\Backup\BackupWriter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Take a backup and put it somewhere else.
 *
 * A backup kept beside the database is not a backup: whatever destroys one
 * destroys the other. The backup disk is meant to be another account or
 * another region, and the file leaves this machine locked.
 */
class CreateBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:backup {--with-files : Back up the uploaded files as well} {--keep-old : Leave old backups alone}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Back up the database, and the uploaded files when asked';

    /**
     * Execute the console command.
     */
    public function handle(BackupWriter $writer, BackupCatalogue $catalogue, BackupCipher $cipher): int
    {
        $started = microtime(true);

        try {
            $written = $writer->write((bool) $this->option('with-files'));
        } catch (Throwable $exception) {
            Log::error('The backup failed', ['message' => $exception->getMessage()]);
            $this->error('The backup failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $seconds = round(microtime(true) - $started, 1);

        foreach ($written as $path) {
            $this->info("Wrote $path.");
        }

        if (!$cipher->isConfigured()) {
            $this->warn('This backup is not locked. Set BACKUP_KEY so the file is useless to anyone who takes it.');
        }

        if (!$this->option('keep-old')) {
            foreach ($catalogue->prune() as $removed) {
                $this->line("Removed $removed.");
            }
        }

        Log::info('A backup was written', ['files' => $written, 'seconds' => $seconds]);
        $this->info("The backup took $seconds seconds.");

        return self::SUCCESS;
    }
}
