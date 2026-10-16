<?php

namespace Tests\Feature;

use App\Enums\TimetableStatus;
use App\Livewire\CreateSubjectForm;
use App\Livewire\EditSubjectForm;
use App\Livewire\ListSubjectsTable;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\School;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Models\Weekday;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public function test_unauthorized_user_cannot_see_all_subjects()
    {
        $this->unauthorized_user()
            ->get('/dashboard/subjects')
            ->assertForbidden();
    }

    public function test_authorized_user_can_see_all_subjects()
    {
        $this->authorized_user(['read subject'])
            ->get('/dashboard/subjects')
            ->assertOk();
    }

    public function test_unauthorized_user_cannot_view_create_subject()
    {
        $this->unauthorized_user()
            ->get('/dashboard/subjects/create')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_create_subject()
    {
        $this->authorized_user(['create subject'])
            ->get('/dashboard/subjects/create')
            ->assertOk();
    }

    public function test_subject_creation_livewire_flow_saves_and_redirects(): void
    {
        $this->authorized_user(['create subject']);

        Livewire::test(CreateSubjectForm::class)
            ->set('name', 'Livewire Algebra')
            ->set('short_name', 'ALG')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('subjects.index'));

        $this->assertDatabaseHas('subjects', [
            'name' => 'Livewire Algebra',
            'short_name' => 'ALG',
            'school_id' => $this->workingSchool()->id,
        ]);
    }

    public function test_subject_creation_livewire_flow_shows_validation_errors(): void
    {
        $this->authorized_user(['create subject']);

        Livewire::test(CreateSubjectForm::class)
            ->set('name', '')
            ->set('short_name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'short_name' => 'required']);
    }

    public function test_subject_creation_from_school_setup_returns_to_subject_setup(): void
    {
        $this->authorized_user(['create subject']);
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);

        Livewire::test(CreateSubjectForm::class, ['setup' => true, 'academicYearId' => $academicYear->id])
            ->set('name', 'Setup Algebra')
            ->set('short_name', 'SA')
            ->call('save')
            ->assertRedirect(route('academic-years.setup', [$academicYear, 'subjects']));
    }

    public function test_subject_edit_livewire_flow_updates_and_refuses_invalid_data(): void
    {
        $subject = Subject::factory()->create();
        $this->authorized_user(['update subject']);

        Livewire::test(EditSubjectForm::class, ['subject' => $subject])
            ->set('name', 'Updated Algebra')
            ->set('short_name', 'UA')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('subjects.index'));

        $this->assertSame('Updated Algebra', $subject->fresh()->name);
        $this->assertSame('UA', $subject->fresh()->short_name);
    }

    public function test_livewire_subject_edit_refuses_a_subject_from_another_school(): void
    {
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id + 1]);
        $this->authorized_user(['update subject']);

        Livewire::test(EditSubjectForm::class, ['subject' => $subject])
            ->assertForbidden();
    }

    public function test_unauthorized_user_cannot_view_edit_subject()
    {
        $this->unauthorized_user()
            ->get('/dashboard/subjects/1/edit')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_edit_subject()
    {
        $this->authorized_user(['update subject'])
            ->get('/dashboard/subjects/1/edit')
            ->assertOk();
    }

    public function test_the_classic_subject_write_routes_are_gone(): void
    {
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['create subject', 'update subject', 'delete subject']);

        $this->post('/dashboard/subjects', ['name' => 'Posted', 'short_name' => 'PS'])->assertStatus(405);
        $this->patch("/dashboard/subjects/$subject->id", ['name' => 'Patched', 'short_name' => 'PT'])->assertStatus(405);
        $this->delete("/dashboard/subjects/$subject->id")->assertStatus(405);

        $this->assertDatabaseMissing('subjects', ['name' => 'Posted']);
        $this->assertNotSoftDeleted($subject);
        $this->assertFalse(Route::has('subjects.store'));
        $this->assertFalse(Route::has('subjects.update'));
        $this->assertFalse(Route::has('subjects.destroy'));
    }

    public function test_unauthorized_user_cannot_delete_subject()
    {
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read subject']);

        Livewire::test(ListSubjectsTable::class)
            ->call('deleteSubject', $subject->id)
            ->assertForbidden();

        $this->assertNotSoftDeleted($subject);
    }

    public function test_authorized_user_can_delete_subject()
    {
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read subject', 'delete subject']);

        Livewire::test(ListSubjectsTable::class)
            ->assertSeeHtml('$wire.call(&quot;deleteSubject&quot;, row.id)')
            ->call('deleteSubject', $subject->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertSoftDeleted($subject);
    }

    public function test_another_schools_subject_cannot_be_deleted()
    {
        $theirs = Subject::factory()->create(['school_id' => School::factory()->create()->id]);
        $this->authorized_user(['read subject', 'delete subject']);

        try {
            Livewire::test(ListSubjectsTable::class)->call('deleteSubject', $theirs->id);
            $this->fail('Another school\'s subject was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertNotSoftDeleted($theirs);
    }

    /**
     * Course offerings, gradebooks and syllabi all read the subject name
     * straight off the relation, which is null once the subject is gone.
     */
    public function test_a_taught_subject_cannot_be_deleted()
    {
        $school = $this->workingSchool();
        $subject = Subject::factory()->create(['school_id' => $school->id]);
        CourseOffering::factory()->create(['school_id' => $school->id, 'subject_id' => $subject->id]);

        $registrar = $this->authorized_user(['delete subject', 'read subject']);

        Livewire::test(ListSubjectsTable::class)
            ->assertSee('row.course_offerings_count === 0', false)
            ->call('deleteSubject', $subject->id)
            ->assertDispatched('status-message', type: 'danger');

        $this->assertNotSoftDeleted($subject);

        $registrar->get(route('course-offerings.index'))->assertOk();
    }

    public function test_a_subject_on_a_published_timetable_cannot_be_deleted(): void
    {
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
        $timetable = Timetable::factory()->create(['status' => TimetableStatus::Draft]);
        $slot = TimetableTimeSlot::create(['timetable_id' => $timetable->id, 'start_time' => '10:00', 'stop_time' => '10:30']);
        TimetableRecord::create([
            'timetable_time_slot_id' => $slot->id,
            'weekday_id' => Weekday::firstOrFail()->id,
            'timetable_time_slot_weekdayable_id' => $subject->id,
            'timetable_time_slot_weekdayable_type' => $subject->getMorphClass(),
        ]);
        Timetable::query()->whereKey($timetable->id)->update(['status' => TimetableStatus::Published->value]);
        $this->authorized_user(['read subject', 'delete subject']);

        Livewire::test(ListSubjectsTable::class)
            ->call('deleteSubject', $subject->id)
            ->assertDispatched('status-message', type: 'danger');

        $this->assertNotSoftDeleted($subject);
        $this->assertSame(1, TimetableRecord::query()->where('timetable_time_slot_weekdayable_id', $subject->id)->count());
    }

    public function test_unathorized_user_cannot_view_subject()
    {
        $this->unauthorized_user()
            ->get('/dashboard/subjects/1')
            ->assertForbidden();
    }

    // public function test_authorized_user_can_view_subject()
    // {
    //     $user = User::factory()->create();
    //     $user->givePermissionTo(['read subject']);
    //     $this->actingAs($user);
    //     $response = $this->get('/dashboard/subjects/1');

    //     $response->assertOk();
    // }
}
