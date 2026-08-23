<?php

namespace App\Services\Report\Formats;

use App\Contracts\ExportFormat;
use Illuminate\Support\Collection;

/**
 * A comma-separated file, which every spreadsheet program opens.
 */
class CsvFormat implements ExportFormat
{
    /**
     * Get the name the format is stored and chosen by.
     */
    public function key(): string
    {
        return 'csv';
    }

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return 'Comma-separated file (CSV)';
    }

    /**
     * Get the file extension, without the dot.
     */
    public function extension(): string
    {
        return 'csv';
    }

    /**
     * Get the content type to send the file with.
     */
    public function mimeType(): string
    {
        return 'text/csv';
    }

    /**
     * Turn the columns and rows into the bytes of one file.
     *
     * @param  array<int, string>  $columns
     * @param  Collection<int, array<int, mixed>>  $rows
     */
    public function render(string $title, array $columns, Collection $rows): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $columns);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
