<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * A row action link must name the row it acts on.
 *
 * A screen reader can list every link on a page. Twenty links all called
 * "Open" tell the reader nothing about which record each one opens.
 */
class RowActionLinkNameTest extends TestCase
{
    /**
     * Text that names an action but never a record.
     *
     * @var list<string>
     */
    private const GENERIC = [
        'add class', 'add section', 'build', 'download', 'edit', 'edit grade',
        'edit roster', 'open', 'open gradebook', 'view', 'view level', 'view rows',
    ];

    public function test_a_repeated_row_action_carries_the_name_of_its_row(): void
    {
        $offenders = [];

        foreach ($this->views() as $path => $source) {
            foreach ($this->linksInsideLoops($source) as [$line, $tag]) {
                if (str_contains($tag, 'aria-label')) {
                    continue;
                }

                if (!in_array($this->visibleText($tag), self::GENERIC, true)) {
                    continue;
                }

                $offenders[] = $path.':'.$line;
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "A row action with a generic name needs an aria-label naming its row:\n".implode("\n", $offenders)
        );
    }

    /**
     * Every button-link that sits inside a loop, with the line it opens on.
     *
     * @return list<array{0: int, 1: string}>
     */
    private function linksInsideLoops(string $source): array
    {
        $lines = explode("\n", $source);
        $depth = 0;
        $found = [];

        foreach ($lines as $index => $line) {
            if (preg_match('/@(foreach|forelse|for|while)\b/', $line)) {
                $depth++;
            }

            if ($depth > 0 && str_contains($line, '<april:button-link')) {
                // The tag may run over several lines, so read to its close.
                $tag = implode("\n", array_slice($lines, $index, 6));
                $end = strpos($tag, '</april:button-link>');
                $found[] = [$index + 1, $end === false ? $tag : substr($tag, 0, $end)];
            }

            if (preg_match('/@(endforeach|endforelse|endfor|endwhile)\b/', $line)) {
                $depth = max(0, $depth - 1);
            }
        }

        return $found;
    }

    /**
     * The words a reader hears, with markup and Blade output removed.
     */
    private function visibleText(string $tag): string
    {
        $inner = substr($tag, (int) strpos($tag, '>') + 1);
        $inner = preg_replace('/\{\{.*?\}\}|<[^>]*>|@\w+(\([^)]*\))?/s', ' ', $inner) ?? '';

        return strtolower(trim(preg_replace('/\s+/', ' ', $inner) ?? ''));
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
