<?php

namespace App\Services\Backup\Dumpers;

use App\Contracts\DatabaseDumper;
use App\Exceptions\InvalidValueException;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * A dump taken with the tools MySQL ships with.
 *
 * The password is written to a short-lived options file rather than the
 * command line, because anyone on the machine can read a command line.
 */
class MysqlDumper implements DatabaseDumper
{
    /**
     * How long a dump or a restore may run, in seconds.
     */
    private const TIMEOUT = 3600;

    /**
     * Get the connection driver this dumper handles.
     */
    public function driver(): string
    {
        return 'mysql';
    }

    /**
     * Check whether the tools this dumper needs are installed.
     */
    public function isAvailable(): bool
    {
        return $this->found('mysqldump') && $this->found('mysql');
    }

    /**
     * Write the whole database to one file.
     *
     * The dump is taken in one transaction, so it is the database as it stood
     * at one moment rather than a mixture of several.
     *
     * @param  array<string, mixed>  $connection
     */
    public function dumpTo(string $path, array $connection): void
    {
        $this->run('mysqldump', $connection, [
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--no-tablespaces',
            '--result-file='.$path,
            (string) $connection['database'],
        ]);
    }

    /**
     * Load one file back into a database.
     *
     * @param  array<string, mixed>  $connection
     */
    public function restoreFrom(string $path, array $connection): void
    {
        $file = fopen($path, 'rb');

        if ($file === false) {
            throw new RuntimeException("The file [$path] could not be opened.");
        }

        $this->run('mysql', $connection, [(string) $connection['database']], $file);
        fclose($file);
    }

    /**
     * Run one of the MySQL tools against the connection.
     *
     * @param  array<string, mixed>  $connection
     * @param  array<int, string>  $arguments
     * @param  resource|null  $input
     */
    private function run(string $tool, array $connection, array $arguments, $input = null): void
    {
        $options = $this->optionsFile($connection);

        try {
            $process = new Process([$tool, '--defaults-extra-file='.$options, ...$arguments]);
            $process->setTimeout(self::TIMEOUT);

            if ($input !== null) {
                $process->setInput($input);
            }

            $process->run();

            if (!$process->isSuccessful()) {
                throw new InvalidValueException("$tool failed: ".trim($process->getErrorOutput()));
            }
        } finally {
            unlink($options);
        }
    }

    /**
     * Write the credentials somewhere only this process can read them.
     *
     * @param  array<string, mixed>  $connection
     */
    private function optionsFile(array $connection): string
    {
        $file = tempnam(sys_get_temp_dir(), 'skuul-db');

        if ($file === false) {
            throw new RuntimeException('The database options file could not be written.');
        }

        chmod($file, 0600);
        file_put_contents($file, implode("\n", [
            '[client]',
            'host='.($connection['host'] ?? '127.0.0.1'),
            'port='.($connection['port'] ?? 3306),
            'user='.($connection['username'] ?? ''),
            'password="'.str_replace('"', '\\"', (string) ($connection['password'] ?? '')).'"',
            '',
        ]));

        return $file;
    }

    /**
     * Check whether one tool is installed.
     */
    private function found(string $tool): bool
    {
        $process = new Process(['which', $tool]);
        $process->run();

        return $process->isSuccessful();
    }
}
