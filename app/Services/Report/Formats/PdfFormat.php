<?php

namespace App\Services\Report\Formats;

use App\Contracts\ExportFormat;
use App\Services\Print\PrintService;
use Illuminate\Support\Collection;

/**
 * Render reports through the configured PDF renderer in the report worker.
 */
class PdfFormat implements ExportFormat
{
    /**
     * Get the name the format is stored and chosen by.
     */
    public function key(): string
    {
        return 'pdf';
    }

    public function label(): string
    {
        return 'Document (PDF)';
    }

    /**
     * Get the file extension, without the dot.
     */
    public function extension(): string
    {
        return 'pdf';
    }

    /**
     * Get the content type to send the file with.
     */
    public function mimeType(): string
    {
        return 'application/pdf';
    }

    /**
     * Turn the columns and rows into the bytes of one document.
     *
     * @param  array<int, string>  $columns
     * @param  Collection<int, array<int, mixed>>  $rows
     */
    public function render(string $title, array $columns, Collection $rows): string
    {
        return PrintService::render('pages.report.export', [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
        ], [
            'title' => $title,
            // A report is wider than it is tall, so it reads better sideways.
            'orientation' => count($columns) > 5 ? 'landscape' : 'portrait',
        ]);
    }
}
