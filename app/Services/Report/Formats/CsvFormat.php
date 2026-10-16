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
        fputcsv($handle, array_map($this->cell(...), $columns));

        foreach ($rows as $row) {
            fputcsv($handle, array_map($this->cell(...), $row));
        }

        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * Prefix formula-like text with an apostrophe to prevent spreadsheet execution.
     * Preserve numeric values, including negative balances.
     */
    private function cell(mixed $value): mixed
    {
        if (!is_string($value) || $value === '' || is_numeric($value)) {
            return $value;
        }

        return in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true) ? "'".$value : $value;
    }
}
