<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * A published april view wins over the package for good.
 *
 * It keeps every fix the package makes after the copy was taken, and it
 * says nothing while it does so: the screen still renders, it just renders
 * last year's component. `resources/views/vendor/april/components/steps.blade.php`
 * sat here for that reason and froze the component before it could stand
 * upright.
 *
 * This app overrides three controls for one stated reason: an april tag
 * cannot carry a blade directive, so these three work their own refusal
 * wiring out. See `.ai/rules/views.md`. Strip that one change and what is
 * left must be the packaged view, word for word.
 */
class PublishedAprilViewTest extends TestCase
{
    /**
     * The one change this app makes to a packaged view.
     */
    private const CUSTOMISATION = '->merge(april_field_error_attributes($attributes))';

    /**
     * The line that says why the file is here at all.
     */
    private const REASON = '{{-- Overrides april-ui so a refused field says so. See app/helpers.php. --}}';

    public function test_every_published_view_says_why_it_overrides_the_package(): void
    {
        $offenders = [];

        foreach ($this->publishedViews() as $name => $source) {
            if (!str_contains($source, self::REASON)) {
                $offenders[] = $name;
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "A published view with no reason on it is a copy nobody meant to keep. Delete it and let the package render:\n".implode("\n", $offenders)
        );
    }

    public function test_every_published_view_tracks_the_package_it_copied(): void
    {
        $offenders = [];

        foreach ($this->publishedViews() as $name => $source) {
            $packaged = $this->packagedView($name);

            if ($packaged === null) {
                $offenders[] = $name.' overrides a component april-ui no longer ships.';

                continue;
            }

            if ($this->withoutCustomisation($source) !== $packaged) {
                $offenders[] = $name.' has fallen behind the packaged view.';
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "Copy the packaged view again and add back the refusal wiring:\n".implode("\n", $offenders)
        );
    }

    public function test_the_comparison_reads_the_change_it_claims_to_read(): void
    {
        $published = self::REASON."\n".'<input {{$attributes'.self::CUSTOMISATION.'->twMerge([])}}>';

        $this->assertSame(
            '<input {{$attributes->twMerge([])}}>',
            $this->withoutCustomisation($published)
        );
    }

    /**
     * The app's own copy of a packaged view, with its one change removed.
     */
    private function withoutCustomisation(string $source): string
    {
        return str_replace(
            [self::REASON."\n", self::CUSTOMISATION],
            '',
            $source
        );
    }

    /**
     * @return array<string, string> file name to source
     */
    private function publishedViews(): array
    {
        $found = [];

        foreach (glob(dirname(__DIR__, 2).'/resources/views/vendor/april/components/*.blade.php') ?: [] as $path) {
            $found[basename($path)] = (string) file_get_contents($path);
        }

        return $found;
    }

    private function packagedView(string $name): ?string
    {
        $path = dirname(__DIR__, 2).'/vendor/yungifez/april-ui/resources/views/components/'.$name;

        return is_file($path) ? (string) file_get_contents($path) : null;
    }
}
