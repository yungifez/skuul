<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

/**
 * Convert report columns and rows to a file. Register formats in ExportFormatRegistry.
 */
interface ExportFormat
{
    /**
     * Get the name the format is stored and chosen by.
     */
    public function key(): string;

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
