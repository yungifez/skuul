<?php

namespace Tests\Feature;

use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The frame every dashboard screen sits in.
 */
class AppLayoutTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_dashboard_screen_holds_one_main_landmark(): void
    {
        $html = $this->dashboardHtml();

        // april:sidebar-inset already renders a <main>. A second one nested
        // inside it is invalid, and a reader hears two main regions.
        $this->assertSame(1, substr_count($html, '<main'), 'The page must hold exactly one <main> element.');
    }

    public function test_the_skip_link_points_at_a_target_that_can_take_focus(): void
    {
        $html = $this->dashboardHtml();

        $this->assertStringContainsString('href="#main"', $html);
        $this->assertSame(1, substr_count($html, 'id="main"'));
        $this->assertMatchesRegularExpression('/id="main"[^>]*tabindex="-1"/', $html);
    }

    public function test_the_skip_link_shows_itself_once_it_takes_focus(): void
    {
        $html = $this->dashboardHtml();

        // Without this the link stays sr-only while focused, so a sighted
        // reader who tabs to it cannot see where the focus went.
        $this->assertStringContainsString('focus:not-sr-only', $html);
    }

    /**
     * Render the dashboard as somebody who may open it.
     */
    private function dashboardHtml(): string
    {
        $actor = $this->authorized_user([]);

        return (string) $actor->get(route('dashboard'))->assertOk()->getContent();
    }
}
