<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Enums\AcademicStructureStatus;
use App\Enums\CampusMoveStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrganizationPermission;
use App\Livewire\ShowStudentProfile;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\CampusMoveRequest;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Authorization\CampusMoveAuthority;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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
            ->assertSee('The receiving campus has to agree')
            ->set('campusCycleSectionId', $cycleSection->id)
            ->set('campusReason', 'Family moved across town')
            ->call('moveCampus')
            ->assertHasNoErrors();

        $this->assertSame($this->workingSchool()->id, $enrollment->fresh()->school_id, 'Asking must not move the student.');
        $this->assertSame(CampusMoveStatus::Requested, CampusMoveRequest::query()->sole()->status);
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
            ->assertSee('this move happens straight away')
            ->set('campusCycleSectionId', $cycleSection->id)
            ->call('moveCampus')
            ->assertHasNoErrors();

        $moved = $enrollment->fresh();

        $this->assertSame($sibling->id, $moved->school_id);
        $this->assertSame($cycleSection->id, $moved->academic_cycle_section_id);
        $this->assertSame(EnrollmentStatus::Active, $moved->status);
        $this->assertSame(0, CampusMoveRequest::query()->count());
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

    /**
     * Make a second campus inside the working school's organization.
     */
    private function siblingCampus(): School
    {
        return School::factory()->create([
            'organization_id' => $this->workingSchool()->organization_id,
        ]);
    }

    private function cycleSection(School $school): AcademicCycleSection
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);

        return AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]);
    }
}
