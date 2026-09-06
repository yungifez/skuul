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

    public function test_the_sidebar_is_a_labelled_navigation_landmark(): void
    {
        $html = $this->dashboardHtml();

        // april draws the sidebar twice, once for the desktop layout and once
        // for the mobile sheet. Only one of the two is ever visible, so both
        // carry the landmark.
        $this->assertSame(2, substr_count($html, 'role="navigation" aria-label="Main"'));
    }

    public function test_a_dashboard_screen_holds_one_top_level_heading(): void
    {
        $html = $this->dashboardHtml();

        // The header used to mark the product name as an h1, so every screen
        // announced two top-level headings and the first was not the page.
        $this->assertSame(1, substr_count($html, '<h1'), 'The page must hold exactly one <h1> element.');
        $this->assertStringNotContainsString('<h1 class="hidden text-sm', $html);
    }

    public function test_the_sidebar_trigger_keeps_its_width_on_a_narrow_screen(): void
    {
        $html = $this->dashboardHtml();

        // The trigger sits on a flex line next to a min-w-0 block, so without
        // shrink-0 a narrow screen squeezed it to 18px. It is the only way to
        // reach the menu on a phone.
        $this->assertMatchesRegularExpression(
            '/<button[^>]*data-slot="sidebar-trigger"[^>]*class="[^"]*\bshrink-0\b/',
            $html,
            'The sidebar trigger must not shrink.'
        );
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
