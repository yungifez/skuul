<?php

namespace Tests\Feature;

use App\Livewire\CreateSubjectForm;
use App\Livewire\EditSubjectForm;
use App\Livewire\ListSubjectsTable;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Subject;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    public function test_unauthorized_user_cannot_create_subject()
    {
        $name = $this->faker()->name;
        $this->unauthorized_user()
            ->post('/dashboard/subjects', [
                'name' => $name,
                'short_name' => 'TS',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('subjects', [
            'name' => $name,
        ]);
    }

    public function test_authorized_user_can_create_subject()
    {
        $name = $this->faker()->name;

        $this->authorized_user(['create subject'])
            ->post('/dashboard/subjects', [
                'name' => $name,
                'short_name' => 'TS',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('subjects', [
            'name' => $name,
        ]);
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

    public function test_unauthorized_user_cannot_update_subject()
    {
        $subject = Subject::factory()->create();
        $name = $this->faker->name;
        $this->unauthorized_user()
            ->patch("/dashboard/subjects/$subject->id", [
                'name' => $name,
                'short_name' => 'TS2',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('subjects', [
            'id' => $subject->id,
            'name' => $name,
        ]);
    }

    public function test_authorized_user_can_update_subject()
    {
        $subject = Subject::factory()->create();
        $name = $this->faker()->name;
        $this->authorized_user(['update subject'])
            ->patch("/dashboard/subjects/$subject->id", [
                'name' => $name,
                'short_name' => 'TS2',
            ])->assertRedirect();

        $this->assertEquals($name, $subject->fresh()->name);
    }

    public function test_unauthorized_user_cannot_delete_subject()
    {
        $subject = Subject::factory()->create();
        $this->unauthorized_user()
            ->delete("/dashboard/subjects/$subject->id")
            ->assertForbidden();

        $this->assertModelExists($subject);

        $this->assertNotSoftDeleted($subject);
    }

    public function test_authorized_user_can_delete_subject()
    {
        $subject = Subject::factory()->create();
        $this->authorized_user(['delete subject'])
            ->delete("/dashboard/subjects/$subject->id")
            ->assertRedirect();

        $this->assertModelExists($subject);

        $this->assertSoftDeleted($subject);
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

        $registrar->delete("/dashboard/subjects/$subject->id")
            ->assertRedirect()
            ->assertSessionHas('danger');

        $this->assertNotSoftDeleted($subject);

        $registrar->get(route('course-offerings.index'))->assertOk();

        Livewire::test(ListSubjectsTable::class)
            ->assertSee('row.course_offerings_count === 0', false);
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
