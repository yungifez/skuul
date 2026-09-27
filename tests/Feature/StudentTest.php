<?php

namespace Tests\Feature;

use App\Enums\AcademicStructureStatus;
use App\Enums\EnrollmentStatus;
use App\Livewire\CreateStudentForm;
use App\Livewire\EditStudentForm;
use App\Livewire\GraduateStudents;
use App\Livewire\ListPromotionsTable;
use App\Livewire\ListStudentsTable;
use App\Livewire\PromoteStudents;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\Promotion;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
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
            ->assertSeeInOrder(['Student record', $student->user->name, 'Personal details', 'Enrollment', 'Parents and guardians', 'Placement history', 'Status history', 'Signature and school stamp'])
            ->assertSee($student->admission_number)
            ->assertDontSee('Change enrollment status')
            ->assertDontSee('Save placement')
            ->assertDontSee('Set password');
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
            ->assertSee('wire:model="name"', false)
            ->assertSee('wire:model="academicCycleSectionId"', false)
            ->assertSee('data-slot="combobox"', false)
            ->assertDontSee('Blood group')
            ->assertDontSee('Religion');
    }

    // test unauthorised users cannot create students

    public function test_unauthorised_users_cannot_create_students(): void
    {
        $this->unauthorized_user();

        Livewire::test(CreateStudentForm::class)->assertForbidden();
    }

    // test user can create student

    public function test_authorized_user_can_create_student(): void
    {
        $email = $this->faker()->unique()->freeEmail();
        $this->authorized_user(['create student', 'read student']);
        $section = $this->activeCycleSection();

        $component = $this->admitLearner($email, $section)->assertHasNoErrors();

        $student = User::query()->where('email', $email)->sole();

        $component->assertRedirect(route('students.show', $student));
        $this->assertSame('Nigeria', $student->country);
        $this->assertSame('Lagos', $student->state);
        $this->assertSame($section->id, $student->studentRecord->academic_cycle_section_id);
        $this->assertNotNull($student->studentRecord->admission_number);
    }

    public function test_a_learner_already_enrolled_here_is_not_admitted_again(): void
    {
        $this->authorized_user(['create student']);
        $enrolled = $this->learnerIn($this->activeCycleSection());
        $otherSection = $this->activeCycleSection();

        $this->admitLearner($enrolled->user->email, $otherSection)
            ->assertHasErrors('email')
            ->assertNoRedirect();

        $this->assertNotSame($otherSection->id, $enrolled->fresh()->academic_cycle_section_id);
    }

    public function test_a_learner_enrolled_at_another_school_must_be_transferred(): void
    {
        $this->workingSchool();
        $otherSchool = School::factory()->create();
        $learner = $this->nonMember();
        $learner->forceFill(['email' => $this->faker()->unique()->freeEmail()])->save();
        $this->memberOf($otherSchool, $learner);
        $elsewhere = StudentRecord::factory()->create(['user_id' => $learner->id, 'school_id' => $otherSchool->id]);
        $this->authorized_user(['create student']);

        $this->admitLearner($elsewhere->user->email, $this->activeCycleSection())
            ->assertHasErrors(['email' => 'This learner is enrolled at another school. Ask that school to move or transfer them.']);

        $this->assertSame(0, StudentRecord::query()->where('user_id', $elsewhere->user_id)->where('school_id', $this->workingSchool()->id)->count());
    }

    public function test_an_admission_number_is_unique_in_the_school(): void
    {
        $this->authorized_user(['create student']);
        $enrolled = $this->learnerIn($this->activeCycleSection());

        $this->admitLearner($this->faker()->unique()->freeEmail(), $this->activeCycleSection(), $enrolled->admission_number)
            ->assertHasErrors('admissionNumber');
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

    public function test_unauthorised_users_cannot_update_students(): void
    {
        $student = StudentRecord::factory()->create();
        $this->unauthorized_user();

        Livewire::test(EditStudentForm::class, ['student' => $student->user])->assertForbidden();
    }

    public function test_authorised_users_can_update_students(): void
    {
        $student = StudentRecord::factory()->create();
        $email = $this->faker()->unique()->freeEmail();
        $this->authorized_user(['update student']);

        Livewire::test(EditStudentForm::class, ['student' => $student->user])
            ->set('email', $email)
            ->set('addressLine2', 'Flat 3')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('students.show', $student->user));

        $this->assertSame($email, $student->user->fresh()->email);
        $this->assertSame('Flat 3', $student->user->fresh()->address_line_2);
    }

    public function test_unauthorised_users_cannot_delete_students()
    {
        $student = StudentRecord::factory()->create()->user;
        $this->authorized_user(['read student']);

        Livewire::test(ListStudentsTable::class)
            ->call('deleteStudent', $student->id)
            ->assertForbidden();

        $this->assertNotSoftDeleted($student);
    }

    public function test_authorised_users_can_delete_students()
    {
        $student = StudentRecord::factory()->create()->user;
        $this->authorized_user(['read student', 'delete student']);

        Livewire::test(ListStudentsTable::class)
            ->assertSeeHtml('$wire.call(&quot;deleteStudent&quot;, row.id)')
            ->call('deleteStudent', $student->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertSoftDeleted($student);
    }

    public function test_a_student_of_another_school_cannot_be_deleted()
    {
        $student = StudentRecord::factory()->create()->user;
        $student->schoolMemberships()->delete();
        $this->memberOf(School::factory()->create(), $student->refresh());
        $this->authorized_user(['read student', 'delete student']);

        try {
            Livewire::test(ListStudentsTable::class)->call('deleteStudent', $student->id);
            $this->fail('A student of another school was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertNotSoftDeleted($student);
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

    public function test_the_promotion_list_sorts_by_the_number_of_learners_it_shows(): void
    {
        $this->authorized_user(['read promotion']);

        Promotion::factory()->count(2)->create();

        Livewire::test(ListPromotionsTable::class)
            ->call('updateTable', ['sort' => ['key' => 'learners_count', 'direction' => 'desc']])
            ->assertOk();
    }

    public function test_unauthorized_user_cannot_view_promoteview()
    {
        $this->unauthorized_user()->get('/dashboard/students/promote')->assertForbidden();
    }

    // test authorized user can view promoteview

    public function test_authorized_user_can_view_promoteview()
    {
        $this->authorized_user(['promote student'])->get('/dashboard/students/promote')->assertOk();
    }

    public function test_unauthorized_user_cannot_promote_students(): void
    {
        $this->unauthorized_user();

        Livewire::test(PromoteStudents::class)->assertForbidden();
    }

    public function test_authorized_user_can_promote_students(): void
    {
        $this->authorized_user(['promote student', 'read promotion']);
        $source = $this->activeCycleSection();
        $destination = $this->activeCycleSection();
        $student = $this->learnerIn($source);

        $component = Livewire::test(PromoteStudents::class)
            ->set('sourceAcademicCycleSectionId', $source->id)
            ->set('destinationAcademicCycleSectionId', $destination->id)
            ->call('loadStudents')
            ->assertSee($student->user->name)
            ->assertSet('selectedStudentIds', [$student->user_id])
            ->call('promote')
            ->assertHasNoErrors();

        $promotion = Promotion::query()->whereJsonContains('students', [$student->user_id])->sole();

        $component->assertRedirect(route('students.promotions.show', $promotion));
        $this->assertSame($source->id, $promotion->source_academic_cycle_section_id);
        $this->assertSame($destination->id, $student->fresh()->academic_cycle_section_id);
    }

    public function test_a_learner_left_unticked_stays_where_they_are(): void
    {
        $this->authorized_user(['promote student']);
        $source = $this->activeCycleSection();
        $destination = $this->activeCycleSection();
        $moving = $this->learnerIn($source);
        $staying = $this->learnerIn($source);

        Livewire::test(PromoteStudents::class)
            ->set('sourceAcademicCycleSectionId', $source->id)
            ->set('destinationAcademicCycleSectionId', $destination->id)
            ->call('loadStudents')
            ->set('selectedStudentIds', [$moving->user_id])
            ->call('promote')
            ->assertRedirect(route('students.promote'));

        $this->assertSame($destination->id, $moving->fresh()->academic_cycle_section_id);
        $this->assertSame($source->id, $staying->fresh()->academic_cycle_section_id);
    }

    public function test_a_second_click_on_a_page_left_open_moves_nobody_twice(): void
    {
        $this->authorized_user(['promote student']);
        $source = $this->activeCycleSection();
        $destination = $this->activeCycleSection();
        $student = $this->learnerIn($source);

        $open = fn () => Livewire::test(PromoteStudents::class)
            ->set('sourceAcademicCycleSectionId', $source->id)
            ->set('destinationAcademicCycleSectionId', $destination->id)
            ->call('loadStudents');
        $promotionsBefore = Promotion::query()->count();
        $firstTab = $open();
        $secondTab = $open();

        $firstTab->call('promote');
        $secondTab->call('promote')->assertNoRedirect()->assertSet('students', []);

        $this->assertSame($promotionsBefore + 1, Promotion::query()->count());
        $this->assertSame(1, $student->placements()->where('academic_cycle_section_id', $destination->id)->count());
    }

    public function test_a_learner_the_page_never_listed_cannot_be_slipped_in(): void
    {
        $this->authorized_user(['promote student']);
        $source = $this->activeCycleSection();
        $destination = $this->activeCycleSection();
        $listed = $this->learnerIn($source);
        $elsewhere = $this->learnerIn($this->activeCycleSection());
        $promotionsBefore = Promotion::query()->count();

        Livewire::test(PromoteStudents::class)
            ->set('sourceAcademicCycleSectionId', $source->id)
            ->set('destinationAcademicCycleSectionId', $destination->id)
            ->call('loadStudents')
            ->set('selectedStudentIds', [$listed->user_id, $elsewhere->user_id])
            ->call('promote')
            ->assertHasErrors('selectedStudentIds.1');

        $this->assertSame($promotionsBefore, Promotion::query()->count());
    }

    public function test_another_schools_section_cannot_be_chosen(): void
    {
        $this->authorized_user(['promote student']);
        $source = $this->activeCycleSection();
        $foreign = AcademicCycleSection::factory()->create(['school_id' => School::factory()->create()->id]);

        Livewire::test(PromoteStudents::class)
            ->set('sourceAcademicCycleSectionId', $source->id)
            ->set('destinationAcademicCycleSectionId', $foreign->id)
            ->call('loadStudents')
            ->assertHasErrors('destinationAcademicCycleSectionId')
            ->set('sourceAcademicCycleSectionId', $foreign->id)
            ->set('destinationAcademicCycleSectionId', $source->id)
            ->call('loadStudents')
            ->assertHasErrors('sourceAcademicCycleSectionId')
            ->assertSet('students', []);
    }

    public function test_changing_a_section_drops_the_reviewed_list(): void
    {
        $this->authorized_user(['promote student']);
        $source = $this->activeCycleSection();
        $destination = $this->activeCycleSection();
        $this->learnerIn($source);
        $otherSection = $this->activeCycleSection();
        $promotionsBefore = Promotion::query()->count();

        Livewire::test(PromoteStudents::class)
            ->set('sourceAcademicCycleSectionId', $source->id)
            ->set('destinationAcademicCycleSectionId', $destination->id)
            ->call('loadStudents')
            ->set('sourceAcademicCycleSectionId', $otherSection->id)
            ->assertSet('students', [])
            ->call('promote')
            ->assertHasErrors('selectedStudentIds');

        $this->assertSame($promotionsBefore, Promotion::query()->count());
    }

    public function test_an_empty_section_says_so(): void
    {
        $this->authorized_user(['promote student']);
        $source = $this->activeCycleSection();
        $destination = $this->activeCycleSection();

        Livewire::test(PromoteStudents::class)
            ->set('sourceAcademicCycleSectionId', $source->id)
            ->set('destinationAcademicCycleSectionId', $destination->id)
            ->call('loadStudents')
            ->assertHasErrors(['sourceAcademicCycleSectionId' => 'No active learners are in this section.']);
    }

    public function test_resetting_a_promotion_needs_permission()
    {
        $promotion = Promotion::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read promotion']);

        Livewire::test(ListPromotionsTable::class)
            ->call('resetPromotion', $promotion->id)
            ->assertForbidden();

        $this->assertModelExists($promotion);
    }

    public function test_authorized_user_can_reset_a_promotion()
    {
        $promotion = Promotion::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read promotion', 'reset promotion']);

        Livewire::test(ListPromotionsTable::class)
            ->call('resetPromotion', $promotion->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertModelMissing($promotion);
        $this->assertFalse(Route::has('students.promotions.reset'));
    }

    public function test_another_schools_promotion_cannot_be_reset()
    {
        $promotion = Promotion::factory()->create(['school_id' => School::factory()->create()->id]);
        $this->authorized_user(['read promotion', 'reset promotion']);

        try {
            Livewire::test(ListPromotionsTable::class)->call('resetPromotion', $promotion->id);
            $this->fail('Another school\'s promotion was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertModelExists($promotion);
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

    public function test_unauthorized_user_cannot_graduate_student(): void
    {
        $this->unauthorized_user();

        Livewire::test(GraduateStudents::class)->assertForbidden();
    }

    public function test_a_learner_left_unticked_does_not_graduate(): void
    {
        $this->authorized_user(['graduate student', 'view graduations']);
        $section = $this->activeCycleSection();
        $leaving = $this->learnerIn($section);
        $staying = $this->learnerIn($section);

        Livewire::test(GraduateStudents::class)
            ->set('academicCycleSectionId', $section->id)
            ->call('loadStudents')
            ->assertSee($leaving->user->name)
            ->set('selectedStudentIds', [$leaving->user_id])
            ->set('reason', 'Completed the final year')
            ->call('graduate')
            ->assertHasNoErrors()
            ->assertRedirect(route('students.graduations'));

        $this->assertSame(EnrollmentStatus::Graduated, $leaving->fresh()->status);
        $this->assertSame(EnrollmentStatus::Active, $staying->fresh()->status);
    }

    public function test_a_learner_of_another_section_cannot_be_slipped_into_a_graduation(): void
    {
        $this->authorized_user(['graduate student']);
        $section = $this->activeCycleSection();
        $listed = $this->learnerIn($section);
        $elsewhere = $this->learnerIn($this->activeCycleSection());

        Livewire::test(GraduateStudents::class)
            ->set('academicCycleSectionId', $section->id)
            ->call('loadStudents')
            ->set('selectedStudentIds', [$listed->user_id, $elsewhere->user_id])
            ->call('graduate')
            ->assertHasErrors('selectedStudentIds.1');

        $this->assertSame(EnrollmentStatus::Active, $listed->fresh()->status);
        $this->assertSame(EnrollmentStatus::Active, $elsewhere->fresh()->status);
    }

    public function test_a_second_click_on_a_page_left_open_explains_nothing_is_left(): void
    {
        $this->authorized_user(['graduate student']);
        $section = $this->activeCycleSection();
        $student = $this->learnerIn($section);

        $open = fn () => Livewire::test(GraduateStudents::class)
            ->set('academicCycleSectionId', $section->id)
            ->call('loadStudents');
        $firstTab = $open();
        $secondTab = $open();

        $firstTab->call('graduate')->assertRedirect(route('students.graduate'));
        $secondTab->call('graduate')->assertNoRedirect()->assertSet('students', []);

        $this->assertSame(EnrollmentStatus::Graduated, $student->fresh()->status);
    }

    public function test_another_schools_section_cannot_be_chosen_for_graduation(): void
    {
        $this->authorized_user(['graduate student']);
        $foreign = AcademicCycleSection::factory()->create(['school_id' => School::factory()->create()->id]);

        Livewire::test(GraduateStudents::class)
            ->set('academicCycleSectionId', $foreign->id)
            ->call('loadStudents')
            ->assertHasErrors('academicCycleSectionId')
            ->assertSet('students', []);
    }

    /**
     * Fill the admission screen and press save.
     */
    private function admitLearner(string $email, AcademicCycleSection $section, ?string $admissionNumber = null): Testable
    {
        $component = Livewire::test(CreateStudentForm::class)
            ->set('name', 'Test Student cody')
            ->set('email', $email)
            ->set('gender', 'Male')
            ->set('birthday', '2004-04-22')
            ->set('admissionDate', '2024-09-01')
            ->set('academicCycleSectionId', (string) $section->id);

        $component->dispatch('country-updated', country: 'Nigeria');
        $component->dispatch('state-updated', state: 'Lagos');

        if ($admissionNumber !== null) {
            $component->set('admissionNumber', $admissionNumber);
        }

        return $component->call('save');
    }

    /**
     * Enroll an active learner of the working school in a section.
     */
    private function learnerIn(AcademicCycleSection $section): StudentRecord
    {
        return StudentRecord::factory()->create([
            'school_id' => $section->school_id,
            'academic_cycle_section_id' => $section->id,
        ]);
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

    public function test_a_teacher_who_is_also_a_parent_still_reads_every_learner(): void
    {
        $ownChild = StudentRecord::factory()->create();
        $otherLearner = StudentRecord::factory()->create();
        $teacher = $this->personWithRoles(['teacher', 'parent']);
        $teacher->parentRecord()->create(['user_id' => $teacher->id]);
        $teacher->parentRecord->students()->attach($ownChild->user_id);

        $this->get(route('students.show', $otherLearner->user))->assertOk();

        Livewire::test(ListStudentsTable::class)
            ->set('search', $otherLearner->user->name)
            ->assertSee($otherLearner->user->email);
    }

    public function test_a_parent_only_reads_their_own_children(): void
    {
        $ownChild = StudentRecord::factory()->create();
        $otherLearner = StudentRecord::factory()->create();
        $parent = $this->personWithRoles(['parent']);
        $parent->parentRecord()->create(['user_id' => $parent->id]);
        $parent->parentRecord->students()->attach($ownChild->user_id);

        $this->get(route('students.show', $ownChild->user))->assertOk();
        $this->get(route('students.show', $otherLearner->user))->assertNotFound();

        Livewire::test(ListStudentsTable::class)
            ->assertSee($ownChild->user->email)
            ->set('search', $otherLearner->user->name)
            ->assertDontSee($otherLearner->user->email);
    }

    /**
     * Sign in as a member of the working school who holds these roles there.
     *
     * @param  array<int, string>  $roles
     */
    private function personWithRoles(array $roles): User
    {
        $school = $this->workingSchool();
        $person = $this->memberOf($school);
        school_context()->set($school, remember: false);
        $person->assignRole($roles);
        $this->actingAsMemberOf($school, $person);

        return $person->refresh();
    }
}
