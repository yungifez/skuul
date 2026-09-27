<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Every component tag in the views must compile.
 *
 * A double quote inside a component attribute ends that attribute early.
 * Blade then leaves the whole tag as raw HTML, and the browser shows
 * nothing where the component should be, without any error.
 */
class BladeComponentTagTest extends TestCase
{
    public function test_every_component_tag_in_the_views_compiles(): void
    {
        $uncompiled = [];

        foreach (File::allFiles(resource_path('views')) as $file) {
            if (!str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $compiled = Blade::compileString($file->getContents());

            if (preg_match_all('/<x-[a-z][\w.:-]*/', $compiled, $matches) > 0) {
                $uncompiled[] = $file->getRelativePathname().': '.implode(', ', array_unique($matches[0]));
            }
        }

        $this->assertSame([], $uncompiled, 'These views leave component tags uncompiled.');
    }
}
