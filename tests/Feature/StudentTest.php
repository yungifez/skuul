<?php

namespace Tests\Feature;

use App\Enums\AcademicStructureStatus;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\Promotion;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

    // test view all students cannot be accessed by unauthorised users

    public function test_view_all_students_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/students/')->assertForbidden();
    }

    // test view all students can be accessed by authorised users

    public function test_view_all_students_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['read student'])->get('dashboard/students')->assertOk();
    }

    public function test_authorized_user_can_open_a_read_only_student_print_view(): void
    {
        $student = StudentRecord::factory()->create();

        $this->authorized_user(['read student'])
            ->get("dashboard/students/{$student->user->id}/print")
            ->assertOk()
            ->assertHeader('content-type', 'text/html; charset=UTF-8')
            ->assertSee('data-print-button', false)
            ->assertDontSee('Change enrollment status')
            ->assertDontSee('Save placement');
    }

    public function test_student_detail_does_not_render_a_blocking_n_plus_one_alert(): void
    {
        $student = StudentRecord::factory()->create();

        $this->authorized_user(['read student', 'update student'])
            ->get("dashboard/students/{$student->user->id}")
            ->assertOk()
            ->assertDontSee('Found the following N+1 queries');
    }

    // test create student cannot be accessed by unauthorised users

    public function test_create_student_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/students/create')->assertForbidden();
    }

    // test create student can be accessed by authorised users

    public function test_create_student_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['create student'])->get('dashboard/students/create')->assertOk();
    }

    public function test_create_student_screen_collects_only_required_profile_information(): void
    {
        $this->authorized_user(['create student'])
            ->get('dashboard/students/create')
            ->assertOk()
            ->assertSee('Full name *')
            ->assertSee('Optional profile details')
            ->assertDontSee('Blood group')
            ->assertDontSee('Religion');
    }

    // test unauthorised users cannot create students

    public function test_unauthorised_users_cannot_create_students()
    {
        $email = $this->faker()->freeEmail();
        $this->unauthorized_user()->post('dashboard/students', [
            'name' => 'Test Student cody',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
            'nationality' => 'nigeria',
            'state' => 'lagos',
            'city' => 'lagos',
            'address' => 'test address',
            'birthday' => '2004/04/22',
            'phone' => '08080808080',
            'academic_cycle_section_id' => $this->activeCycleSection()->id,
            'admission_date' => '2004/04/22',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }

    // test user can create student

    public function test_authorized_user_can_create_student()
    {
        $email = $this->faker()->freeEmail();

        $this->authorized_user(['create student'])->post('dashboard/students', [
            'name' => 'Test Student cody',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
            'nationality' => 'nigeria',
            'state' => 'lagos',
            'city' => 'lagos',
            'address' => 'test address',
            'birthday' => '2004/04/22',
            'phone' => '08080808080',
            'academic_cycle_section_id' => $this->activeCycleSection()->id,
            'admission_date' => '2004/04/22', ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'address' => 'test address',
            'birthday' => '2004/04/22',
            'phone' => '08080808080',
        ]);
    }

    // test edit student cannot be accessed by unauthorised users

    public function test_edit_student_cannot_be_accessed_to_unauthorised_users()
    {
        $student = StudentRecord::factory()->create();
        $this->unauthorized_user()->get('dashboard/students/'.$student->user->id.'/edit')->assertForbidden();
    }

    // test edit student can be accessed by authorised users

    public function test_edit_student_can_be_accessed_by_authorised_users()
    {
        $student = StudentRecord::factory()->create();
        $this->authorized_user(['update student'])->get('dashboard/students/'.$student->user->id.'/edit')->assertOk();
    }

    public function test_unauthorised_users_cannot_update_students()
    {
        $email = $this->faker()->freeEmail();

        $student = StudentRecord::factory()->create();

        $this->unauthorized_user()->put('dashboard/students/'.$student->user->id, [
            'name' => 'Test Student 2',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
            'nationality' => 'nigeria',
            'state' => 'lagos',
            'city' => 'lagos',
            'address' => 'test address',
            'birthday' => '2004/04/22',
            'phone' => '08080808080',
            'academic_cycle_section_id' => $this->activeCycleSection()->id,
            'admission_date' => '2004/04/22', ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }

    public function test_authorised_users_can_update_students()
    {
        $student = StudentRecord::factory()->create();
        $email = $this->faker()->freeEmail();

        $this->authorized_user(['update student'])->put('dashboard/students/'.$student->user->id, [
            'name' => 'Test 2 Student 2 Student',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
            'nationality' => 'nigeria',
            'state' => 'lagos',
            'city' => 'lagos',
            'address' => 'test address',
            'birthday' => '2004/04/22',
            'phone' => '08080808080',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    // test unauthorised users cannot delete students

    public function test_unauthorised_users_cannot_delete_students()
    {
        $student = StudentRecord::factory()->create();
        $this->unauthorized_user()
            ->delete('dashboard/students/'.$student->user->id)
            ->assertForbidden();

        $this->assertModelExists($student->user) && $this->assertNotSoftDeleted($student->user);
    }

    // test authorised users can delete students

    public function test_authorised_users_can_delete_students()
    {
        $student = StudentRecord::factory()->create();
        $this->authorized_user(['delete student'])->delete('dashboard/students/'.$student->user->id);

        $this->assertModelExists($student->user) && $this->assertSoftDeleted($student->user);
    }

    // test unauthorized user annot view all promotions

    public function test_unauthorized_user_cannot_view_all_promotions()
    {
        $this->unauthorized_user()->get('dashboard/students/promotions')->assertForbidden();
    }

    // test authorized user can view all promotions

    public function test_authorized_user_can_view_all_promotions()
    {
        $this->authorized_user(['read promotion'])->get('dashboard/students/promotions')->assertOk();
    }

    // test unauthorized user cannot view promotion

    public function test_unauthorized_user_cannot_view_promotion()
    {
        $promotion = Promotion::factory()->create();

        $this->unauthorized_user()->get('dashboard/students/promotions/'.$promotion->id)->assertForbidden();
    }

    // test authorized user can view promotion

    public function test_authorized_user_can_view_promotion()
    {
        $promotion = Promotion::factory()->create();

        $this->authorized_user(['read promotion'])->get('dashboard/students/promotions/'.$promotion->id)->assertOk();
    }

    // tes unauthorized user cannot view promoteview

    public function test_unauthorized_user_cannot_view_promoteview()
    {
        $this->unauthorized_user()->get('/dashboard/students/promote')->assertForbidden();
    }

    // test authorized user can view promoteview

    public function test_authorized_user_can_view_promoteview()
    {
        $this->authorized_user(['promote student'])->get('/dashboard/students/promote')->assertOk();
    }

    // test unauthorized user cannot promote students

    public function test_unauthorized_user_cannot_promote_students()
    {
        $student = StudentRecord::factory()->create();
        $destination = $this->activeCycleSection();

        $this->unauthorized_user()->post('/dashboard/students/promote', [
            'student_id' => [$student->user->id],
            'source_academic_cycle_section_id' => $student->academic_cycle_section_id,
            'destination_academic_cycle_section_id' => $destination->id,
        ])->assertForbidden();
    }

    // test authorized user can promote students

    public function test_authorized_user_can_promote_students()
    {
        $student = StudentRecord::factory()->create();
        $source = $student->academic_cycle_section_id;
        $destination = $this->activeCycleSection();

        $this->authorized_user(['promote student'])->post('/dashboard/students/promote', [
            'student_id' => [$student->user->id],
            'source_academic_cycle_section_id' => $source,
            'destination_academic_cycle_section_id' => $destination->id,
        ]);

        $promotion = Promotion::where([
            'source_academic_cycle_section_id' => $source,
            'destination_academic_cycle_section_id' => $destination->id,
        ])->whereJsonContains('students', [$student->user->id])->first();

        $this->assertModelExists($promotion);
        $this->assertSame($destination->id, $student->fresh()->academic_cycle_section_id);
    }

    // test unauthorized user cannot delete promotion

    public function test_unauthorized_user_cannot_delete_promotion()
    {
        $this->unauthorized_user()->delete('dashboard/students/promotions/1/reset')->assertForbidden();
    }

    // test authorized user can delete promotion

    public function test_authorized_user_can_delete_promotion()
    {
        $promotion = Promotion::factory()->create();
        $this->authorized_user(['reset promotion'])->delete('dashboard/students/promotions/'.$promotion->id.'/reset');

        $this->assertModelMissing($promotion);
    }

    // test unauthorized user cannot view all graduations

    public function test_unauthorized_user_cannot_view_all_graduations()
    {
        $this->unauthorized_user()->get('dashboard/students/graduations')->assertForbidden();
    }

    // test authorized user can view all graduations

    public function test_authorized_user_can_view_all_graduations()
    {
        $this->authorized_user(['view graduations'])->get('dashboard/students/graduations')->assertOk();
    }

    // test unauthorized user cannot graduate student

    public function test_unauthorized_user_cannot_graduate_student()
    {
        $student = StudentRecord::factory()->create();
        $this->unauthorized_user()->post('/dashboard/students/graduate', [
            'student_id' => [$student->user->id],
        ])->assertForbidden();
    }

    /**
     * Get an active cycle section in this year that a student can be placed in.
     */
    private function activeCycleSection(): AcademicCycleSection
    {
        $school = $this->workingSchool();
        $academicLevel = AcademicLevel::query()->where('school_id', $school->id)->first()
            ?? AcademicLevel::factory()->create(['school_id' => $school->id]);

        return AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => current_academic_year_id(),
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]);
    }
}
