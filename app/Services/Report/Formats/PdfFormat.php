<?php

namespace App\Services\Report\Formats;

use App\Contracts\ExportFormat;
use App\Services\Print\PrintService;
use Illuminate\Support\Collection;

/**
 * A report printed as a document, for filing or handing over.
 *
 * The table is laid out in HTML with print styles and given to whichever
 * renderer the installation uses. Because a report is built by a worker, a
 * long document never holds up the person who asked for it.
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

    /**
     * Get the label to show in the interface.
     */
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
