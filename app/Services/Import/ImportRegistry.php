<?php

namespace App\Services\Import;

use App\Contracts\Importer;
use App\Exceptions\InvalidValueException;
use App\Imports\StaffImporter;
use App\Imports\StudentImporter;

/**
 * The imports this application can run.
 *
 * A new import is one class and one line here, so checking, previewing, and
 * writing do not change when the list grows.
 */
class ImportRegistry
{
    /**
     * The importers, by the name people choose them with.
     *
     * @var array<int, class-string<Importer>>
     */
    private const IMPORTERS = [
        StudentImporter::class,
        StaffImporter::class,
    ];

    /**
     * Get the importer with the given name.
     *
     * @throws InvalidValueException when no importer has that name
     */
    public function get(string $key): Importer
    {
        foreach (self::IMPORTERS as $class) {
            /** @var Importer $importer */
            $importer = app($class);

            if ($importer->key() === $key) {
                return $importer;
            }
        }

        throw new InvalidValueException("There is no import called $key.");
    }

    /**
     * Get every importer, by name.
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        $importers = [];

        foreach (self::IMPORTERS as $class) {
            /** @var Importer $importer */
            $importer = app($class);
            $importers[$importer->key()] = $importer->title();
        }

        return $importers;
    }

    /**
     * Get what every import accepts, for the screen that explains them.
     *
     * @return array<int, array{key: string, title: string, required: array<int, string>, optional: array<int, string>}>
     */
    public function describe(): array
    {
        $imports = [];

        foreach (self::IMPORTERS as $class) {
            /** @var Importer $importer */
            $importer = app($class);

            $imports[] = [
                'key' => $importer->key(),
                'title' => $importer->title(),
                'required' => $importer->requiredColumns(),
                'optional' => $importer->optionalColumns(),
            ];
        }

        return $imports;
    }
}
