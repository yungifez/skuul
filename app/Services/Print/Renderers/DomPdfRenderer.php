<?php

namespace App\Services\Print\Renderers;

use App\Contracts\DocumentRenderer;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Render PDFs locally with Dompdf, without an external browser service.
 */
class DomPdfRenderer implements DocumentRenderer
{
    /**
     * Get the name the renderer is chosen by.
     */
    public function key(): string
    {
        return 'dompdf';
    }

    public function label(): string
    {
        return 'Built-in renderer';
    }

    /**
     * This renderer ships with the application, so it is always available.
     */
    public function isAvailable(): bool
    {
        return true;
    }

    /**
     * Turn one HTML page into the bytes of a PDF.
     *
     * @param  array{paper?: string, orientation?: string, title?: string}  $options
     */
    public function render(string $html, array $options = []): string
    {
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($options['paper'] ?? 'a4', $options['orientation'] ?? 'portrait');
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ])
        );

        return (string) $pdf->output();
    }
}
