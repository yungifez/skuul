<?php

namespace App\Services\Print;

use Illuminate\Http\Response;

/**
 * Print a page of the application as a document.
 *
 * The page is an ordinary Blade view with print styles. Which renderer turns
 * it into a PDF is decided by `App\Services\Print\DocumentRendererRegistry`,
 * so a school can change renderer without any of these documents changing.
 */
class PrintService
{
    /**
     * Render a document as a browser print view.
     *
     * The browser owns pagination, fonts, previews, and PDF saving. This keeps
     * the printed page aligned with the HTML and CSS that the user can see.
     *
     * @param  array<string, mixed>  $data
     */
    public static function page(string $view, array $data): Response
    {
        return response()->view($view, $data);
    }

    /**
     * Render a view and get the bytes of the document.
     *
     * @param  array<string, mixed>  $data
     * @param  array{paper?: string, orientation?: string, title?: string}  $options
     */
    public static function render(string $view, array $data, array $options = []): string
    {
        $html = view($view, $data)->render();

        return app(DocumentRendererRegistry::class)->current()->render($html, $options);
    }

    /**
     * Render a view and send it to the reader as a download.
     *
     * @param  array<string, mixed>  $data
     * @param  array{paper?: string, orientation?: string, title?: string}  $options
     */
    public static function download(string $view, array $data, string $name, array $options = []): Response
    {
        $file = str_replace('"', '', $name).'.pdf';

        return response(self::render($view, $data, $options), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"$file\"",
        ]);
    }
}
