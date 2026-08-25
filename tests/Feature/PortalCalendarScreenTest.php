<?php

namespace Tests\Feature;

use App\Enums\CalendarEventType;
use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Models\AcademicCycleSection;
use App\Models\CalendarEvent;
use App\Models\CalendarEventAudience;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A family reads the calendar the school published, and only the days that
 * reach their child.
 */
class PortalCalendarScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_family_reads_a_day_the_whole_school_shares(): void
    {
        $enrollment = $this->enrollment();
        $this->event(['title' => 'Founders day']);

        $this->actingAs($this->guardianOf($enrollment))
            ->get(route('portal.calendar.index', $enrollment))
            ->assertOk()
            ->assertSee('Founders day')
            ->assertSee(now()->format('F Y'));
    }

    public function test_the_family_reads_which_days_the_school_is_shut(): void
    {
        $enrollment = $this->enrollment();
        $this->event(['title' => 'Mid-term break', 'type' => CalendarEventType::Holiday]);

        $this->actingAs($this->guardianOf($enrollment))
            ->get(route('portal.calendar.index', $enrollment))
            ->assertOk()
            ->assertSee('The school is shut')
            ->assertDontSee('The school is open every day this month.');
    }

    public function test_a_draft_never_reaches_a_family(): void
    {
        $enrollment = $this->enrollment();
        $this->event(['title' => 'Not decided yet', 'is_published' => false]);

        $this->actingAs($this->guardianOf($enrollment))
            ->get(route('portal.calendar.index', $enrollment))
            ->assertOk()
            ->assertDontSee('Not decided yet')
            ->assertSee('Nothing is on this month');
    }

    public function test_a_day_for_another_home_group_stays_out_of_reach(): void
    {
        $ownGroup = $this->section();
        $otherGroup = $this->section();
        $enrollment = $this->enrollment(['academic_cycle_section_id' => $ownGroup->id]);

        $this->eventFor($this->event(['title' => 'Our year meeting']), ['academic_cycle_section_id' => $ownGroup->id]);
        $this->eventFor($this->event(['title' => 'Their year meeting']), ['academic_cycle_section_id' => $otherGroup->id]);

        $this->actingAs($this->guardianOf($enrollment))
            ->get(route('portal.calendar.index', $enrollment))
            ->assertOk()
            ->assertSee('Our year meeting')
            ->assertDontSee('Their year meeting');
    }

    public function test_a_day_that_names_the_child_reaches_the_family(): void
    {
        $enrollment = $this->enrollment();
        $this->eventFor($this->event(['title' => 'A meeting about your child']), ['user_id' => $enrollment->user_id]);
        $this->eventFor($this->event(['title' => 'A meeting about another child']), ['user_id' => $this->enrollment()->user_id]);

        $this->actingAs($this->guardianOf($enrollment))
            ->get(route('portal.calendar.index', $enrollment))
            ->assertOk()
            ->assertSee('A meeting about your child')
            ->assertDontSee('A meeting about another child');
    }

    public function test_an_appointment_offers_its_published_time_as_a_portal_request(): void
    {
        $enrollment = $this->enrollment();
        $appointment = $this->event([
            'title' => 'Guardian meeting',
            'type' => CalendarEventType::Appointment,
            'is_all_day' => false,
            'starts_at' => now()->addDay()->setTime(14, 0),
            'ends_at' => now()->addDay()->setTime(14, 30),
        ]);
        $this->eventFor($appointment, ['user_id' => $enrollment->user_id]);

        $this->actingAs($this->guardianOf($enrollment))
            ->get(route('portal.calendar.index', $enrollment))
            ->assertOk()
            ->assertSee('Request this time');

    }

    public function test_a_child_reads_their_own_calendar(): void
    {
        $enrollment = $this->enrollment();
        $this->event(['title' => 'Founders day']);

        $this->actingAs($enrollment->user)
            ->get(route('portal.calendar.index', $enrollment))
            ->assertOk()
            ->assertSee('Founders day');
    }

    public function test_the_month_can_be_stepped_through(): void
    {
        $enrollment = $this->enrollment();
        $nextMonth = now()->addMonthNoOverflow()->startOfMonth();
        $this->event([
            'title' => 'Next month event',
            'starts_at' => $nextMonth->copy()->startOfDay(),
            'ends_at' => $nextMonth->copy()->endOfDay(),
        ]);

        $guardian = $this->guardianOf($enrollment);

        $this->actingAs($guardian)
            ->get(route('portal.calendar.index', $enrollment))
            ->assertOk()
            ->assertDontSee('Next month event');

        $this->actingAs($guardian)
            ->get(route('portal.calendar.index', [$enrollment, 'month' => $nextMonth->format('Y-m')]))
            ->assertOk()
            ->assertSee('Next month event')
            ->assertSee($nextMonth->format('F Y'));
    }

    public function test_a_bad_month_falls_back_to_this_one(): void
    {
        $enrollment = $this->enrollment();

        $this->actingAs($this->guardianOf($enrollment))
            ->get(route('portal.calendar.index', [$enrollment, 'month' => 'not-a-month']))
            ->assertOk()
            ->assertSee(now()->format('F Y'));
    }

    public function test_the_calendar_of_another_school_stays_out_of_reach(): void
    {
        $enrollment = $this->enrollment();
        $otherSchool = School::factory()->create();
        $this->event(['title' => 'Another school day', 'school_id' => $otherSchool->id]);

        $this->actingAs($this->guardianOf($enrollment))
            ->get(route('portal.calendar.index', $enrollment))
            ->assertOk()
            ->assertDontSee('Another school day');
    }

    public function test_a_stranger_never_opens_the_calendar(): void
    {
        $enrollment = $this->enrollment();

        $this->actingAs($this->memberOf($this->workingSchool()))
            ->get(route('portal.calendar.index', $enrollment))
            ->assertForbidden();
    }

    public function test_a_closed_calendar_area_has_no_screen(): void
    {
        $enrollment = $this->enrollment();
        features()->enable(Feature::Portal, config: [PortalArea::Calendar->value => false]);

        $this->actingAs($this->guardianOf($enrollment))
            ->get(route('portal.calendar.index', $enrollment))
            ->assertNotFound();
    }

    public function test_a_school_that_turned_events_off_has_no_family_screen(): void
    {
        $enrollment = $this->enrollment();
        app(FeatureManager::class)->disable(Feature::Events);

        $this->actingAs($this->guardianOf($enrollment))
            ->get(route('portal.calendar.index', $enrollment))
            ->assertNotFound();
    }

    public function test_the_family_is_offered_a_way_in(): void
    {
        $enrollment = $this->enrollment();

        $this->actingAs($this->guardianOf($enrollment))
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('portal.calendar.index', $enrollment), escape: false);
    }

    /**
     * Make an enrollment in the working school.
     *
     * @param  array<string, mixed>  $values
     */
    private function enrollment(array $values = []): StudentRecord
    {
        return StudentRecord::factory()->create([
            'school_id' => $this->workingSchool()->id,
            ...$values,
        ]);
    }

    /**
     * Make a home group in the working school.
     */
    private function section(): AcademicCycleSection
    {
        return AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => current_academic_year_id(),
        ]);
    }

    /**
     * Make a guardian recorded against the student.
     */
    private function guardianOf(StudentRecord $enrollment): User
    {
        $guardian = $this->memberOf($this->workingSchool());
        $guardian->parentRecord()->create(['user_id' => $guardian->id]);
        $guardian->refresh()->parentRecord->students()->syncWithoutDetaching($enrollment->user);

        return $guardian->fresh();
    }

    /**
     * Put one published day on the calendar of the working school.
     *
     * @param  array<string, mixed>  $values
     */
    private function event(array $values = []): CalendarEvent
    {
        return CalendarEvent::create([
            'school_id' => $this->workingSchool()->id,
            'title' => 'Founders day',
            'type' => CalendarEventType::Assembly,
            'is_published' => true,
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
            ...$values,
        ]);
    }

    /**
     * Name who one day is for.
     *
     * @param  array<string, mixed>  $values
     */
    private function eventFor(CalendarEvent $event, array $values): CalendarEventAudience
    {
        return CalendarEventAudience::create(['calendar_event_id' => $event->id, ...$values]);
    }
}
