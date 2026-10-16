<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Destructive text on the dark theme.
 *
 * April UI paints it with --destructive, which measures 4.3:1 on a card and
 * 3.61:1 on an accent surface. WCAG AA asks for 4.5:1. The same token is the
 * background of destructive buttons, so the application adds a text-only
 * token instead of lightening the shared one.
 */
class DestructiveTextContrastTest extends TestCase
{
    public function test_the_dark_theme_carries_a_lighter_red_for_text(): void
    {
        $css = $this->appCss();

        $this->assertMatchesRegularExpression(
            '/\.dark\s*\{\s*--destructive-text:\s*0 72% 68%;/',
            $css,
            'The dark theme must define its own destructive text colour.'
        );
    }

    public function test_the_light_theme_keeps_the_shared_token(): void
    {
        // The light theme already measures 4.51:1, so it needs no new colour.
        $this->assertMatchesRegularExpression(
            '/:root\s*\{\s*--destructive-text:\s*var\(--destructive\);/',
            $this->appCss()
        );
    }

    public function test_destructive_text_reads_the_new_token(): void
    {
        $css = $this->appCss();
        $position = strpos($css, '.text-destructive {');

        $this->assertNotFalse($position, 'The override rule is missing.');

        $declaration = substr($css, $position, strpos($css, '}', $position) - $position);
        $this->assertStringContainsString('color: hsl(var(--destructive-text));', $declaration);
    }

    public function test_the_override_sits_outside_every_layer(): void
    {
        // A rule inside @layer loses to the Tailwind utility of the same name,
        // whatever its specificity.
        $css = $this->appCss();
        $before = substr($css, 0, strpos($css, '.text-destructive {'));

        $this->assertSame(
            substr_count($before, '{'),
            substr_count($before, '}'),
            'Every block before the override must be closed, or the rule sits inside one.'
        );
    }

    public function test_april_still_paints_destructive_text_with_the_shared_token(): void
    {
        // This test fails the day April UI gives destructive text its own
        // readable colour. The override can go when it does.
        $this->assertStringContainsString(
            '--destructive: 0 72% 58%;',
            $this->read('vendor/yungifez/april-ui/resources/css/april.css'),
            'April UI changed its dark destructive colour.'
        );
    }

    private function appCss(): string
    {
        return $this->read('resources/css/app.css');
    }

    private function read(string $relativePath): string
    {
        $path = dirname(__DIR__, 2).'/'.$relativePath;
        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }
}
