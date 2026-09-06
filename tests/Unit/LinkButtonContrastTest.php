<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * April UI paints its "link" button variant with --primary, a surface colour
 * that is unreadable as text in both themes. The application repaints it.
 */
class LinkButtonContrastTest extends TestCase
{
    public function test_the_link_button_variant_still_paints_itself_with_the_primary_surface(): void
    {
        // This test exists to catch the day April UI fixes the variant itself,
        // or renames the class. Either way the override needs revisiting.
        foreach (['button', 'button-link'] as $component) {
            $this->assertStringContainsString(
                '\'link\' => "text-primary underline-offset-4 hover:underline"',
                $this->read("vendor/yungifez/april-ui/resources/views/components/{$component}.blade.php"),
                "April UI changed the link variant in {$component}.blade.php."
            );
        }
    }

    public function test_the_application_repaints_link_buttons_with_a_readable_colour(): void
    {
        $css = $this->read('resources/css/app.css');
        $selector = '[data-slot="button"].text-primary';

        $this->assertStringContainsString($selector, $css);

        $position = strpos($css, $selector);
        $declaration = substr($css, $position, strpos($css, '}', $position) - $position);
        $this->assertStringContainsString('color: hsl(var(--primary-foreground));', $declaration);
    }

    public function test_the_override_sits_outside_every_layer(): void
    {
        // A rule inside @layer loses to the Tailwind utility of the same name,
        // whatever its specificity.
        $css = $this->read('resources/css/app.css');
        $before = substr($css, 0, strpos($css, '[data-slot="button"].text-primary'));

        $this->assertSame(
            substr_count($before, '{'),
            substr_count($before, '}'),
            'Every block before the override must be closed, or the rule sits inside one.'
        );
    }

    private function read(string $relativePath): string
    {
        $path = dirname(__DIR__, 2).'/'.$relativePath;
        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }
}
