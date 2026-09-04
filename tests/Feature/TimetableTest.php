<?php

namespace Tests\Feature;

use App\Enums\AcademicPeriodStatus;
use App\Enums\RosterMode;
use App\Livewire\CreateTimetableForm;
use App\Livewire\ManageTimetable;
use App\Livewire\ShowTimetable;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\CourseOffering;
use App\Models\CustomTimetableItem;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Models\Weekday;
use App\Services\Timetable\TimetableGrid;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TimetableTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    // test unauthorized user can't view all timetables

    public function test_unauthorized_user_cant_view_all_timetables()
    {
        $this->unauthorized_user()
            ->get('/dashboard/timetables')
            ->assertForbidden();
    }

    // test authorized user can view all timetables

    public function test_authorized_user_can_view_all_timetables()
    {
        $this->authorized_user(['read timetable'])
            ->get('/dashboard/timetables')
            ->assertOk();
    }

    public function test_a_parent_cannot_open_the_staff_timetable_workspace(): void
    {
        $this->authorized_user(['read timetable']);
        auth()->user()->assignRole('parent');

        $this->get('/dashboard/timetables')->assertForbidden();
    }

    public function test_a_student_can_open_only_a_published_timetable_for_their_section(): void
    {
        $school = $this->workingSchool();
        $student = User::factory()->create();
        $student = $this->memberOf($school, $student);
        $student->assignRole('student');
        $student->givePermissionTo('read timetable');
        $enrollment = $student->studentRecords()->create([
            'school_id' => $school->id,
            'academic_cycle_section_id' => AcademicCycleSection::factory()->create(['school_id' => $school->id])->id,
            'admission_date' => now()->toDateString(),
            'status' => 'active',
            'is_primary' => true,
        ]);

        $ownTimetable = Timetable::factory()->create([
            'academic_cycle_section_id' => $enrollment->academic_cycle_section_id,
            'status' => TimetableStatus::Published,
            'published_at' => now(),
        ]);
        $draftTimetable = Timetable::factory()->create([
            'academic_cycle_section_id' => $enrollment->academic_cycle_section_id,
        ]);

        $this->actingAsMemberOf($school, $student)
            ->get(route('timetables.show', $ownTimetable))
            ->assertOk();

        $this->get(route('timetables.show', $draftTimetable))->assertForbidden();
    }

    // test unauthorized user can't view create timetable

    public function test_unauthorized_user_cant_view_create_timetable()
    {
        $this->unauthorized_user()
            ->get('/dashboard/timetables/create')
            ->assertForbidden();
    }

    // test authorized user can view create timetable

    public function test_user_can_view_create_timetable()
    {
        $this->authorized_user(['create timetable'])
            ->get('/dashboard/timetables/create')
            ->assertOk();
    }

    public function test_timetable_schedule_options_are_progressively_disclosed(): void
    {
        $this->authorized_user(['create timetable']);

        Livewire::test(CreateTimetableForm::class)
            ->assertSet('calendarView', 'month')
            ->assertSet('eventStep', 1)
            ->assertSet('showEventDialog', false)
            ->assertSee('What are you adding?')
            ->assertDontSee('On weekdays')
            ->call('openEventDialog')
            ->assertSet('showEventDialog', true)
            ->call('chooseEventType', 'subject')
            ->assertSet('eventStep', 2)
            ->assertSee('When does it happen?')
            ->assertSee('On these weekdays')
            ->call('continueEventSchedule')
            ->assertSet('eventStep', 3)
            ->assertSee('Add the details')
            ->call('closeEventDialog')
            ->assertSet('showEventDialog', false)
            ->call('openEventDialog', '2030-09-11')
            ->assertSet('calendarView', 'month')
            ->assertSet('newEvent.recurrence', 'one_time')
            ->assertSet('newEvent.occurs_on', '2030-09-11');
    }

    public function test_user_can_create_a_schoolwide_recurring_timetable_with_a_role_event(): void
    {
        $this->authorized_user(['create timetable', 'create schoolwide timetable']);
        $weekday = Weekday::query()->firstOrFail();

        Livewire::test(CreateTimetableForm::class)
            ->set('name', 'Staff duty timetable')
            ->set('scope', 'schoolwide')
            ->set('newEvent.weekday_id', $weekday->id)
            ->set('newEvent.type', 'role')
            ->set('newEvent.title', 'Morning duty')
            ->set('newEvent.audience_role', 'teacher')
            ->call('addEvent')
            ->call('save')
            ->assertHasNoErrors();

        $timetable = Timetable::query()->where('name', 'Staff duty timetable')->firstOrFail();

        $this->assertNull($timetable->academic_cycle_section_id);
        $this->assertDatabaseHas('timetable_time_slot_weekday', [
            'timetable_time_slot_weekdayable_type' => (new CustomTimetableItem)->getMorphClass(),
            'audience_role' => 'teacher',
            'weekday_id' => $weekday->id,
        ]);
    }

    public function test_a_timetable_creator_without_schoolwide_permission_is_kept_to_section_scope(): void
    {
        $this->authorized_user(['create timetable']);

        Livewire::test(CreateTimetableForm::class)
            ->set('scope', 'schoolwide')
            ->assertSet('scope', 'section');
    }

    public function test_schoolwide_creation_is_rejected_even_if_the_client_state_is_tampered_with(): void
    {
        $this->authorized_user(['create timetable']);

        $component = Livewire::test(CreateTimetableForm::class)
            ->set('canCreateSchoolwide', true)
            ->set('scope', 'schoolwide');

        $component->assertSet('scope', 'section')->assertHasErrors('scope');
        $this->assertDatabaseMissing('timetables', ['name' => 'Tampered schoolwide timetable']);
    }

    public function test_a_timetable_can_contain_a_one_date_event(): void
    {
        $this->authorized_user(['create timetable']);
        $period = AcademicPeriod::factory()->create([
            'academic_year_id' => current_academic_year_id(),
            'school_id' => current_school_id(),
            'starts_on' => '2030-09-01',
            'ends_on' => '2030-12-20',
        ]);

        Livewire::test(CreateTimetableForm::class)
            ->set('name', 'Sports day schedule')
            ->set('academicPeriodId', $period->id)
            ->set('newEvent.recurrence', 'one_time')
            ->set('newEvent.occurs_on', $period->starts_on?->toDateString())
            ->set('newEvent.type', 'freehand')
            ->set('newEvent.title', 'Sports day')
            ->call('addEvent')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('timetables', [
            'name' => 'Sports day schedule',
        ]);
        $this->assertDatabaseHas('timetable_time_slots', [
            'recurrence' => 'one_time',
            'occurs_on' => $period->starts_on?->toDateString(),
        ]);
    }

    public function test_a_timetable_can_mix_weekly_and_one_date_events(): void
    {
        $this->authorized_user(['create timetable']);
        $period = AcademicPeriod::factory()->create([
            'academic_year_id' => current_academic_year_id(),
            'school_id' => current_school_id(),
            'starts_on' => '2030-09-01',
            'ends_on' => '2030-12-20',
        ]);
        $weekday = Weekday::query()->firstOrFail();

        Livewire::test(CreateTimetableForm::class)
            ->set('name', 'Mixed calendar')
            ->set('academicPeriodId', $period->id)
            ->set('newEvent.weekday_id', $weekday->id)
            ->set('newEvent.type', 'freehand')
            ->set('newEvent.title', 'Weekly assembly')
            ->call('addEvent')
            ->set('newEvent.recurrence', 'one_time')
            ->set('newEvent.occurs_on', $period->starts_on?->toDateString())
            ->set('newEvent.type', 'freehand')
            ->set('newEvent.title', 'Sports day')
            ->call('addEvent')
            ->call('save')
            ->assertHasNoErrors();

        $timetable = Timetable::query()->where('name', 'Mixed calendar')->firstOrFail();

        $this->assertSame(2, $timetable->timeSlots()->count());
        $this->assertSame(1, $timetable->timeSlots()->where('recurrence', 'weekly')->count());
        $this->assertSame(1, $timetable->timeSlots()->where('recurrence', 'one_time')->count());
    }

    public function test_a_recurring_event_can_be_added_to_multiple_weekdays(): void
    {
        $this->authorized_user(['create timetable']);
        $weekdays = Weekday::query()->whereIn('name', ['Monday', 'Wednesday'])->get();

        Livewire::test(CreateTimetableForm::class)
            ->set('name', 'Multi-day timetable')
            ->set('newEvent.weekday_ids', $weekdays->pluck('id')->all())
            ->set('newEvent.type', 'freehand')
            ->set('newEvent.title', 'Morning registration')
            ->call('addEvent')
            ->call('save')
            ->assertHasNoErrors();

        $timetable = Timetable::query()->where('name', 'Multi-day timetable')->firstOrFail();

        $this->assertSame(1, $timetable->timeSlots()->count());
        $this->assertSame($weekdays->count(), $timetable->timeSlots()->firstOrFail()->weekdays()->count());
    }

    public function test_the_calendar_can_switch_between_week_and_month_views(): void
    {
        $this->authorized_user(['update timetable']);
        $timetable = Timetable::factory()->create();

        Livewire::test(ManageTimetable::class, ['timetable' => $timetable])
            ->assertSet('calendarView', 'month')
            ->assertSee('wire:click="previousCalendarPeriod"', false)
            ->assertSee('wire:click="nextCalendarPeriod"', false)
            ->assertSee('x-effect="open = $wire.showTimeSlotDialog"', false)
            ->assertDontSee('x-effect="show = $wire.showTimeSlotDialog"', false)
            ->call('openTimeSlotDialog', '2030-09-08')
            ->assertSet('showTimeSlotDialog', true)
            ->assertSet('calendarView', 'month')
            ->assertSet('slotRecurrence', 'one_time')
            ->assertSet('slotOccursOn', '2030-09-08')
            ->call('closeTimeSlotDialog')
            ->assertSet('showTimeSlotDialog', false)
            ->call('setCalendarView', 'month')
            ->assertSet('calendarView', 'month')
            ->call('setCalendarView', 'day')
            ->assertSet('calendarView', 'day')
            ->assertSee('Add slot for this date')
            ->call('chooseCalendarDate', '2030-09-08')
            ->assertSet('calendarDate', '2030-09-08')
            ->assertSet('calendarView', 'day')
            ->call('setCalendarView', 'week')
            ->assertSee('Add recurring slot');
    }

    public function test_month_calendar_navigation_moves_across_month_boundaries(): void
    {
        $this->authorized_user(['update timetable']);
        $timetable = Timetable::factory()->create();

        Livewire::test(ManageTimetable::class, ['timetable' => $timetable])
            ->call('chooseCalendarDate', '2030-01-31')
            ->call('nextCalendarPeriod')
            ->assertSet('calendarDate', '2030-02-28')
            ->call('nextCalendarPeriod')
            ->assertSet('calendarDate', '2030-03-28')
            ->call('previousCalendarPeriod')
            ->assertSet('calendarDate', '2030-02-28');
    }

    public function test_read_only_timetable_can_switch_between_calendar_views(): void
    {
        $this->authorized_user(['read timetable']);
        $timetable = Timetable::factory()->create();

        Livewire::test(ShowTimetable::class, ['timetable' => $timetable])
            ->assertSet('calendarView', 'month')
            ->assertSee('Calendar view')
            ->call('setCalendarView', 'week')
            ->assertSet('calendarView', 'week')
            ->assertSee('No events')
            ->call('setCalendarView', 'day')
            ->assertSet('calendarView', 'day')
            ->assertSee('No events scheduled for this date.')
            ->call('chooseCalendarDate', '2030-09-08')
            ->assertSet('calendarDate', '2030-09-08')
            ->call('nextCalendarPeriod')
            ->assertSet('calendarDate', '2030-09-09')
            ->call('previousCalendarPeriod')
            ->assertSet('calendarDate', '2030-09-08');
    }

    public function test_read_only_calendar_plots_events_in_month_week_and_day_views(): void
    {
        $this->authorized_user(['read timetable']);
        $timetable = Timetable::factory()->create();
        $timetable->academicPeriod()->update([
            'starts_on' => '2030-09-01',
            'ends_on' => '2030-12-20',
        ]);
        $timetable->refresh();
        $tuesday = Weekday::query()->where('name', 'Tuesday')->firstOrFail();
        $slot = TimetableTimeSlot::create([
            'timetable_id' => $timetable->id,
            'start_time' => '08:00',
            'stop_time' => '09:00',
            'recurrence' => 'weekly',
            'starts_on' => '2030-09-01',
            'recurrence_interval' => 1,
            'recurrence_weekdays' => [$tuesday->id],
        ]);
        $item = CustomTimetableItem::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Assembly',
        ]);
        TimetableRecord::create([
            'timetable_time_slot_id' => $slot->id,
            'weekday_id' => $tuesday->id,
            'timetable_time_slot_weekdayable_id' => $item->id,
            'timetable_time_slot_weekdayable_type' => $item->getMorphClass(),
        ]);

        $component = Livewire::test(ShowTimetable::class, ['timetable' => $timetable])
            ->assertSee('Assembly')
            ->assertSee('08:00–09:00');

        $component->call('chooseCalendarDate', '2030-09-03')
            ->call('setCalendarView', 'week')
            ->assertSee('Assembly');
        $component->call('setCalendarView', 'day')
            ->call('chooseCalendarDate', '2030-09-03')
            ->assertSee('Assembly')
            ->assertSee('08:00–09:00');
    }

    public function test_calendar_views_plot_active_empty_time_slots(): void
    {
        $this->authorized_user(['update timetable']);
        $timetable = Timetable::factory()->create();
        $timetable->academicPeriod()->update([
            'starts_on' => '2030-09-01',
            'ends_on' => '2030-12-20',
        ]);
        $timetable->refresh();
        $tuesday = Weekday::query()->where('name', 'Tuesday')->firstOrFail();

        $slot = TimetableTimeSlot::factory()->create([
            'timetable_id' => $timetable->id,
            'start_time' => '01:00',
            'stop_time' => '02:00',
            'recurrence' => 'weekly',
            'starts_on' => '2030-09-01',
            'recurrence_interval' => 1,
            'recurrence_weekdays' => [$tuesday->id],
        ]);

        Livewire::test(ManageTimetable::class, ['timetable' => $timetable])
            ->assertSee('Open time slot')
            ->call('setCalendarView', 'day')
            ->call('chooseCalendarDate', '2030-09-03')
            ->assertSee('Open time slot')
            ->call('moveCalendar', 1)
            ->assertSet('calendarDate', '2030-09-04')
            ->call('setCalendarView', 'week')
            ->assertSee('01:00')
            ->assertSee('Open time slot')
            ->assertSet('showSlotEditorDialog', false)
            ->call('selectCell', $slot->id, $tuesday->id)
            ->assertSet('showSlotEditorDialog', true)
            ->assertSee('x-effect="open = $wire.showSlotEditorDialog"', false)
            ->assertSee('Find a subject or a break')
            ->call('closeSlotEditorDialog')
            ->assertSet('showSlotEditorDialog', false)
            ->call('selectCell', $slot->id, $tuesday->id)
            ->assertSet('showSlotEditorDialog', true);
    }

    public function test_a_teacher_only_sees_subjects_assigned_to_them_in_a_read_only_timetable(): void
    {
        $this->authorized_user(['read timetable']);
        $school = $this->workingSchool();
        $teacher = $this->memberOf($school);
        school_context()->set($school, remember: false);
        $teacher->assignRole('teacher');
        $teacher->givePermissionTo('read timetable');

        $timetable = Timetable::factory()->create();
        $timetable->academicPeriod()->update([
            'starts_on' => now()->subDay()->toDateString(),
            'ends_on' => now()->addYear()->toDateString(),
        ]);
        $section = $timetable->academicCycleSection;
        $assignedSlot = TimetableTimeSlot::create([
            'timetable_id' => $timetable->id,
            'start_time' => '08:00',
            'stop_time' => '09:00',
        ]);
        $otherSlot = TimetableTimeSlot::create([
            'timetable_id' => $timetable->id,
            'start_time' => '09:00',
            'stop_time' => '10:00',
        ]);
        $weekday = Weekday::firstOrFail();
        $assignedSubject = Subject::factory()->create(['school_id' => $school->id, 'name' => 'Assigned subject']);
        $otherSubject = Subject::factory()->create(['school_id' => $school->id, 'name' => 'Other subject']);

        $offering = CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $section->academic_year_id,
            'academic_period_id' => $timetable->academic_period_id,
            'academic_level_id' => $section->academic_level_id,
            'subject_id' => $assignedSubject->id,
            'roster_mode' => RosterMode::HomeSection,
        ]);
        $offering->cycleSections()->attach($section);
        TeachingAssignment::create([
            'school_id' => $school->id,
            'subject_id' => $assignedSubject->id,
            'user_id' => $teacher->id,
            'academic_year_id' => $section->academic_year_id,
            'academic_period_id' => $timetable->academic_period_id,
            'course_offering_id' => $offering->id,
            'starts_on' => now()->toDateString(),
        ]);

        TimetableRecord::create([
            'timetable_time_slot_id' => $assignedSlot->id,
            'weekday_id' => $weekday->id,
            'timetable_time_slot_weekdayable_id' => $assignedSubject->id,
            'timetable_time_slot_weekdayable_type' => $assignedSubject->getMorphClass(),
        ]);
        TimetableRecord::create([
            'timetable_time_slot_id' => $otherSlot->id,
            'weekday_id' => $weekday->id,
            'timetable_time_slot_weekdayable_id' => $otherSubject->id,
            'timetable_time_slot_weekdayable_type' => $otherSubject->getMorphClass(),
        ]);

        $this->actingAsMemberOf($school, $teacher);

        $grid = app(TimetableGrid::class)->of($timetable, viewer: $teacher);
        $assignedCell = $grid['rows'][0]['cells'][$weekday->id];
        $otherCell = $grid['rows'][1]['cells'][$weekday->id];

        $this->assertSame('Assigned subject', $assignedCell['name']);
        $this->assertNull($otherCell['name']);

        $show = Livewire::test(ShowTimetable::class, ['timetable' => $timetable]);
        $show
            ->assertSee('Showing subjects assigned to you')
            ->assertSee('Assigned subject')
            ->assertDontSee('Other subject');
    }

    public function test_a_student_only_sees_subjects_allowed_by_their_roster(): void
    {
        $this->authorized_user(['read timetable']);
        $school = $this->workingSchool();
        $timetable = Timetable::factory()->create();
        $section = $timetable->academicCycleSection;
        $student = User::factory()->create();
        $student = $this->memberOf($school, $student);
        school_context()->set($school, remember: false);
        $studentRecord = $student->studentRecords()->create([
            'school_id' => $school->id,
            'academic_cycle_section_id' => $section->id,
            'admission_date' => now()->toDateString(),
            'status' => 'active',
            'is_primary' => true,
        ]);
        $student->assignRole('student');
        $student->givePermissionTo('read timetable');
        $takenSlot = TimetableTimeSlot::create([
            'timetable_id' => $timetable->id,
            'start_time' => '08:00',
            'stop_time' => '09:00',
        ]);
        $notTakenSlot = TimetableTimeSlot::create([
            'timetable_id' => $timetable->id,
            'start_time' => '09:00',
            'stop_time' => '10:00',
        ]);
        $weekday = Weekday::firstOrFail();
        $taken = Subject::factory()->create(['school_id' => $school->id, 'name' => 'Taken subject']);
        $notTaken = Subject::factory()->create(['school_id' => $school->id, 'name' => 'Not taken subject']);

        $takenOffering = CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $section->academic_year_id,
            'academic_period_id' => $timetable->academic_period_id,
            'academic_level_id' => $section->academic_level_id,
            'subject_id' => $taken->id,
            'roster_mode' => RosterMode::IndividualRoster,
        ]);
        $takenOffering->studentRecords()->attach($studentRecord);
        CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $section->academic_year_id,
            'academic_period_id' => $timetable->academic_period_id,
            'academic_level_id' => $section->academic_level_id,
            'subject_id' => $notTaken->id,
            'roster_mode' => RosterMode::IndividualRoster,
        ]);

        TimetableRecord::create([
            'timetable_time_slot_id' => $takenSlot->id,
            'weekday_id' => $weekday->id,
            'timetable_time_slot_weekdayable_id' => $taken->id,
            'timetable_time_slot_weekdayable_type' => $taken->getMorphClass(),
        ]);
        TimetableRecord::create([
            'timetable_time_slot_id' => $notTakenSlot->id,
            'weekday_id' => $weekday->id,
            'timetable_time_slot_weekdayable_id' => $notTaken->id,
            'timetable_time_slot_weekdayable_type' => $notTaken->getMorphClass(),
        ]);

        $this->actingAsMemberOf($school, $student);

        $grid = app(TimetableGrid::class)->of($timetable, viewer: $student);
        $takenCell = $grid['rows'][0]['cells'][$weekday->id];
        $notTakenCell = $grid['rows'][1]['cells'][$weekday->id];

        $this->assertSame('Taken subject', $takenCell['name']);
        $this->assertNull($notTakenCell['name']);
    }

    public function test_a_time_slot_can_use_an_explicit_term_scoped_weekly_rule(): void
    {
        $this->authorized_user(['update timetable']);
        $timetable = Timetable::factory()->create();
        $timetable->academicPeriod()->update([
            'starts_on' => '2030-09-01',
            'ends_on' => '2030-12-20',
        ]);
        $timetable->refresh();
        $tuesday = Weekday::query()->where('name', 'Tuesday')->firstOrFail();

        Livewire::test(ManageTimetable::class, ['timetable' => $timetable])
            ->assertSee('Add a time slot')
            ->assertSee('Change schedule')
            ->assertDontSee('On these weekdays')
            ->set('startTime', '01:00')
            ->set('stopTime', '02:00')
            ->set('slotRecurrence', 'weekly')
            ->set('slotRecurrenceInterval', 2)
            ->set('slotStartsOn', '2030-09-24')
            ->set('slotWeekdayIds', [$tuesday->id])
            ->call('addTimeSlot')
            ->assertHasNoErrors()
            ->assertSee('01:00')
            ->assertSee('Every 2 weeks on Tuesday');

        $slot = $timetable->timeSlots()->firstOrFail();

        $this->assertSame('2030-09-24', $slot->starts_on?->toDateString());
        $this->assertSame(2, $slot->recurrence_interval);
        $this->assertSame([$tuesday->id], $slot->recurrence_weekdays);
        $this->assertTrue($slot->occursOn('2030-09-24', $tuesday->id));
        $this->assertFalse($slot->occursOn('2030-10-01', $tuesday->id));
        $this->assertTrue($slot->occursOn('2030-10-08', $tuesday->id));
    }

    public function test_a_time_slot_can_repeat_monthly_from_a_specific_day(): void
    {
        $this->authorized_user(['update timetable']);
        $timetable = Timetable::factory()->create();
        $timetable->academicPeriod()->update([
            'starts_on' => '2030-09-01',
            'ends_on' => '2030-12-20',
        ]);
        $timetable->refresh();

        Livewire::test(ManageTimetable::class, ['timetable' => $timetable])
            ->set('startTime', '01:00')
            ->set('stopTime', '02:00')
            ->set('slotRecurrence', 'monthly')
            ->set('slotStartsOn', '2030-09-24')
            ->call('addTimeSlot')
            ->assertHasNoErrors();

        $slot = $timetable->timeSlots()->firstOrFail();

        $this->assertTrue($slot->occursOn('2030-10-24'));
        $this->assertFalse($slot->occursOn('2030-10-23'));
    }

    // test unauthorized user can't view edit timetable

    public function test_unauthorized_user_cant_view_edit_timetable()
    {
        $this->unauthorized_user()
            ->get('/dashboard/timetables/1/edit')
            ->assertForbidden();
    }

    // test authorized user can view edit timetable

    public function test_user_can_view_edit_timetable()
    {
        $this->authorized_user(['update timetable'])
            ->get('/dashboard/timetables/1/edit')
            ->assertOk();
    }

    // test unauthorized user can't update timetable

    public function test_unauthorized_user_cant_update_timetable()
    {
        $this->unauthorized_user()
            ->patch('/dashboard/timetables/1', [
                'name' => 'Test timetable',
                'description' => 'Test timetable description',
            ])->assertForbidden();
    }

    // test authorized user can update timetable

    public function test_user_can_update_timetable()
    {
        $timetable = Timetable::factory()->create();

        $this->authorized_user(['update timetable'])
            ->patch("/dashboard/timetables/$timetable->id", [
                'name' => 'Test timetable',
                'my_class_id' => 1,
                'description' => 'Test timetable description',
            ]);

        $this->assertDatabaseHas('timetables', [
            'id' => $timetable->id,
            'name' => 'Test timetable',
            'academic_cycle_section_id' => $timetable->academic_cycle_section_id,
            'description' => 'Test timetable description',
        ]);
    }

    // test unauthorized user can't delete timetable

    public function test_unauthorized_user_cant_delete_timetable()
    {
        $this->unauthorized_user()
            ->delete('/dashboard/timetables/1')
            ->assertForbidden();
    }

    // test authorized user can delete timetable

    public function test_user_can_delete_timetable()
    {
        $timetable = Timetable::factory()->create();

        $this->authorized_user(['delete timetable'])
            ->delete("/dashboard/timetables/$timetable->id");

        $this->assertDatabaseMissing('timetables', [
            'id' => $timetable->id,
            'name' => $timetable->name,
            'academic_cycle_section_id' => $timetable->academic_cycle_section_id,
            'description' => $timetable->description,
        ]);
    }

    // test unauthorized user can view manage timetable

    public function test_unauthorized_user_cant_view_manage_timetable()
    {
        $this->unauthorized_user()
            ->get('/dashboard/timetables/1/manage')
            ->assertForbidden();
    }

    // test authorized user can view manage timetable

    public function test_authorized_user_can_view_manage_timetable()
    {
        $this->authorized_user(['update timetable'])
            ->get('/dashboard/timetables/1/manage')
            ->assertOk();
    }

    public function test_authorized_user_can_manage_a_draft_for_a_scheduled_period(): void
    {
        $timetable = Timetable::factory()->create();
        $timetable->academicPeriod()->update(['status' => AcademicPeriodStatus::Scheduled]);

        $this->authorized_user(['update timetable'])
            ->get(route('timetables.manage', $timetable))
            ->assertOk();
    }

    // test unauthorized user cannot store timetable time slot

    public function test_unauthorized_user_cant_store_timetable_time_slot()
    {
        $this->unauthorized_user()
            ->post('/dashboard/timetables/manage/time-slots', [
                'start_time' => '10:00',
                'stop_time' => '11:00',
            ])->assertForbidden();
    }

    // test authorized user can store timetable time slot

    public function test_authorized_user_can_store_timetable_time_slot()
    {
        $timetable = Timetable::factory()->create();

        $this->authorized_user(['update timetable'])
            ->post('/dashboard/timetables/manage/time-slots', [
                'start_time' => '10:00',
                'stop_time' => '11:00',
                'timetable_id' => $timetable->id,
            ]);

        $this->assertDatabaseHas('timetable_time_slots', [
            'timetable_id' => $timetable->id,
            'start_time' => '10:00:00',
            'stop_time' => '11:00:00',
        ]);
    }

    // test unatuorized user cannot delete timetable time slot

    public function test_unauthorized_user_cant_delete_timetable_time_slot()
    {
        $timeslot = TimetableTimeSlot::factory()->create();
        $this->unauthorized_user()
            ->delete("/dashboard/timetables/manage/time-slots/$timeslot->id")
            ->assertForbidden();
    }

    // test authorized user can delete timetable time slot

    public function test_authorized_user_can_delete_timetable_time_slot()
    {
        $timeslot = TimetableTimeSlot::factory()->create();
        $this->authorized_user(['update timetable'])
            ->delete("/dashboard/timetables/manage/time-slots/$timeslot->id");

        $this->assertDatabaseMissing('timetable_time_slots', [
            'id' => $timeslot->id,
            'timetable_id' => $timeslot->timetable_id,
            'start_time' => "$timeslot->start_time:00",
            'stop_time' => "$timeslot->stop_time:00",
        ]);
    }

    // test unauthorized user cannot create timetable record

    public function test_unauthorized_user_cannot_create_timetable_record()
    {
        $timeslot = TimetableTimeSlot::factory()->create();
        $this->unauthorized_user()
            ->post("/dashboard/timetables/manage/time-slots/$timeslot->id/record/create", [
                'type' => 'subject',
                'weekday_id' => '1',
                'id' => 1,
            ])->assertForbidden();
    }

    // test authorized user can create timetable record

    public function test_authorized_user_can_create_timetable_record()
    {
        $timeslot = TimetableTimeSlot::factory()->create();
        $this->authorized_user(['update timetable'])
            ->post("/dashboard/timetables/manage/time-slots/$timeslot->id/record/create", [
                'type' => 'subject',
                'weekday_id' => '1',
                'id' => '1',
            ])->assertRedirect();

        $this->assertDatabaseHas('timetable_time_slot_weekday', [
            'timetable_time_slot_id' => $timeslot->id,
            'weekday_id' => 1,
            'timetable_time_slot_weekdayable_id' => 1,
        ]);
    }
}
