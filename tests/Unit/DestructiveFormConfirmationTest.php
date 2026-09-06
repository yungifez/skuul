<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * A form that undoes something must say what it undoes.
 *
 * `resources/js/app.js` asks the reader to confirm every form that carries
 * the DELETE method. Without a message of its own that question reads
 * "Delete this item? This action cannot be undone." Several of these forms
 * do not delete anything at all, so the default is both vague and wrong.
 *
 * A form on a screen that already asks the question itself carries
 * `data-confirm="false"`, so the reader is not asked twice.
 */
class DestructiveFormConfirmationTest extends TestCase
{
    public function test_every_destructive_form_asks_a_question_of_its_own(): void
    {
        $offenders = [];

        foreach ($this->views() as $path => $source) {
            foreach ($this->destructiveForms($source) as [$line, $form]) {
                if (preg_match('/\bdata-confirm=|:data-confirm=|x-bind:data-confirm=/', $form) === 1) {
                    continue;
                }

                $offenders[] = $path.':'.$line;
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "A form that carries the DELETE method needs a data-confirm naming what it undoes:\n".implode("\n", $offenders)
        );
    }

    public function test_a_screen_that_asks_its_own_question_can_turn_the_browser_one_off(): void
    {
        $handler = (string) file_get_contents(dirname(__DIR__, 2).'/resources/js/app.js');

        $this->assertStringContainsString(
            'form.dataset.confirm !== "false"',
            $handler,
            'The handler must let a screen with its own dialog skip the browser question.'
        );
    }

    public function test_the_scan_reads_the_forms_it_claims_to_read(): void
    {
        $source = <<<'BLADE'
        <form method="POST" action="/a"><input type="hidden" name="_method" value="DELETE"></form>
        <form method="POST" action="/b">@method('DELETE')</form>
        <form method="POST" action="/c">@csrf</form>
        BLADE;

        $this->assertCount(2, $this->destructiveForms($source));
    }

    /**
     * Every form the confirmation handler stops, with the line it opens on.
     *
     * The handler looks for the DELETE method, however the view spells it.
     *
     * @return list<array{0: int, 1: string}>
     */
    private function destructiveForms(string $source): array
    {
        if (preg_match_all('/<form\b.*?<\/form>/s', $source, $matches, PREG_OFFSET_CAPTURE) === 0) {
            return [];
        }

        $found = [];

        foreach ($matches[0] as [$form, $offset]) {
            $deletes = preg_match('/@method\([\'"]DELETE[\'"]\)/i', $form) === 1
                || preg_match('/name="_method"[^>]*value="DELETE"/i', $form) === 1;

            if (!$deletes) {
                continue;
            }

            $found[] = [substr_count(substr($source, 0, $offset), "\n") + 1, $form];
        }

        return $found;
    }

    /**
     * @return array<string, string>
     */
    private function views(): array
    {
        $root = dirname(__DIR__, 2).'/resources/views';
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
        $views = [];

        foreach ($files as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $views[substr($file->getPathname(), strlen($root) + 1)] = (string) file_get_contents($file->getPathname());
        }

        return $views;
    }
}
