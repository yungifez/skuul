<?php

namespace Tests\Feature;

use App\Enums\Feature;
use App\Models\CalendarEvent;
use App\Models\School;
use App\Services\School\SchoolContext;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Livewire\Mechanisms\HandleRequests\HandleRequests;
use Tests\TestCase;

/**
 * A screen left open in a tab still talks to the server. When the school
 * turns its feature off, or the person switches school in another tab, that
 * tab must stop writing, not only the address.
 */
class StaleLivewireTabTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_open_screen_cannot_save_after_the_feature_is_turned_off(): void
    {
        $this->authorized_user(['create calendar event', 'read calendar event']);
        $snapshot = $this->snapshotFrom(route('calendar-events.create'));

        features()->disable(Feature::Events, $this->workingSchool()->id);

        $this->saveTitled($snapshot, 'Sports day')->assertNotFound();
        $this->assertSame(0, CalendarEvent::query()->count());
    }

    public function test_an_open_screen_still_saves_while_the_feature_is_on(): void
    {
        $this->authorized_user(['create calendar event', 'read calendar event']);
        $snapshot = $this->snapshotFrom(route('calendar-events.create'));

        $this->saveTitled($snapshot, 'Sports day')->assertOk();
        $this->assertSame('Sports day', CalendarEvent::sole()->title);
    }

    public function test_a_tab_opened_in_one_school_cannot_save_into_another(): void
    {
        $this->authorized_user(['create calendar event', 'read calendar event']);
        $user = auth()->user();
        $home = $this->workingSchool();
        $other = School::factory()->create();
        $this->memberOf($other, $user);
        school_context()->set($other, remember: false);
        $user->givePermissionTo(['create calendar event', 'read calendar event']);
        school_context()->set($home, remember: false);

        $snapshot = $this->snapshotFrom(route('calendar-events.create'));

        // The person switches to the other school in a second tab.
        $this->withSession([SchoolContext::SESSION_KEY => $other->id]);

        $this->saveTitled($snapshot, 'Sports day')
            ->assertStatus(409)
            ->assertSee('You switched school in another tab.');
        $this->assertSame(0, CalendarEvent::query()->withoutGlobalScopes()->count());
    }

    /**
     * Read a component's snapshot from a page, as the browser holds it.
     */
    private function snapshotFrom(string $url, string $component = 'calendar-event-editor'): string
    {
        $html = (string) $this->get($url)->assertOk()->getContent();
        preg_match_all('/wire:snapshot="([^"]+)"/', $html, $matches);

        foreach ($matches[1] as $encoded) {
            $snapshot = html_entity_decode($encoded, ENT_QUOTES);

            if (json_decode($snapshot, true)['memo']['name'] === $component) {
                return $snapshot;
            }
        }

        $this->fail("The page has no $component component.");
    }

    /**
     * Send what the browser sends when the title is typed and save is pressed.
     */
    private function saveTitled(string $snapshot, string $title): TestResponse
    {
        return $this->withHeaders(['X-Livewire' => 'true'])->postJson(app(HandleRequests::class)->getUpdateUri(), [
            '_token' => csrf_token(),
            'components' => [[
                'snapshot' => $snapshot,
                'updates' => ['title' => $title],
                'calls' => [['path' => '', 'method' => 'save', 'params' => []]],
            ]],
        ]);
    }
}
