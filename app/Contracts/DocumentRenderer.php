<?php

namespace App\Contracts;

/**
 * Render printable HTML as a PDF. Register implementations in DocumentRendererRegistry.
 */
interface DocumentRenderer
{
    /**
     * Get the name the renderer is chosen by.
     */
    public function key(): string;

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
