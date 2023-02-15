<?php

namespace Tests\Feature;

use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimetableTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;

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

    //test unauthorized user can't view create timetable

    public function test_unauthorized_user_cant_view_create_timetable()
    {
        $this->unauthorized_user()
            ->get('/dashboard/timetables/create')
            ->assertForbidden();
    }

    //test authorized user can view create timetable

    public function test_user_can_view_create_timetable()
    {
        $this->authorized_user(['create timetable'])
            ->get('/dashboard/timetables/create')
            ->assertOk();
    }

    //test unauthorized user can't view edit timetable

    public function test_unauthorized_user_cant_view_edit_timetable()
    {
        $this->unauthorized_user()
            ->get('/dashboard/timetables/1/edit')
            ->assertForbidden();
    }

    //test authorized user can view edit timetable

    public function test_user_can_view_edit_timetable()
    {
        $this->authorized_user(['update timetable'])
            ->get('/dashboard/timetables/1/edit')
            ->assertOk();
    }

    //test unauthorized user can't update timetable

    public function test_unauthorized_user_cant_update_timetable()
    {
        $this->unauthorized_user()
            ->patch('/dashboard/timetables/1', [
                'name'        => 'Test timetable',
                'my_class_id' => 1,
                'description' => 'Test timetable description',
            ])->assertForbidden();
    }

    //test authorized user can update timetable

    public function test_user_can_update_timetable()
    {
        $timetable = Timetable::factory()->create();

        $this->authorized_user(['update timetable'])
            ->patch("/dashboard/timetables/$timetable->id", [
                'name'        => 'Test timetable',
                'my_class_id' => 1,
                'description' => 'Test timetable description',
            ]);

        $this->assertDatabaseHas('timetables', [
            'id'          => $timetable->id,
            'name'        => 'Test timetable',
            'my_class_id' => 1,
            'description' => 'Test timetable description',
        ]);
    }

    //test unauthorized user can't delete timetable

    public function test_unauthorized_user_cant_delete_timetable()
    {
        $this->unauthorized_user()
            ->delete('/dashboard/timetables/1')
            ->assertForbidden();
    }

    //test authorized user can delete timetable

    public function test_user_can_delete_timetable()
    {
        $timetable = Timetable::factory()->create();

        $this->authorized_user(['delete timetable'])
            ->delete("/dashboard/timetables/$timetable->id");

        $this->assertDatabaseMissing('timetables', [
            'id'          => $timetable->id,
            'name'        => $timetable->name,
            'my_class_id' => $timetable->my_class_id,
            'description' => $timetable->description,
        ]);
    }

    //test unauthorized user can view manage timetable

    public function test_unauthorized_user_cant_view_manage_timetable()
    {
        $this->unauthorized_user()
            ->get('/dashboard/timetables/1/manage')
            ->assertForbidden();
    }

    //test authorized user can view manage timetable

    public function test_authorized_user_can_view_manage_timetable()
    {
        $this->authorized_user(['update timetable'])
            ->get('/dashboard/timetables/1/manage')
            ->assertOk();
    }

    //test unauthorized user cannot store timetable time slot

    public function test_unauthorized_user_cant_store_timetable_time_slot()
    {
        $this->unauthorized_user()
            ->post('/dashboard/timetables/manage/time-slots', [
                'start_time' => '10:00',
                'stop_time'  => '11:00',
            ])->assertForbidden();
    }

    //test authorized user can store timetable time slot

    public function test_authorized_user_can_store_timetable_time_slot()
    {
        $timetable = Timetable::factory()->create();

        $this->authorized_user(['update timetable'])
            ->post('/dashboard/timetables/manage/time-slots', [
                'start_time'   => '10:00',
                'stop_time'    => '11:00',
                'timetable_id' => $timetable->id,
            ]);

        $this->assertDatabaseHas('timetable_time_slots', [
            'timetable_id' => $timetable->id,
            'start_time'   => '10:00:00',
            'stop_time'    => '11:00:00',
        ]);
    }

    //test unatuorized user cannot delete timetable time slot

    public function test_unauthorized_user_cant_delete_timetable_time_slot()
    {
        $timeslot = TimetableTimeSlot::factory()->create();
        $this->unauthorized_user()
            ->delete("/dashboard/timetables/manage/time-slots/$timeslot->id")
            ->assertForbidden();
    }

    //test authorized user can delete timetable time slot

    public function test_authorized_user_can_delete_timetable_time_slot()
    {
        $timeslot = TimetableTimeSlot::factory()->create();
        $this->authorized_user(['update timetable'])
            ->delete("/dashboard/timetables/manage/time-slots/$timeslot->id");

        $this->assertDatabaseMissing('timetable_time_slots', [
            'id'           => $timeslot->id,
            'timetable_id' => $timeslot->timetable_id,
            'start_time'   => "$timeslot->start_time:00",
            'stop_time'    => "$timeslot->stop_time:00",
        ]);
    }

    //test unauthorized user cannot create timetable record

    public function test_unauthorized_user_cannot_create_timetable_record()
    {
        $timeslot = TimetableTimeSlot::factory()->create();
        $this->unauthorized_user()
            ->post("/dashboard/timetables/manage/time-slots/$timeslot->id/record/create", [
                'type'       => 'subject',
                'weekday_id' => '1',
                'id'         => 1,
            ])->assertForbidden();
    }

    //test authorized user can create timetable record

    public function test_authorized_user_can_create_timetable_record()
    {
        $timeslot = TimetableTimeSlot::factory()->create();
        $this->authorized_user(['update timetable'])
            ->post("/dashboard/timetables/manage/time-slots/$timeslot->id/record/create", [
                'type'       => 'subject',
                'weekday_id' => '1',
                'id'         => '1',
            ])->assertRedirect();

        $this->assertDatabaseHas('timetable_time_slot_weekday', [
            'timetable_time_slot_id'             => $timeslot->id,
            'weekday_id'                         => 1,
            'timetable_time_slot_weekdayable_id' => 1,
        ]);
    }
}
