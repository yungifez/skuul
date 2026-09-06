<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * April UI binds the focus ring to --primary, a surface colour that is almost
 * invisible against the page. The application repaints it.
 */
class FocusRingContrastTest extends TestCase
{
    public function test_april_still_paints_the_ring_with_the_primary_surface(): void
    {
        // This test exists to catch the day April UI gives the ring its own
        // readable colour. The override would then need revisiting.
        $april = $this->read('vendor/yungifez/april-ui/resources/css/april.css');

        $this->assertStringContainsString('--primary: 28 38% 74%;', $april);
        $this->assertStringContainsString('--ring: 28 38% 74%;', $april);
        $this->assertStringContainsString('--primary: 28 50% 22%;', $april);
        $this->assertStringContainsString('--ring: 28 50% 22%;', $april);
    }

    public function test_both_themes_repaint_the_ring_with_the_readable_colour(): void
    {
        $css = $this->read('resources/css/app.css');

        // The ring must be readable on the light theme and on the dark one,
        // so both blocks carry it.
        $this->assertSame(2, substr_count($css, '--ring: var(--primary-foreground);'));
        $this->assertMatchesRegularExpression('/:root\s*\{\s*--ring: var\(--primary-foreground\);/', $css);
        $this->assertMatchesRegularExpression('/\.dark\s*\{\s*--ring: var\(--primary-foreground\);/', $css);
    }

    public function test_the_override_sits_outside_every_layer(): void
    {
        // A rule inside @layer loses to the Tailwind utility of the same name,
        // whatever its specificity.
        $css = $this->read('resources/css/app.css');
        $before = substr($css, 0, (int) strpos($css, '--ring: var(--primary-foreground);'));

        $this->assertSame(
            substr_count($before, '{') - 1,
            substr_count($before, '}'),
            'Only the :root block may be open before the override.'
        );
    }

    private function read(string $relativePath): string
    {
        $path = dirname(__DIR__, 2).'/'.$relativePath;
        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }
}
