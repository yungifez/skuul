<?php

namespace App\Services\Print;

use App\Contracts\DocumentRenderer;
use App\Exceptions\InvalidValueException;
use App\Services\Print\Renderers\BrowserRenderer;
use App\Services\Print\Renderers\DomPdfRenderer;

/**
 * The renderers this application can print with.
 *
 * The installation names the one it wants. An installation that names nothing,
 * or names a renderer that is not set up, still prints: the built-in renderer
 * is the last one on the list and is always available.
 */
class DocumentRendererRegistry
{
    /**
     * The renderers, best first.
     *
     * @var array<int, class-string<DocumentRenderer>>
     */
    private const RENDERERS = [
        BrowserRenderer::class,
        DomPdfRenderer::class,
    ];

    /**
     * Get the renderer this installation prints with.
     */
    public function current(): DocumentRenderer
    {
        $chosen = config('services.browser_renderer.driver');

        if (is_string($chosen) && $chosen !== '') {
            $renderer = $this->get($chosen);

            if ($renderer->isAvailable()) {
                return $renderer;
            }
        }

        foreach (self::RENDERERS as $class) {
            /** @var DocumentRenderer $renderer */
            $renderer = app($class);

            if ($renderer->isAvailable()) {
                return $renderer;
            }
        }

        return app(DomPdfRenderer::class);
    }

    /**
     * Get the renderer with the given name, set up or not.
     *
     * @throws InvalidValueException when no renderer has that name
     */
    public function get(string $key): DocumentRenderer
    {
        foreach (self::RENDERERS as $class) {
            /** @var DocumentRenderer $renderer */
            $renderer = app($class);

            if ($renderer->key() === $key) {
                return $renderer;
            }
        }

        throw new InvalidValueException("There is no renderer called $key.");
    }

    /**
     * Get every renderer that is set up, by name.
     *
     * @return array<string, string>
     */
    public function available(): array
    {
        $renderers = [];

        foreach (self::RENDERERS as $class) {
            /** @var DocumentRenderer $renderer */
            $renderer = app($class);

            if ($renderer->isAvailable()) {
                $renderers[$renderer->key()] = $renderer->label();
            }
        }

        return $renderers;
    }
}
