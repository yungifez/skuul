<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * A breadcrumb trail is a list of page names. Every crumb must read like the
 * page it points at, and the same page must carry the same name everywhere.
 */
class BreadcrumbLabelTest extends TestCase
{
    /**
     * Words that name an action, not a page.
     *
     * A trail that ends in "create" tells the reader nothing. It must name the
     * page, the way the heading does: "Create student".
     *
     * @var list<string>
     */
    private const BARE_ACTIONS = ['create', 'edit', 'show', 'view', 'index', 'new'];

    public function test_no_breadcrumb_starts_with_a_small_letter(): void
    {
        $offenders = [];

        foreach ($this->breadcrumbLabels() as [$file, $line, $label]) {
            if (preg_match('/^[a-z]/', $label)) {
                $offenders[] = "{$file}:{$line} \"{$label}\"";
            }
        }

        $this->assertSame([], $offenders, "These breadcrumbs start with a small letter:\n".implode("\n", $offenders));
    }

    public function test_a_bare_action_crumb_always_follows_the_record_it_acts_on(): void
    {
        $offenders = [];

        foreach ($this->breadcrumbTrails() as $file => $trail) {
            foreach ($trail as $position => [$line, $label, $isLiteral]) {
                if (!$isLiteral || !in_array(mb_strtolower($label), self::BARE_ACTIONS, true)) {
                    continue;
                }

                // "Classes > Kindergarten 1 > Edit" reads well, because the
                // crumb before it names the record. "Fees > Create" does not.
                $previousNamesARecord = isset($trail[$position - 1]) && $trail[$position - 1][2] === false;

                if (!$previousNamesARecord) {
                    $offenders[] = "{$file}:{$line} \"{$label}\"";
                }
            }
        }

        sort($offenders);

        $this->assertSame([], $offenders, "These breadcrumbs name an action, not a page:\n".implode("\n", $offenders));
    }

    /**
     * Every literal breadcrumb label in the view folder.
     *
     * @return list<array{0: string, 1: int, 2: string}>
     */
    private function breadcrumbLabels(): array
    {
        $labels = [];

        foreach ($this->breadcrumbTrails() as $file => $trail) {
            foreach ($trail as [$line, $label, $isLiteral]) {
                if ($isLiteral) {
                    $labels[] = [$file, $line, $label];
                }
            }
        }

        return $labels;
    }

    /**
     * The breadcrumb trail of every view, in the order the reader reads it.
     *
     * A crumb built from a record, such as $student->name, carries false for
     * its literal flag: the record supplies its wording, not the view.
     *
     * @return array<string, list<array{0: int, 1: string, 2: bool}>>
     */
    private function breadcrumbTrails(): array
    {
        $root = dirname(__DIR__, 2).'/resources/views';
        $trails = [];

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

        foreach ($files as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $relative = str_replace($root.'/', '', $file->getPathname());

            foreach (file($file->getPathname()) as $index => $line) {
                if (!preg_match_all("/'text'\s*=>\s*(.+?)(?:,\s*'active')?\s*[,\]]/", $line, $matches)) {
                    continue;
                }

                foreach ($matches[1] as $value) {
                    $value = trim($value);
                    $isLiteral = (bool) preg_match("/^'([^']*)'$/", $value, $literal);
                    $trails[$relative][] = [$index + 1, $isLiteral ? $literal[1] : $value, $isLiteral];
                }
            }
        }

        return $trails;
    }
}
