<?php

namespace App\Contracts;

/**
 * Use engine-specific tools to dump and restore a database.
 * Register implementations in DatabaseDumperRegistry.
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
