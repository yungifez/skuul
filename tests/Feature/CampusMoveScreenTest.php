<?php

namespace Tests\Feature;

use App\Actions\Enrollment\MoveEnrollmentBetweenCampuses;
use App\Actions\Enrollment\RequestCampusMove;
use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Enums\AcademicStructureStatus;
use App\Enums\CampusMoveStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrganizationPermission;
use App\Livewire\HealthRecordForm;
use App\Livewire\ListStudentFeeInvoices;
use App\Livewire\ShowStudentProfile;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\CampusMoveRequest;
use App\Models\FeeInvoice;
use App\Models\School;
use App\Models\StudentHealthRecord;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Authorization\CampusMoveAuthority;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Staff move a student between campuses from the student's own screen.
 */
class CampusMoveScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_screen_offers_the_sections_of_the_sibling_campuses(): void
    {
        $sibling = $this->siblingCampus();
        $cycleSection = $this->cycleSection($sibling);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read student', 'update student']);

        Livewire::test(ShowStudentProfile::class, ['student' => $enrollment->user])
            ->assertSee('Move to another campus')
            ->set('managing', 'campus')
            ->assertSee($sibling->name)
            ->assertSee($cycleSection->name);
    }

    public function test_a_campus_administrator_asks_instead_of_moving(): void
    {
        $sibling = $this->siblingCampus();
        $cycleSection = $this->cycleSection($sibling);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read student', 'update student', CampusMoveAuthority::RequestPermission]);

        Livewire::test(ShowStudentProfile::class, ['student' => $enrollment->user])
            ->set('managing', 'campus')
            ->assertSee('The receiving campus has to agree')
            ->assertSee('Ask the other campus')
            ->assertSeeHtml('<label for="campus-effective-on" class="mb-1.5 block text-sm font-medium">Effective on</label>')
            ->set('campusCycleSectionId', $cycleSection->id)
            ->set('campusReason', 'Family moved across town')
            ->call('moveCampus')
            ->assertHasNoErrors();

        $this->assertSame($this->workingSchool()->id, $enrollment->fresh()->school_id, 'Asking must not move the student.');
        $this->assertSame(CampusMoveStatus::Requested, CampusMoveRequest::query()->sole()->status);
    }

    public function test_a_reason_longer_than_its_column_is_refused_on_the_form(): void
    {
        $cycleSection = $this->cycleSection($this->siblingCampus());
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read student', 'update student', CampusMoveAuthority::RequestPermission]);

        Livewire::test(ShowStudentProfile::class, ['student' => $enrollment->user])
            ->set('managing', 'campus')
            ->set('campusCycleSectionId', $cycleSection->id)
            ->set('campusReason', str_repeat('a', 501))
            ->call('moveCampus')
            ->assertHasErrors(['campusReason' => 'max']);

        $this->assertSame(0, CampusMoveRequest::query()->count());
    }

    public function test_taking_back_a_request_already_decided_reloads_instead_of_failing(): void
    {
        $cycleSection = $this->cycleSection($this->siblingCampus());
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $request = app(RequestCampusMove::class)->request($enrollment, $cycleSection);
        $this->authorized_user(['read student', 'update student', CampusMoveAuthority::RequestPermission]);
        $screen = Livewire::test(ShowStudentProfile::class, ['student' => $enrollment->user]);

        app(RequestCampusMove::class)->reject($request);

        $screen->call('cancelCampusMove')
            ->assertDispatched('status-message', type: 'danger', message: 'The other campus already decided this request.');

        $this->assertSame(CampusMoveStatus::Rejected, $request->fresh()->status);
    }

    public function test_the_old_campus_still_sees_the_bills_a_moved_learner_left_behind(): void
    {
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $sibling->id]);
        $leftBehind = FeeInvoice::factory()->create([
            'name' => 'Term fees left behind',
            'school_id' => $this->workingSchool()->id,
            'student_record_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
        ]);
        FeeInvoice::factory()->create([
            'name' => 'Bill of the new campus',
            'school_id' => $sibling->id,
            'student_record_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
        ]);
        $this->authorized_user(['read student']);

        Livewire::test(ListStudentFeeInvoices::class, ['student' => $enrollment->user])
            ->assertSee($leftBehind->name)
            ->assertDontSee('Bill of the new campus')
            ->assertDontSee('No invoices');
    }

    public function test_an_organization_person_moves_the_student_straight_away(): void
    {
        $sibling = $this->siblingCampus();
        $cycleSection = $this->cycleSection($sibling);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $actor = $this->organizationPersonWith([OrganizationPermission::MoveStudents]);
        $this->memberOf($this->workingSchool(), $actor);
        school_context()->set($this->workingSchool(), remember: false);
        $actor->givePermissionTo(['read student', 'update student']);
        $this->actingAs($actor->refresh());

        Livewire::test(ShowStudentProfile::class, ['student' => $enrollment->user])
            ->set('managing', 'campus')
            ->assertDontSee('The receiving campus has to agree')
            ->assertSee('Move campus')
            ->set('campusCycleSectionId', $cycleSection->id)
            ->call('moveCampus')
            ->assertHasNoErrors();

        $moved = $enrollment->fresh();

        $this->assertSame($sibling->id, $moved->school_id);
        $this->assertSame($cycleSection->id, $moved->academic_cycle_section_id);
        $this->assertSame(EnrollmentStatus::Active, $moved->status);
        $this->assertSame(0, CampusMoveRequest::query()->count());
    }

    public function test_a_screen_left_open_at_the_old_campus_cannot_close_the_enrollment(): void
    {
        $sibling = $this->siblingCampus();
        $cycleSection = $this->cycleSection($sibling);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read student', 'update student']);

        $screen = Livewire::test(ShowStudentProfile::class, ['student' => $enrollment->user]);

        app(MoveEnrollmentBetweenCampuses::class)->move($enrollment, $cycleSection);

        $screen->set('statusSelection', EnrollmentStatus::Withdrawn->value)
            ->set('statusReason', 'Left the school')
            ->call('changeStatus')
            ->assertForbidden();

        $this->assertSame(EnrollmentStatus::Active, $enrollment->fresh()->status);
        $this->assertSame($sibling->id, $enrollment->fresh()->school_id);
    }

    public function test_only_the_campus_a_learner_attends_may_change_their_enrollment(): void
    {
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['update student', 'update health record']);

        $this->assertTrue(Gate::allows('manage', $enrollment));
        $this->assertTrue(Gate::allows('recordHealth', $enrollment));

        app(MoveEnrollmentBetweenCampuses::class)->move($enrollment, $this->cycleSection($sibling));
        $moved = $enrollment->fresh();

        $this->assertSame("This learner now attends {$sibling->name}. Only that campus can change their enrollment.", Gate::inspect('manage', $moved)->message());
        $this->assertTrue(Gate::denies('recordHealth', $moved));
    }

    public function test_a_health_form_left_open_at_the_old_campus_cannot_save(): void
    {
        $sibling = $this->siblingCampus();
        $cycleSection = $this->cycleSection($sibling);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read health record', 'update health record']);

        $form = Livewire::test(HealthRecordForm::class, ['enrollment' => $enrollment]);

        app(MoveEnrollmentBetweenCampuses::class)->move($enrollment, $cycleSection);

        try {
            $form->set('values.allergies', 'Peanuts')->call('save');
        } catch (HttpException) {
        }

        $this->assertNull(StudentHealthRecord::query()->where('student_record_id', $enrollment->id)->first());
    }

    public function test_somebody_without_either_right_cannot_move_or_ask(): void
    {
        $sibling = $this->siblingCampus();
        $cycleSection = $this->cycleSection($sibling);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read student', 'update student']);

        Livewire::test(ShowStudentProfile::class, ['student' => $enrollment->user])
            ->set('campusCycleSectionId', $cycleSection->id)
            ->call('moveCampus')
            ->assertHasErrors('campusCycleSectionId');

        $this->assertSame($this->workingSchool()->id, $enrollment->fresh()->school_id);
        $this->assertSame(0, CampusMoveRequest::query()->count());
    }

    /**
     * Make a person with authority over the working school's organization.
     *
     * @param  array<int, OrganizationPermission>  $permissions
     */
    private function organizationPersonWith(array $permissions): User
    {
        $organization = $this->workingSchool()->organization;

        // The organization must keep somebody who can manage its members.
        app(GrantOrganizationMembership::class)->grant($this->nonMember(), $organization);

        $user = $this->nonMember();
        app(GrantOrganizationMembership::class)->grant($user, $organization);
        app(SetOrganizationMemberPermissions::class)->set($user, $organization, $permissions);

        return $user->refresh();
    }

    public function test_the_screen_hides_the_move_when_the_organization_has_one_campus(): void
    {
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read student', 'update student']);

        Livewire::test(ShowStudentProfile::class, ['student' => $enrollment->user])
            ->assertDontSee('Move to another campus');
    }

    public function test_a_section_of_another_organization_is_refused(): void
    {
        $this->siblingCampus();
        $stranger = $this->cycleSection(School::factory()->create());
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read student', 'update student']);

        Livewire::test(ShowStudentProfile::class, ['student' => $enrollment->user])
            ->set('campusCycleSectionId', $stranger->id)
            ->call('moveCampus')
            ->assertHasErrors('campusCycleSectionId');

        $this->assertSame($this->workingSchool()->id, $enrollment->fresh()->school_id);
    }

    public function test_a_closed_or_past_year_section_of_a_sibling_is_refused(): void
    {
        $sibling = $this->siblingCampus();
        $current = $this->cycleSection($sibling);
        $draft = $this->cycleSection($sibling, AcademicStructureStatus::Draft, isCurrentYear: false);
        $draft->update(['academic_year_id' => $current->academic_year_id]);
        $pastYear = $this->cycleSection($sibling, isCurrentYear: false);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read student', 'update student']);

        $screen = Livewire::test(ShowStudentProfile::class, ['student' => $enrollment->user])
            ->assertSet('campusCycleSections', fn (array $sections): bool => array_column($sections, 'id') === [$current->id]);

        foreach ([$draft, $pastYear] as $section) {
            $screen->set('campusCycleSectionId', $section->id)
                ->call('moveCampus')
                ->assertHasErrors('campusCycleSectionId');
        }

        $this->assertSame($this->workingSchool()->id, $enrollment->fresh()->school_id);
    }

    /**
     * Make a second campus inside the working school's organization.
     */
    private function siblingCampus(): School
    {
        return School::factory()->create([
            'organization_id' => $this->workingSchool()->organization_id,
        ]);
    }

    /**
     * Make an open section in the campus's current school year.
     */
    private function cycleSection(School $school, AcademicStructureStatus $status = AcademicStructureStatus::Active, bool $isCurrentYear = true): AcademicCycleSection
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);

        if ($isCurrentYear) {
            $school->forceFill(['academic_year_id' => $academicYear->id])->save();
        }

        return AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'status' => $status,
        ]);
    }
}
