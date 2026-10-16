<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Muted text on a muted surface falls below the readable minimum.
 *
 * On the light theme --muted-foreground over bg-muted/50 measures 4.39:1.
 * WCAG AA asks for 4.5:1 on normal text. The pair is only 0.11 short, so it
 * looks fine in a screenshot and still fails a reader with low vision.
 */
class MutedTextOnMutedSurfaceTest extends TestCase
{
    public function test_no_view_paints_muted_text_on_a_muted_surface(): void
    {
        $offenders = [];

        foreach ($this->bladeFiles() as $path => $contents) {
            foreach (explode("\n", $contents) as $number => $line) {
                if (str_contains($line, 'bg-muted/50') && str_contains($line, 'text-muted-foreground')) {
                    $offenders[] = $path.':'.($number + 1);
                }
            }
        }

        $this->assertSame([], $offenders, implode("\n", array_merge(
            ['Use text-foreground on a muted surface. These lines pair it with muted text:'],
            $offenders
        )));
    }

    /**
     * @return array<string, string>
     */
    private function bladeFiles(): array
    {
        $root = dirname(__DIR__, 2).'/resources/views';
        $this->assertDirectoryExists($root);

        $files = [];
        $tree = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

        foreach ($tree as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                $files[substr($file->getPathname(), strlen($root) + 1)] = (string) file_get_contents($file->getPathname());
            }
        }

        $this->assertNotEmpty($files);

        return $files;
    }
}
