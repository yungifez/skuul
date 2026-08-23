<?php

namespace App\Contracts;

/**
 * One way of copying a database out and putting it back.
 *
 * Each database engine has its own tools, so the backup command does not know
 * how a dump is taken. It asks the registry for the dumper of the connection
 * it was given.
 *
 * A new engine is one class and one line in
 * `App\Services\Backup\DatabaseDumperRegistry`.
 */
interface DatabaseDumper
{
    /**
     * Get the connection driver this dumper handles.
     */
    public function driver(): string;

    /**
     * Check whether the tools this dumper needs are installed.
     */
    public function isAvailable(): bool;

    /**
     * Write the whole database to one file.
     *
     * @param  array<string, mixed>  $connection
     */
    public function dumpTo(string $path, array $connection): void;

    /**
     * Load one file back into a database.
     *
     * @param  array<string, mixed>  $connection
     */
    public function restoreFrom(string $path, array $connection): void;
}
