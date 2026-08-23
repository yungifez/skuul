<?php

namespace App\Contracts;

/**
 * One way of turning a printable page into a document somebody can keep.
 *
 * The application writes HTML and print styles. Which program turns that into
 * a PDF is a deployment choice, not an application one, so a school can move
 * from the built-in renderer to a browser service without a report, an
 * invoice, or a timetable changing at all.
 *
 * A new renderer is one class and one line in
 * `App\Services\Print\DocumentRendererRegistry`.
 */
interface DocumentRenderer
{
    /**
     * Get the name the renderer is chosen by.
     */
    public function key(): string;

    /**
     * Get the label to show in the interface.
     */
    public function label(): string;

    /**
     * Check whether this renderer can be used on this installation.
     *
     * A renderer that needs a service nobody has set up is never chosen.
     */
    public function isAvailable(): bool;

    /**
     * Turn one HTML page into the bytes of a PDF.
     *
     * @param  array{paper?: string, orientation?: string, title?: string}  $options
     */
    public function render(string $html, array $options = []): string;
}
