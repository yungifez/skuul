<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * A Livewire view must open with the component's root element.
 *
 * Livewire finds the root by reading the first `<` in the rendered HTML and
 * taking the tag name after it. A directive placed before that element writes
 * `<!--[if BLOCK]><![endif]-->` first, so Livewire reads an empty tag name and
 * stores it against the component. The next request from a parent component
 * then throws "Invalid Livewire child tag name" and the screen returns a 500.
 *
 * `@php` is safe because it writes nothing. Every other directive is not, so
 * put the condition inside the root element rather than around it.
 */
class LivewireRootElementTest extends TestCase
{
    public function test_every_livewire_view_opens_with_its_root_element(): void
    {
        $offenders = [];

        foreach (glob(dirname(__DIR__, 2).'/resources/views/livewire/*.blade.php') as $path) {
            $source = (string) file_get_contents($path);

            // A @php block writes nothing, so step over it and read on.
            $source = (string) preg_replace('/@php\b.*?@endphp/s', '', $source);
            $source = (string) preg_replace('/\{\{--.*?--\}\}/s', '', $source);

            $firstLine = '';

            foreach (explode("\n", $source) as $line) {
                if (trim($line) !== '') {
                    $firstLine = trim($line);
                    break;
                }
            }

            if (!str_starts_with($firstLine, '<')) {
                $offenders[] = basename($path).' opens with '.$firstLine;
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "A Livewire view must open with its root element, not a directive:\n".implode("\n", $offenders)
        );
    }
}
