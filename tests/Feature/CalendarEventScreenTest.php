<?php

namespace Tests\Feature;

use App\Enums\AcademicStructureStatus;
use App\Enums\CalendarEventType;
use App\Enums\Feature;
use App\Enums\SchoolMembershipStatus;
use App\Livewire\CalendarEventDirectory as CalendarEventDirectoryComponent;
use App\Livewire\CalendarEventEditor;
use App\Models\AcademicCycleSection;
use App\Models\CalendarEvent;
use App\Models\School;
use App\Models\User;
use App\Services\Calendar\SchoolCalendar;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
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

    public function test_the_add_link_on_a_day_is_big_enough_to_press(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);

        $html = (string) $this->get(route('calendar-events.index'))->assertOk()->getContent();

        // The plus icon alone is a 12px target. WCAG 2.2 asks for 24px.
        $this->assertMatchesRegularExpression(
            '/<a[^>]*class="[^"]*\bsize-6\b[^"]*"[^>]*aria-label="Add a day on /',
            $html,
            'Each day needs an add link of at least 24px.'
        );
    }

    public function test_a_day_is_added_as_a_draft(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);
        $day = now()->addWeek();

        $this->get(route('calendar-events.create', ['day' => $day->toDateString()]))->assertOk()->assertSeeLivewire(CalendarEventEditor::class);

        Livewire::test(CalendarEventEditor::class, ['day' => $day->toDateString()])
            ->assertSet('startsAt', $day->toDateString())
            ->set('title', 'Mid-term break')
            ->set('type', CalendarEventType::Holiday->value)
            ->set('endsAt', $day->copy()->addDays(2)->toDateString())
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('calendar-events.edit', CalendarEvent::inSchool()->sole()));

        $event = CalendarEvent::inSchool()->sole();

        $this->assertFalse($event->is_published);
        $this->assertSame('00:00:00', $event->starts_at->format('H:i:s'));
        $this->assertSame('23:59:59', $event->ends_at->format('H:i:s'));
        $this->assertSame($day->copy()->addDays(2)->toDateString(), $event->ends_at->toDateString());
    }

    public function test_a_bad_day_in_the_link_falls_back_to_today(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);

        Livewire::test(CalendarEventEditor::class, ['day' => 'not-a-day'])
            ->assertSet('startsAt', now()->toDateString());
    }

    public function test_switching_times_on_keeps_the_chosen_days(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);

        Livewire::test(CalendarEventEditor::class)
            ->set('startsAt', '2026-10-05')
            ->set('endsAt', '2026-10-05')
            ->set('isAllDay', false)
            ->assertSet('startsAt', '2026-10-05T08:00')
            ->assertSet('endsAt', '2026-10-05T15:00')
            ->set('isAllDay', true)
            ->assertSet('startsAt', '2026-10-05');
    }

    public function test_a_day_cannot_end_before_it_starts(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);

        Livewire::test(CalendarEventEditor::class)
            ->set('title', 'Backwards')
            ->set('startsAt', now()->addWeek()->toDateString())
            ->set('endsAt', now()->toDateString())
            ->call('save')
            ->assertHasErrors(['endsAt' => 'after_or_equal'])
            ->assertSee('A day on the calendar cannot end before it starts.');

        $this->assertSame(0, CalendarEvent::inSchool()->count());
    }

    public function test_a_closure_cannot_run_for_years_by_a_typo(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);

        Livewire::test(CalendarEventEditor::class)
            ->set('title', 'Strike closure')
            ->set('type', CalendarEventType::Closure->value)
            ->set('startsAt', '2026-10-05')
            ->set('endsAt', '2062-10-05')
            ->assertSee('Once published, attendance and the timetable treat these days as shut.')
            ->call('save')
            ->assertHasErrors('endsAt')
            ->assertSee('cannot last more than a year');

        $this->assertSame(0, CalendarEvent::inSchool()->count());
    }

    public function test_a_draft_closure_does_not_shut_the_school(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event', 'publish calendar event']);
        $event = $this->event(['type' => CalendarEventType::Closure, 'is_published' => false]);

        $this->assertTrue(app(SchoolCalendar::class)->isTeachingDay($event->starts_at));

        Livewire::test(CalendarEventEditor::class, ['event' => $event])
            ->assertSee('Draft')
            ->call('publish')
            ->assertDispatched('status-message', type: 'success', message: 'Published. The school can read it now.')
            ->assertSee('On the calendar');

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

        Livewire::test(CalendarEventDirectoryComponent::class)
            ->set('draftsOnly', true)
            ->assertSee('Nothing matches this filter')
            ->assertDontSee(route('calendar-events.edit', $draft));

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

    public function test_calendar_month_type_and_draft_filters_change_without_a_reload(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);
        $holiday = $this->event([
            'title' => 'Current holiday',
            'type' => CalendarEventType::Holiday,
        ]);
        $assembly = $this->event([
            'title' => 'Current assembly',
            'type' => CalendarEventType::Assembly,
        ]);
        $draft = $this->event([
            'title' => 'Current draft assembly',
            'type' => CalendarEventType::Assembly,
            'is_published' => false,
        ]);

        Livewire::test(CalendarEventDirectoryComponent::class)
            ->assertSee(route('calendar-events.edit', $holiday))
            ->assertSee(route('calendar-events.edit', $assembly))
            ->set('type', CalendarEventType::Assembly->value)
            ->assertSee(route('calendar-events.edit', $assembly))
            ->assertDontSee(route('calendar-events.edit', $holiday))
            ->set('type', 'not-a-calendar-kind')
            ->assertSet('type', '')
            ->assertSee(route('calendar-events.edit', $holiday))
            ->set('draftsOnly', true)
            ->assertSee(route('calendar-events.edit', $draft))
            ->assertDontSee(route('calendar-events.edit', $assembly))
            ->call('nextMonth')
            ->assertSet('month', now()->addMonthNoOverflow()->format('Y-m'))
            ->assertDontSee(route('calendar-events.edit', $draft))
            ->call('previousMonth')
            ->assertSee(route('calendar-events.edit', $draft))
            ->call('showCurrentMonth')
            ->assertSet('month', now()->format('Y-m'))
            ->call('clearFilters')
            ->assertSet('type', '')
            ->assertSet('draftsOnly', false)
            ->assertSee(route('calendar-events.edit', $holiday));
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

        Livewire::test(CalendarEventEditor::class)
            ->set('title', 'Year meeting')
            ->set('type', CalendarEventType::ParentMeeting->value)
            ->set('sectionIds', [$section->id, $section->id])
            ->call('save')
            ->assertHasErrors('sectionIds.1')
            ->set('sectionIds', [$section->id])
            ->call('save')
            ->assertHasNoErrors();

        $event = CalendarEvent::inSchool()->sole();

        $this->assertSame(1, $event->audiences()->count());
        $this->assertSame($section->id, $event->audiences()->sole()->academic_cycle_section_id);
        $this->assertFalse($event->isForEverybody());
    }

    public function test_only_this_years_open_sections_are_offered(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);
        $current = AcademicCycleSection::factory()->create(['school_id' => $this->workingSchool()->id, 'academic_year_id' => current_academic_year_id(), 'name' => 'Current group']);
        $archived = AcademicCycleSection::factory()->create(['school_id' => $this->workingSchool()->id, 'academic_year_id' => current_academic_year_id(), 'name' => 'Archived group', 'status' => AcademicStructureStatus::Archived]);
        $foreign = AcademicCycleSection::factory()->create(['school_id' => School::factory()->create()->id, 'name' => 'Foreign group']);

        Livewire::test(CalendarEventEditor::class)
            ->assertSee('Current group')
            ->assertDontSee('Archived group')
            ->assertDontSee('Foreign group')
            ->set('title', 'Sneaky')
            ->set('sectionIds', [$foreign->id])
            ->call('save')
            ->assertHasErrors('sectionIds.0');

        $this->assertSame(0, CalendarEvent::inSchool()->count());
    }

    /**
     * A section is named "A", and every class has an A. The class has to come
     * with it on any screen that does not already say which class.
     */
    public function test_a_section_is_named_with_its_class(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => current_academic_year_id(),
            'name' => 'A',
            'label' => null,
        ]);
        $class = $section->academicLevel->name;

        Livewire::test(CalendarEventEditor::class)
            ->assertSee($class.' · A')
            ->set('title', 'Year meeting')
            ->set('type', CalendarEventType::ParentMeeting->value)
            ->set('sectionIds', [$section->id])
            ->call('save');

        $this->get(route('calendar-events.index'))
            ->assertOk()
            ->assertSee($class.' · A');
    }

    public function test_changing_a_day_replaces_who_it_is_for(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);
        $event = $this->event();
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => current_academic_year_id(),
        ]);

        $this->get(route('calendar-events.edit', $event))->assertOk()->assertSeeLivewire(CalendarEventEditor::class);

        Livewire::test(CalendarEventEditor::class, ['event' => $event])
            ->assertSet('startsAt', $event->starts_at->toDateString())
            ->set('title', 'A better title')
            ->set('type', CalendarEventType::Assembly->value)
            ->set('sectionIds', [$section->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('status-message', type: 'success', message: 'Saved.');

        $this->assertSame('A better title', $event->fresh()->title);
        $this->assertSame(1, $event->audiences()->count());
    }

    public function test_a_day_is_removed_from_the_calendar(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event', 'delete calendar event']);
        $event = $this->event();

        Livewire::test(CalendarEventEditor::class, ['event' => $event])
            ->call('remove')
            ->assertRedirect(route('calendar-events.index', ['month' => $event->starts_at->format('Y-m')]));

        $this->assertSame(0, CalendarEvent::inSchool()->count());
    }

    public function test_removing_and_publishing_need_their_own_permissions(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);
        $event = $this->event(['is_published' => false]);

        Livewire::test(CalendarEventEditor::class, ['event' => $event])
            ->assertDontSee('Publish')
            ->call('remove')
            ->assertForbidden();

        $this->assertSame(1, CalendarEvent::inSchool()->count());
    }

    public function test_a_person_who_may_only_read_gets_the_facts_not_the_form(): void
    {
        $this->authorized_user(['read calendar event']);
        $event = $this->event(['location' => null]);

        $this->get(route('calendar-events.edit', $event))
            ->assertOk()
            ->assertDontSeeLivewire(CalendarEventEditor::class)
            ->assertSee('The school is shut on this day.')
            ->assertSee('—');

        Livewire::test(CalendarEventEditor::class, ['event' => $event])->assertForbidden();
    }

    public function test_another_schools_day_cannot_be_opened_in_the_editor(): void
    {
        $this->authorized_user(['read calendar event', 'update calendar event', 'publish calendar event', 'delete calendar event']);
        $foreign = CalendarEvent::create([
            'school_id' => School::factory()->create()->id,
            'title' => 'Their sports day',
            'type' => CalendarEventType::Holiday,
            'is_published' => false,
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]);

        $this->get(route('calendar-events.edit', $foreign))->assertForbidden();
        Livewire::test(CalendarEventEditor::class, ['event' => $foreign])->assertForbidden();

        $this->assertFalse($foreign->fresh()->is_published);
    }

    public function test_publishing_needs_its_own_permission(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);
        $event = $this->event(['is_published' => false]);

        Livewire::test(CalendarEventEditor::class, ['event' => $event])
            ->call('publish')
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
        $person = $this->memberOf($this->workingSchool(), User::factory()->create(['name' => 'Zainab Parent']));

        Livewire::test(CalendarEventEditor::class)
            ->set('title', 'A meeting about one child')
            ->set('type', CalendarEventType::Appointment->value)
            ->set('isAllDay', false)
            ->set('personSearch', 'Zai')
            ->assertSee('Zainab Parent')
            ->call('addPerson', $person->id)
            ->assertSet('userIds', [$person->id])
            ->assertSet('personSearch', '')
            ->call('addPerson', $person->id)
            ->assertSet('userIds', [$person->id])
            ->call('save')
            ->assertHasNoErrors();

        $event = CalendarEvent::inSchool()->sole();

        $this->assertSame($person->id, $event->audiences()->sole()->user_id);
        $this->assertFalse($event->isForEverybody());
    }

    public function test_a_day_cannot_name_somebody_from_another_school(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);
        $stranger = $this->personOfAnotherSchool('Ben Elsewhere');

        Livewire::test(CalendarEventEditor::class)
            ->set('title', 'A meeting about one child')
            ->set('personSearch', 'Ben')
            ->assertDontSee('Ben Elsewhere')
            ->call('addPerson', $stranger->id)
            ->assertSet('userIds', []);

        $this->assertSame(0, CalendarEvent::inSchool()->count());
    }

    public function test_the_chosen_people_cannot_be_written_from_the_browser(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);
        $stranger = $this->personOfAnotherSchool('Ben Elsewhere');

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test(CalendarEventEditor::class)->set('userIds', [$stranger->id]);
    }

    public function test_the_editor_drops_a_person_who_has_left_the_school(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event', 'update calendar event']);
        $stayer = $this->memberOf($this->workingSchool(), User::factory()->create(['name' => 'Ada Stayer']));
        $leaver = $this->memberOf($this->workingSchool(), User::factory()->create(['name' => 'Ben Leaver']));
        $event = $this->event(['type' => CalendarEventType::Appointment]);
        $event->audiences()->createMany([['user_id' => $stayer->id], ['user_id' => $leaver->id]]);
        $leaver->schoolMemberships()->update(['status' => SchoolMembershipStatus::Ended]);

        Livewire::test(CalendarEventEditor::class, ['event' => $event->fresh()])
            ->assertSet('userIds', [$stayer->id])
            ->assertSee('Ada Stayer')
            ->assertDontSee('Ben Leaver')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame([$stayer->id], $event->audiences()->pluck('user_id')->filter()->values()->all());
    }

    public function test_the_form_offers_the_people_of_this_school_only(): void
    {
        $this->authorized_user(['read calendar event', 'create calendar event']);
        $this->memberOf($this->workingSchool(), User::factory()->create(['name' => 'Ada Colleague']));
        $this->personOfAnotherSchool('Ada Elsewhere');
        $left = $this->memberOf($this->workingSchool(), User::factory()->create(['name' => 'Ada Leaver']));
        $left->schoolMemberships()->update(['status' => SchoolMembershipStatus::Ended]);

        $this->get(route('calendar-events.create'))->assertOk()->assertDontSee('Ada Colleague');

        Livewire::test(CalendarEventEditor::class)
            ->set('personSearch', 'A')
            ->assertDontSee('Ada Colleague')
            ->set('personSearch', 'Ada')
            ->assertSee('Ada Colleague')
            ->assertDontSee('Ada Elsewhere')
            ->assertDontSee('Ada Leaver')
            ->set('personSearch', '100%')
            ->assertSee('Nobody in this school matches');
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
