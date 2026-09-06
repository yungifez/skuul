<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * A checkbox or radio with no class of its own is painted by the browser, in
 * the operating system accent colour. The application repaints those.
 */
class NativeChoiceControlPaintTest extends TestCase
{
    public function test_the_application_repaints_the_controls_the_browser_draws(): void
    {
        $css = $this->css();
        $selector = 'input[type="checkbox"],'."\n".'input[type="radio"] {';

        $this->assertStringContainsString($selector, $css, 'The repaint rule is missing.');

        $position = strpos($css, $selector);
        $declaration = substr($css, $position, strpos($css, '}', $position) - $position);

        $this->assertStringContainsString('accent-color: hsl(var(--primary-foreground));', $declaration);
        $this->assertStringContainsString('width: 1rem;', $declaration);
        $this->assertStringContainsString('height: 1rem;', $declaration);
    }

    public function test_the_repaint_sits_outside_every_layer(): void
    {
        // A rule inside @layer loses to the Tailwind utility of the same name,
        // whatever its specificity.
        $css = $this->css();
        $before = substr($css, 0, strpos($css, 'input[type="checkbox"],'));

        $this->assertSame(
            substr_count($before, '{'),
            substr_count($before, '}'),
            'Every block before the repaint must be closed, or the rule sits inside one.'
        );
    }

    public function test_every_sized_choice_control_still_measures_one_rem(): void
    {
        // The repaint sets 1rem on every native checkbox and radio. A view that
        // wants a different size would silently lose it.
        $offenders = [];

        foreach ($this->choiceControlTags() as [$file, $line, $tag]) {
            if (!preg_match('/class="([^"]*)"/', $tag, $class)) {
                continue;
            }

            foreach (preg_split('/\s+/', $class[1]) as $utility) {
                if (preg_match('/^(?:size|[hw])-(.+)$/', $utility, $size) && $size[1] !== '4') {
                    $offenders[] = "{$file}:{$line} \"{$utility}\"";
                }
            }
        }

        $this->assertSame([], $offenders, "These choice controls ask for a size the repaint overrides:\n".implode("\n", $offenders));
    }

    /**
     * Every native checkbox and radio tag in the view folder.
     *
     * @return list<array{0: string, 1: int, 2: string}>
     */
    private function choiceControlTags(): array
    {
        $root = dirname(__DIR__, 2).'/resources/views';
        $tags = [];

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

        foreach ($files as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $source = (string) file_get_contents($file->getPathname());
            $relative = str_replace($root.'/', '', $file->getPathname());

            if (!preg_match_all('/<input\b[^>]*>/s', $source, $matches, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            foreach ($matches[0] as [$tag, $offset]) {
                if (!str_contains($tag, 'type="checkbox"') && !str_contains($tag, 'type="radio"')) {
                    continue;
                }

                $tags[] = [$relative, substr_count(substr($source, 0, $offset), "\n") + 1, $tag];
            }
        }

        return $tags;
    }

    private function css(): string
    {
        $path = dirname(__DIR__, 2).'/resources/css/app.css';
        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }
}
