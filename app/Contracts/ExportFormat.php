<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

/**
 * One shape a report can be handed over in.
 *
 * A report says what its columns and rows are. How those become a file
 * somebody can open is a separate question, so a school that wants a
 * spreadsheet instead of a comma-separated file changes nothing about the
 * report itself.
 *
 * A new shape is one class and one line in
 * `App\Services\Report\ExportFormatRegistry`.
 */
interface ExportFormat
{
    /**
     * Get the name the format is stored and chosen by.
     */
    public function key(): string;

    /**
     * Get the label to show in the interface.
     */
    public function label(): string;

    /**
     * Get the file extension, without the dot.
     */
    public function extension(): string;

    /**
     * Get the content type to send the file with.
     */
    public function mimeType(): string;

    /**
     * Turn the columns and rows into the bytes of one file.
     *
     * @param  array<int, string>  $columns
     * @param  Collection<int, array<int, mixed>>  $rows
     */
    public function render(string $title, array $columns, Collection $rows): string;
}
