<?php

namespace Tests\Feature;

use App\Enums\CalendarEventType;
use App\Models\AcademicCycleSection;
use App\Models\CalendarEvent;
use App\Models\CalendarEventAudience;
use App\Models\School;
use App\Models\StudentRecord;
use App\Services\Calendar\SchoolCalendar;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The calendar says what is on and whether the school is open.
 */
class SchoolCalendarTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_event_starts_unpublished_and_for_everybody(): void
    {
        $this->authorized_user([]);

        $event = $this->event(['title' => 'Sports day']);

        $this->assertFalse($event->is_published);
        $this->assertTrue($event->isForEverybody());
        $this->assertSame(CalendarEventType::Other, $event->type);
    }

    public function test_only_published_events_are_read(): void
    {
        $this->authorized_user([]);
        $this->event(['title' => 'Draft plan']);
        $this->event(['title' => 'Sports day', 'is_published' => true]);

        $events = app(SchoolCalendar::class)->between(now()->subWeek(), now()->addWeek());

        $this->assertSame(1, $events->count());
        $this->assertSame('Sports day', $events->first()->title);
    }

    public function test_the_calendar_never_shows_another_school(): void
    {
        $this->authorized_user([]);
        $this->event(['title' => 'Ours', 'is_published' => true]);
        $this->event(['title' => 'Theirs', 'is_published' => true, 'school_id' => School::factory()->create()->id]);

        $events = app(SchoolCalendar::class)->between(now()->subWeek(), now()->addWeek());

        $this->assertSame(['Ours'], $events->pluck('title')->all());
    }

    public function test_a_holiday_makes_it_a_day_off(): void
    {
        $this->authorized_user([]);
        $calendar = app(SchoolCalendar::class);

        $this->assertTrue($calendar->isTeachingDay());

        $this->event([
            'title' => 'Founders day',
            'type' => CalendarEventType::Holiday,
            'is_published' => true,
        ]);

        $this->assertFalse($calendar->isTeachingDay());
    }

    public function test_an_assembly_leaves_it_a_teaching_day(): void
    {
        $this->authorized_user([]);
        $this->event(['type' => CalendarEventType::Assembly, 'is_published' => true]);

        $this->assertTrue(app(SchoolCalendar::class)->isTeachingDay());
    }

    public function test_an_unpublished_holiday_does_not_close_the_school(): void
    {
        $this->authorized_user([]);
        $this->event(['type' => CalendarEventType::Holiday]);

        $this->assertTrue(app(SchoolCalendar::class)->isTeachingDay());
    }

    public function test_the_closures_of_a_term_are_listed(): void
    {
        $this->authorized_user([]);
        $this->event(['title' => 'Founders day', 'type' => CalendarEventType::Holiday, 'is_published' => true]);
        $this->event([
            'title' => 'Storm closure',
            'type' => CalendarEventType::Closure,
            'is_published' => true,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3),
        ]);
        $this->event(['title' => 'Club', 'type' => CalendarEventType::Activity, 'is_published' => true]);

        $closures = app(SchoolCalendar::class)->closures(now()->subWeek(), now()->addWeek());

        $this->assertSame(['Founders day', 'Storm closure'], $closures->pluck('title')->all());
    }

    public function test_an_event_can_be_for_one_cycle_section_only(): void
    {
        $this->authorized_user([]);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $forEverybody = $this->event(['title' => 'Sports day', 'is_published' => true]);
        $forTheCycleSection = $this->event(['title' => 'Class trip', 'is_published' => true]);
        CalendarEventAudience::create([
            'calendar_event_id' => $forTheCycleSection->id,
            'academic_cycle_section_id' => $enrollment->academic_cycle_section_id,
        ]);
        $otherCycleSection = AcademicCycleSection::factory()->create(['school_id' => $this->workingSchool()->id]);
        $forAnotherCycleSection = $this->event(['title' => 'Other trip', 'is_published' => true]);
        CalendarEventAudience::create([
            'calendar_event_id' => $forAnotherCycleSection->id,
            'academic_cycle_section_id' => $otherCycleSection->id,
        ]);

        $events = app(SchoolCalendar::class)->between(now()->subWeek(), now()->addWeek(), $enrollment->user);

        $this->assertSame(['Class trip', 'Sports day'], $events->pluck('title')->sort()->values()->all());
    }

    public function test_an_event_can_be_for_one_person(): void
    {
        $this->authorized_user([]);
        $person = $this->memberOf($this->workingSchool());
        $meeting = $this->event(['title' => 'Guardian meeting', 'type' => CalendarEventType::ParentMeeting, 'is_published' => true]);
        CalendarEventAudience::create(['calendar_event_id' => $meeting->id, 'user_id' => $person->id]);

        $other = $this->memberOf($this->workingSchool());

        $this->assertTrue(app(SchoolCalendar::class)->between(now()->subDay(), now()->addDay(), $person)->contains('id', $meeting->id));
        $this->assertFalse(app(SchoolCalendar::class)->between(now()->subDay(), now()->addDay(), $other)->contains('id', $meeting->id));
    }

    /**
     * Create an event in the working school.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function event(array $attributes = []): CalendarEvent
    {
        return CalendarEvent::create($attributes + [
            'school_id' => $this->workingSchool()->id,
            'title' => 'School event',
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]);
    }
}
