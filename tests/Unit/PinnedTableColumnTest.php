<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Every data table pins its last column to the right edge. A column that
 * scrolls under it must not show through.
 */
class PinnedTableColumnTest extends TestCase
{
    public function test_the_pinned_header_cell_is_opaque(): void
    {
        $declaration = $this->declaration('[data-slot="data-table"] th:last-child {');

        $this->assertMatchesRegularExpression('/background-color: color-mix\(in oklab, var\(--color-muted\) 50%, var\(--color-background\)\);/', $declaration);
        $this->assertStringNotContainsString('transparent', $declaration);
    }

    public function test_the_pinned_body_cell_is_opaque(): void
    {
        $this->assertStringContainsString(
            'background-color: var(--color-background);',
            $this->declaration('[data-slot="data-table"] td:last-child {'),
        );
    }

    /**
     * The last rule for a selector, which is the one that sets its own colour.
     */
    private function declaration(string $selector): string
    {
        $css = (string) file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');
        $position = strrpos($css, $selector);

        $this->assertNotFalse($position, "The rule $selector is missing.");

        return substr($css, $position, strpos($css, '}', $position) - $position);
    }
}
