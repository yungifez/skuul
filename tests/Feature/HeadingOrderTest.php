<?php

namespace Tests\Feature;

use App\Enums\CalendarEventType;
use App\Models\CalendarEvent;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A reader who moves by heading walks the levels in order. A screen that
 * jumps from h1 to h3 hides a level, so that reader cannot tell whether a
 * section is missing or the page simply skipped a number.
 */
class HeadingOrderTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_dashboard_steps_through_its_headings(): void
    {
        $this->authorized_user([]);

        $this->assertHeadingsStepDown($this->htmlOf(route('dashboard')));
    }

    public function test_a_card_title_sits_one_level_under_the_page(): void
    {
        $this->authorized_user(['read cohort', 'create cohort']);

        $html = $this->htmlOf(route('cohorts.index'));

        // april-ui fixes the card title at h3, which skips h2 on every screen
        // that opens with the page heading. resources/views/vendor overrides it.
        $this->assertStringContainsString('<h2 data-slot="card-title"', $html);
        $this->assertStringNotContainsString('<h3 data-slot="card-title"', $html);
        $this->assertHeadingsStepDown($html);
    }

    public function test_an_alert_title_sits_one_level_under_the_page(): void
    {
        $this->authorized_user(['read calendar event', 'update calendar event']);
        CalendarEvent::create([
            'school_id' => $this->workingSchool()->id,
            'title' => 'Mid-term break',
            'type' => CalendarEventType::Holiday,
            'is_published' => false,
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]);

        $html = $this->htmlOf(route('calendar-events.index'));

        // april-ui fixes the alert title at h5, three levels under the page.
        $this->assertStringContainsString('<h2 data-slot="alert-title"', $html);
        $this->assertStringNotContainsString('<h5 data-slot="alert-title"', $html);
        $this->assertHeadingsStepDown($html);
    }

    /**
     * Read a screen as somebody who may open it.
     */
    private function htmlOf(string $url): string
    {
        return (string) $this->get($url)->assertOk()->getContent();
    }

    /**
     * Fail if any heading on the page is more than one level under the last.
     */
    private function assertHeadingsStepDown(string $html): void
    {
        preg_match_all('/<h([1-6])[\s>]/', $html, $matches);

        $levels = array_map('intval', $matches[1]);

        $this->assertNotEmpty($levels, 'The page holds no headings at all.');
        $this->assertSame(1, $levels[0], 'The first heading on the page must be the h1.');

        $previous = $levels[0];

        foreach ($levels as $level) {
            $this->assertLessThanOrEqual(
                $previous + 1,
                $level,
                "A heading jumps from h{$previous} to h{$level}."
            );

            $previous = $level;
        }
    }
}
