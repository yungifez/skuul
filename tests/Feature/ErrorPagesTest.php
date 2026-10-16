<?php

namespace Tests\Feature;

use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * A failed request shows a page in the app's own look, with a way back.
 */
class ErrorPagesTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_missing_page_says_so_and_links_home(): void
    {
        $this->get('/no-such-page-at-all')
            ->assertNotFound()
            ->assertSee('Page not found')
            ->assertSee('Go to the dashboard')
            ->assertDontSee('NOT FOUND');
    }

    public function test_a_refused_page_keeps_a_reason_the_app_gave(): void
    {
        Route::get('/_test/refused', fn () => abort(403, 'Only the bursar can open this.'));
        Route::get('/_test/refused-plainly', fn () => abort(403));

        $this->get('/_test/refused')->assertForbidden()->assertSee('Only the bursar can open this.');
        $this->get('/_test/refused-plainly')->assertForbidden()->assertSee('Your account cannot open this page.');
    }

    public function test_a_server_error_hides_what_went_wrong(): void
    {
        config(['app.debug' => false]);
        Route::get('/_test/broken', fn () => throw new \RuntimeException('SQLSTATE secret detail'));

        $this->get('/_test/broken')
            ->assertStatus(500)
            ->assertSee('Something went wrong')
            ->assertDontSee('SQLSTATE secret detail');
    }

    public function test_every_error_page_renders_in_the_app_layout(): void
    {
        foreach ([401, 403, 404, 409, 419, 429, 500, 503] as $code) {
            $html = view("errors.$code", ['exception' => new HttpException($code)])->render();

            $this->assertStringContainsString('bg-background', $html, "Error page $code does not use the app layout.");
            $this->assertStringContainsString('h-11', $html, "Error page $code has no way on.");
        }
    }
}
