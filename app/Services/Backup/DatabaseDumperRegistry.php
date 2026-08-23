<?php

namespace App\Services\Backup;

use App\Contracts\DatabaseDumper;
use App\Exceptions\InvalidValueException;
use App\Services\Backup\Dumpers\MysqlDumper;

/**
 * The database engines this application can back up.
 *
 * A new engine is one class and one line here. The backup and rehearsal
 * commands never name an engine themselves.
 */
class DatabaseDumperRegistry
{
    /**
     * The dumpers, by the driver they handle.
     *
     * @var array<int, class-string<DatabaseDumper>>
     */
    private const DUMPERS = [
        MysqlDumper::class,
    ];

    /**
     * Get the dumper of one connection.
     *
     * @throws InvalidValueException when the engine has no dumper, or its tools are missing
     */
    public function forConnection(string $connection): DatabaseDumper
    {
        $driver = (string) config("database.connections.$connection.driver");
        $dumper = $this->forDriver($driver);

        if (!$dumper->isAvailable()) {
            throw new InvalidValueException("The $driver tools are not installed on this machine, so no backup can be taken.");
        }

        return $dumper;
    }

    /**
     * Get the dumper of one engine, installed or not.
     *
     * @throws InvalidValueException when no dumper handles the engine
     */
    public function forDriver(string $driver): DatabaseDumper
    {
        foreach (self::DUMPERS as $class) {
            /** @var DatabaseDumper $dumper */
            $dumper = app($class);

            if ($dumper->driver() === $driver) {
                return $dumper;
            }
        }

        throw new InvalidValueException("This application cannot back up a $driver database yet.");
    }
}
