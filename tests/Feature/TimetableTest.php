<?php

namespace Tests\Feature;

use App\Enums\AcademicPeriodStatus;
use App\Livewire\CreateTimetableForm;
use App\Models\AcademicPeriod;
use App\Models\CustomTimetableItem;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Models\Weekday;
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
