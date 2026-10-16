<?php

namespace App\Services\Report;

use App\Contracts\ExportFormat;
use App\Exceptions\InvalidValueException;
use App\Services\Report\Formats\CsvFormat;
use App\Services\Report\Formats\PdfFormat;
use App\Services\Report\Formats\XlsxFormat;

/**
 * The shapes a report can be handed over in.
 *
 * A new shape is one class and one line here. No report knows which shapes
 * exist, so the list can grow without a single report changing.
 */
class ExportFormatRegistry
{
    /**
     * The formats, in the order the interface offers them.
     *
     * @var array<int, class-string<ExportFormat>>
     */
    private const FORMATS = [
        CsvFormat::class,
        XlsxFormat::class,
        PdfFormat::class,
    ];

    /**
     * Get the format with the given name.
     *
     * @throws InvalidValueException when no format has that name
     */
    public function get(string $key): ExportFormat
    {
        foreach (self::FORMATS as $class) {
            /** @var ExportFormat $format */
            $format = app($class);

            if ($format->key() === $key) {
                return $format;
            }
        }

        throw new InvalidValueException("There is no export format called $key.");
    }

    /**
     * Get every format, by name.
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        $formats = [];

        foreach (self::FORMATS as $class) {
            /** @var ExportFormat $format */
            $format = app($class);
            $formats[$format->key()] = $format->label();
        }

        return $formats;
    }
}
