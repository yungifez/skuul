<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Leading directive markers break Livewire's root-tag detection on later requests.
 * Allow @php blocks, which produce no output, before the root element.
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
