<?php

namespace Tests\Feature;

use App\Enums\CalendarEventType;
use App\Enums\Feature;
use App\Models\AcademicCycleSection;
use App\Models\CalendarEvent;
use App\Models\School;
use App\Models\User;
use App\Services\Calendar\SchoolCalendar;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The calendar says what is on and whether the school is open. A draft says
 * neither, until somebody publishes it.
 */
class CalendarEventScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_calendar_starts_empty(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);

        $this->get(route('calendar-events.index'))
            ->assertOk()
            ->assertSee('Nothing is on this month')
            ->assertSee('The school is open every day this month.')
            ->assertSee(now()->format('F Y'));
    }

    public function test_a_day_is_added_as_a_draft(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);

        $response = $this->post(route('calendar-events.store'), [
            'title' => 'Mid-term break',
            'type' => CalendarEventType::Holiday->value,
            'is_all_day' => '1',
            'starts_at' => now()->addWeek()->format('Y-m-d\T08:00'),
            'ends_at' => now()->addWeek()->addDays(2)->format('Y-m-d\T15:00'),
        ]);

        $event = CalendarEvent::inSchool()->sole();

        $response->assertRedirect(route('calendar-events.edit', $event));

        $this->assertFalse($event->is_published);
        $this->assertSame('00:00:00', $event->starts_at->format('H:i:s'));
        $this->assertSame('23:59:59', $event->ends_at->format('H:i:s'));
    }

    public function test_a_day_cannot_end_before_it_starts(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);

        $this->post(route('calendar-events.store'), [
            'title' => 'Backwards',
            'type' => CalendarEventType::Holiday->value,
            'is_all_day' => '1',
            'starts_at' => now()->addWeek()->format('Y-m-d\TH:i'),
            'ends_at' => now()->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors('ends_at');

        $this->assertSame(0, CalendarEvent::inSchool()->count());
    }

    public function test_a_draft_closure_does_not_shut_the_school(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'publish calendar event']);
        $event = $this->event(['type' => CalendarEventType::Closure, 'is_published' => false]);

        $this->assertTrue(app(SchoolCalendar::class)->isTeachingDay($event->starts_at));

        $this->from(route('calendar-events.edit', $event))
            ->put(route('calendar-events.publication.update', $event), ['is_published' => '1'])
            ->assertRedirect(route('calendar-events.edit', $event));

        $this->assertFalse(app(SchoolCalendar::class)->isTeachingDay($event->starts_at));
    }

    public function test_a_person_who_may_only_read_never_sees_a_draft(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);
        $draft = $this->event(['title' => 'A draft nobody reads', 'is_published' => false]);
        $published = $this->event(['title' => 'A day the school reads', 'is_published' => true]);

        $this->authorized_user(['read calendar event']);

        $this->get(route('calendar-events.index', ['month' => $draft->starts_at->format('Y-m')]))
            ->assertOk()
            ->assertSee('A day the school reads')
            ->assertDontSee('A draft nobody reads');

        $this->get(route('calendar-events.edit', $draft))->assertForbidden();
        $this->get(route('calendar-events.edit', $published))->assertOk();
    }

    public function test_the_month_shows_which_days_the_school_is_shut(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);
        $closure = $this->event([
            'title' => 'Public holiday',
            'type' => CalendarEventType::Holiday,
            'is_published' => true,
        ]);

        $this->get(route('calendar-events.index', ['month' => $closure->starts_at->format('Y-m')]))
            ->assertOk()
            ->assertSee($closure->starts_at->format('j M').'.')
            ->assertSee('The school is shut');
    }

    public function test_the_month_can_be_stepped_through(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);
        $nextMonth = now()->addMonthNoOverflow()->startOfMonth();
        $event = $this->event([
            'title' => 'Next month event',
            'starts_at' => $nextMonth->copy()->addDays(2)->startOfDay(),
            'ends_at' => $nextMonth->copy()->addDays(2)->endOfDay(),
        ]);

        $this->get(route('calendar-events.index'))
            ->assertOk()
            ->assertDontSee('Next month event');

        $this->get(route('calendar-events.index', ['month' => $nextMonth->format('Y-m')]))
            ->assertOk()
            ->assertSee('Next month event')
            ->assertSee($nextMonth->format('F Y'));
    }

    public function test_a_bad_month_falls_back_to_this_one(): void
    {
        $this->authorized_user(['read calendar event']);

        $this->get(route('calendar-events.index', ['month' => 'not-a-month']))
            ->assertOk()
            ->assertSee(now()->format('F Y'));
    }

    public function test_a_day_can_name_the_home_groups_it_is_for(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => current_academic_year_id(),
        ]);

        $this->post(route('calendar-events.store'), [
            'title' => 'Year meeting',
            'type' => CalendarEventType::ParentMeeting->value,
            'is_all_day' => '1',
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'ends_at' => now()->format('Y-m-d\TH:i'),
            'academic_cycle_section_ids' => [$section->id],
        ]);

        $event = CalendarEvent::inSchool()->sole();

        $this->assertSame(1, $event->audiences()->count());
        $this->assertSame($section->id, $event->audiences()->sole()->academic_cycle_section_id);
        $this->assertFalse($event->isForEverybody());
    }

    public function test_changing_a_day_replaces_who_it_is_for(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);
        $event = $this->event();
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => current_academic_year_id(),
        ]);

        $this->from(route('calendar-events.edit', $event))
            ->put(route('calendar-events.update', $event), [
                'title' => 'A better title',
                'type' => CalendarEventType::Assembly->value,
                'is_all_day' => '1',
                'starts_at' => $event->starts_at->format('Y-m-d\TH:i'),
                'ends_at' => $event->ends_at->format('Y-m-d\TH:i'),
                'academic_cycle_section_ids' => [$section->id],
            ])
            ->assertRedirect(route('calendar-events.edit', $event));

        $this->assertSame('A better title', $event->fresh()->title);
        $this->assertSame(1, $event->audiences()->count());
    }

    public function test_a_day_is_removed_from_the_calendar(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'delete calendar event']);
        $event = $this->event();

        $this->delete(route('calendar-events.destroy', $event))
            ->assertRedirect(route('calendar-events.index', ['month' => $event->starts_at->format('Y-m')]));

        $this->assertSame(0, CalendarEvent::inSchool()->count());
    }

    public function test_publishing_needs_its_own_permission(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);
        $event = $this->event(['is_published' => false]);

        $this->from(route('calendar-events.edit', $event))
            ->put(route('calendar-events.publication.update', $event), ['is_published' => '1'])
            ->assertForbidden();

        $this->assertFalse($event->fresh()->is_published);
    }

    public function test_the_screen_needs_permission(): void
    {
        $this->unauthorized_user();

        $this->get(route('calendar-events.index'))->assertForbidden();
    }

    public function test_a_school_that_turned_events_off_has_no_screen(): void
    {
        $this->authorized_user(['read calendar event']);
        app(FeatureManager::class)->disable(Feature::Events);

        $this->get(route('calendar-events.index'))->assertNotFound();
    }

    public function test_a_day_can_name_the_people_it_is_for(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);
        $person = $this->memberOf($this->workingSchool());

        $this->post(route('calendar-events.store'), [
            'title' => 'A meeting about one child',
            'type' => CalendarEventType::Appointment->value,
            'is_all_day' => '0',
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'ends_at' => now()->addHour()->format('Y-m-d\TH:i'),
            'user_ids' => [$person->id],
        ]);

        $event = CalendarEvent::inSchool()->sole();

        $this->assertSame($person->id, $event->audiences()->sole()->user_id);
        $this->assertFalse($event->isForEverybody());
    }

    public function test_a_day_cannot_name_somebody_from_another_school(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);
        $stranger = $this->personOfAnotherSchool();

        $this->post(route('calendar-events.store'), [
            'title' => 'A meeting about one child',
            'type' => CalendarEventType::Appointment->value,
            'is_all_day' => '0',
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'ends_at' => now()->addHour()->format('Y-m-d\TH:i'),
            'user_ids' => [$stranger->id],
        ])->assertSessionHasErrors('user_ids.0');

        $this->assertSame(0, CalendarEvent::inSchool()->count());
    }

    public function test_the_form_offers_the_people_of_this_school_only(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);
        $colleague = $this->memberOf($this->workingSchool(), User::factory()->create(['name' => 'Ada Colleague']));
        $this->personOfAnotherSchool('Ben Elsewhere');

        $this->get(route('calendar-events.create'))
            ->assertOk()
            ->assertSee('Ada Colleague')
            ->assertDontSee('Ben Elsewhere')
            ->assertSee('value="'.$colleague->id.'"', escape: false);
    }

    /**
     * Make a person who belongs to another school only.
     *
     * The user factory grants a membership in the working school, so the
     * membership has to go before the other school is named.
     */
    private function personOfAnotherSchool(?string $name = null): User
    {
        $person = User::factory()->create($name === null ? [] : ['name' => $name]);
        $person->schoolMemberships()->delete();

        return $this->memberOf(School::factory()->create(), $person->refresh());
    }

    /**
     * Put one day on the calendar of the working school.
     *
     * @param  array<string, mixed>  $values
     */
    private function event(array $values = []): CalendarEvent
    {
        return CalendarEvent::create([
            'school_id' => $this->workingSchool()->id,
            'title' => 'Mid-term break',
            'type' => CalendarEventType::Holiday,
            'is_published' => true,
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
            ...$values,
        ]);
    }
}
