<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * A phone field must ask the phone for a dial pad.
 */
class PhoneFieldKeyboardTest extends TestCase
{
    public function test_every_phone_field_asks_for_the_number_keyboard(): void
    {
        $offenders = [];

        foreach ($this->views() as $path => $source) {
            foreach (explode("\n", $source) as $number => $line) {
                // Only an input opens a keyboard. The error line under it names
                // the field too.
                if (!str_contains($line, '<input') || !preg_match('/(?:name|wire:model(?:\.[a-z]+)*)="(?:[a-z_.]*[_.])?phone"/i', $line)) {
                    continue;
                }

                // The line holds the whole control, so the type sits on it too.
                if (str_contains($line, 'type="tel"')) {
                    continue;
                }

                $offenders[] = $path.':'.($number + 1);
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "A phone field without type=\"tel\" opens a letter keyboard on a phone:\n".implode("\n", $offenders)
        );
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
