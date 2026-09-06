<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * The bundle disables a form's buttons on submit to stop a double submit.
 * A wire:submit form is answered over AJAX, so the page never navigates and
 * neither pageshow nor livewire:navigated ever fires. Something has to hand
 * those buttons back, or the form dies after its first submit.
 */
class LivewireFormReleaseTest extends TestCase
{
    public function test_the_submit_guard_still_disables_the_buttons_it_must_release(): void
    {
        // If this guard goes away the release below has nothing to undo, and
        // the rest of this test would pass while protecting nothing.
        $bundle = $this->bundle();

        $this->assertStringContainsString('form.dataset.submitting = "true";', $bundle);
        $this->assertStringContainsString('submitButton.disabled = true;', $bundle);
    }

    public function test_a_livewire_form_is_released_when_its_request_comes_back(): void
    {
        $bundle = $this->bundle();

        $this->assertStringContainsString('Livewire.hook("commit"', $bundle);
        $this->assertStringContainsString('respond(releaseLivewireForms);', $bundle);

        // A request that never reaches the server must free the form too.
        $this->assertStringContainsString('fail(releaseLivewireForms);', $bundle);
    }

    public function test_the_release_only_touches_forms_livewire_answers_itself(): void
    {
        // A plain form submits by navigating. Freeing its buttons mid-flight
        // would let the reader send the same request twice.
        $release = $this->body('function releaseLivewireForms()');

        $this->assertStringContainsString('if (isLivewireForm(form))', $release);
        $this->assertStringContainsString('wire:submit', $this->body('function isLivewireForm(form)'));
    }

    public function test_the_hook_is_registered_before_livewire_starts(): void
    {
        // Livewire.start() fires livewire:init. A listener added after the
        // call never hears it, and the hook is never registered.
        $bundle = $this->bundle();
        $listener = strpos($bundle, 'document.addEventListener("livewire:init"');

        $this->assertNotFalse($listener, 'The bundle no longer listens for livewire:init.');
        $this->assertLessThan(
            strpos($bundle, 'Livewire.start();'),
            $listener,
            'The commit hook must be registered before Livewire starts.'
        );
    }

    public function test_every_livewire_form_in_the_views_is_covered_by_the_release(): void
    {
        // The release matches on the attribute name. A form written any other
        // way, such as wire:submit.prevent, still has to be caught.
        foreach ($this->livewireFormAttributes() as $attribute) {
            $this->assertStringStartsWith(
                'wire:submit',
                $attribute,
                "isLivewireForm() would not recognise a form written with {$attribute}."
            );
        }
    }

    /**
     * Every wire:submit attribute name used on a form across the views.
     *
     * @return array<int, string>
     */
    private function livewireFormAttributes(): array
    {
        $views = dirname(__DIR__, 2).'/resources/views';
        $found = [];

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($views));

        foreach ($files as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            preg_match_all('/<form\b[^>]*?\b(wire:submit[\w.]*)/s', (string) file_get_contents($file->getPathname()), $matches);
            $found = array_merge($found, $matches[1]);
        }

        $this->assertNotEmpty($found, 'No wire:submit form was found, so this guard has nothing to protect.');

        return array_values(array_unique($found));
    }

    /**
     * The text of one named function in the bundle.
     */
    private function body(string $signature): string
    {
        $bundle = $this->bundle();
        $start = strpos($bundle, $signature);

        $this->assertNotFalse($start, "The bundle no longer declares {$signature}.");

        return substr($bundle, $start, strpos($bundle, "\n}\n", $start) - $start);
    }

    private function bundle(): string
    {
        $path = dirname(__DIR__, 2).'/resources/js/app.js';
        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }
}
