<?php

namespace App\Services\Print\Renderers;

use App\Contracts\DocumentRenderer;
use App\Exceptions\InvalidValueException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Printing done by a real browser, running as a separate service.
 *
 * A browser lays a page out the way the staff who wrote the print styles saw
 * it, and it is the only renderer that keeps a document and the screen in
 * step. The service is spoken to over HTTP, so it can be scaled, replaced, or
 * turned off without the application changing.
 *
 * This class expects a Gotenberg-style service: an HTML file posted as
 * `index.html`, PDF bytes returned. Another service is another class beside
 * this one.
 */
class BrowserRenderer implements DocumentRenderer
{
    /**
     * Get the name the renderer is chosen by.
     */
    public function key(): string
    {
        return 'browser';
    }

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return 'Browser renderer';
    }

    /**
     * Use this renderer only once somebody has set its address.
     */
    public function isAvailable(): bool
    {
        return $this->address() !== '';
    }

    /**
     * Send the page to the browser service and keep what comes back.
     *
     * @param  array{paper?: string, orientation?: string, title?: string}  $options
     *
     * @throws InvalidValueException when the service cannot print the page
     */
    public function render(string $html, array $options = []): string
    {
        $address = $this->address();

        if ($address === '') {
            throw new InvalidValueException('No browser renderer is set up on this installation.');
        }

        $response = $this->request()
            ->attach('files', $html, 'index.html')
            ->post($address, $this->paperFor($options));

        if ($response->failed()) {
            throw new InvalidValueException('The renderer could not print this document. Try again in a moment.');
        }

        return $response->body();
    }

    /**
     * Build the call, with the timeout and any key the service asks for.
     */
    private function request(): PendingRequest
    {
        $request = Http::timeout($this->setting('timeout', 120));
        $token = $this->setting('token', '');

        return is_string($token) && $token !== '' ? $request->withToken($token) : $request;
    }

    /**
     * Turn the paper choice into the fields the service reads.
     *
     * Sizes are given in inches, which is what a browser measures paper in.
     *
     * @param  array{paper?: string, orientation?: string, title?: string}  $options
     * @return array<string, string>
     */
    private function paperFor(array $options): array
    {
        $sizes = [
            'a4' => ['8.27', '11.7'],
            'a5' => ['5.83', '8.27'],
            'letter' => ['8.5', '11'],
            'legal' => ['8.5', '14'],
        ];

        [$width, $height] = $sizes[strtolower($options['paper'] ?? 'a4')] ?? $sizes['a4'];

        if (($options['orientation'] ?? 'portrait') === 'landscape') {
            [$width, $height] = [$height, $width];
        }

        return [
            'paperWidth' => $width,
            'paperHeight' => $height,
            'printBackground' => 'true',
        ];
    }

    /**
     * Get the address the service prints on.
     */
    private function address(): string
    {
        $url = $this->setting('url', '');

        return is_string($url) ? trim($url) : '';
    }

    /**
     * Read one setting of this renderer.
     */
    private function setting(string $name, string|int $fallback): string|int
    {
        $value = config("services.browser_renderer.$name", $fallback);

        return is_string($value) || is_int($value) ? $value : $fallback;
    }
}
