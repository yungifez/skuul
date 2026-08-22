<?php

namespace Tests\Feature;

use App\Actions\Enrollment\MoveEnrollmentBetweenCampuses;
use App\Actions\Enrollment\RequestCampusMove;
use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\CampusMoveStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrganizationPermission;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\CampusMoveRequest;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Authorization\CampusMoveAuthority;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Moving a student between campuses is tiered.
 *
 * An organization person moves them straight away. A campus administrator
 * only asks, and the receiving campus decides.
 */
class CampusMoveRequestTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_campus_administrator_only_asks(): void
    {
        $source = $this->workingSchool();
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        $actor = $this->campusAdministratorOf($source, [CampusMoveAuthority::RequestPermission]);
        $cycleSection = $this->cycleSection($sibling);

        $this->assertFalse(app(CampusMoveAuthority::class)->movesFreely($actor, $sibling));
        $this->assertTrue(app(CampusMoveAuthority::class)->canRequest($actor, $source));

        $request = app(RequestCampusMove::class)->request($enrollment, $cycleSection, $actor, 'Family moved');

        $this->assertSame(CampusMoveStatus::Requested, $request->status);
        $this->assertSame($source->id, $enrollment->fresh()->school_id, 'Asking must not move the student.');
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::CampusMoveRequested)->forSubject($request)->first());
    }

    public function test_approving_moves_the_student_in_the_same_breath(): void
    {
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $cycleSection = $this->cycleSection($sibling);
        $request = app(RequestCampusMove::class)->request($enrollment, $cycleSection);
        $approver = $this->campusAdministratorOf($sibling, [CampusMoveAuthority::ApprovePermission]);

        $this->assertTrue(app(CampusMoveAuthority::class)->canDecide($approver, $request));

        $decided = app(RequestCampusMove::class)->approve($request, $approver, 'We have room');
        $moved = $enrollment->fresh();

        $this->assertSame(CampusMoveStatus::Approved, $decided->status);
        $this->assertSame($approver->id, $decided->decided_by);
        $this->assertSame($sibling->id, $moved->school_id);
        $this->assertSame($cycleSection->id, $moved->academic_cycle_section_id);
        $this->assertSame(1, StudentRecord::query()->where('user_id', $enrollment->user_id)->count());
    }

    public function test_rejecting_leaves_the_student_where_they_are(): void
    {
        $source = $this->workingSchool();
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        $request = app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($sibling));
        $approver = $this->campusAdministratorOf($sibling, [CampusMoveAuthority::ApprovePermission]);

        $decided = app(RequestCampusMove::class)->reject($request, $approver, 'The section is full');

        $this->assertSame(CampusMoveStatus::Rejected, $decided->status);
        $this->assertSame($source->id, $enrollment->fresh()->school_id);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::CampusMoveStatusChanged)->forSubject($request)->first());
    }

    public function test_the_source_campus_may_take_its_request_back(): void
    {
        $source = $this->workingSchool();
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        $actor = $this->campusAdministratorOf($source, [CampusMoveAuthority::RequestPermission]);
        $request = app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($sibling), $actor);

        $this->assertTrue(app(CampusMoveAuthority::class)->canCancel($actor, $request));

        $decided = app(RequestCampusMove::class)->cancel($request, $actor);

        $this->assertSame(CampusMoveStatus::Cancelled, $decided->status);
        $this->assertSame($source->id, $enrollment->fresh()->school_id);
    }

    public function test_the_asking_campus_cannot_approve_its_own_request(): void
    {
        $source = $this->workingSchool();
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        $actor = $this->campusAdministratorOf($source, [
            CampusMoveAuthority::RequestPermission,
            CampusMoveAuthority::ApprovePermission,
        ]);
        $request = app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($sibling), $actor);

        $this->assertFalse(
            app(CampusMoveAuthority::class)->canDecide($actor, $request),
            'An approve permission at the asking campus must not decide the receiving campus.'
        );
    }

    public function test_an_organization_person_moves_a_student_without_asking(): void
    {
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $cycleSection = $this->cycleSection($sibling);
        $actor = $this->organizationPersonWith([OrganizationPermission::MoveStudents]);

        $this->assertTrue(app(CampusMoveAuthority::class)->movesFreely($actor, $sibling));

        app(MoveEnrollmentBetweenCampuses::class)->move($enrollment, $cycleSection, $actor);

        $this->assertSame($sibling->id, $enrollment->fresh()->school_id);
        $this->assertSame(0, CampusMoveRequest::query()->count(), 'A free move never writes a request.');
    }

    public function test_an_organization_person_may_decide_a_campus_request(): void
    {
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $request = app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($sibling));
        $actor = $this->organizationPersonWith([OrganizationPermission::MoveStudents]);

        $this->assertTrue(app(CampusMoveAuthority::class)->canDecide($actor, $request));

        app(RequestCampusMove::class)->approve($request, $actor);

        $this->assertSame($sibling->id, $enrollment->fresh()->school_id);
    }

    public function test_an_organization_person_without_the_move_right_cannot_decide(): void
    {
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $request = app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($sibling));
        $actor = $this->organizationPersonWith([OrganizationPermission::ReadReports]);

        $this->assertFalse(app(CampusMoveAuthority::class)->movesFreely($actor, $sibling));
        $this->assertFalse(app(CampusMoveAuthority::class)->canDecide($actor, $request));
    }

    public function test_a_stranger_cannot_decide_the_request(): void
    {
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $request = app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($sibling));

        $this->assertFalse(app(CampusMoveAuthority::class)->canDecide($this->nonMember(), $request));
    }

    public function test_one_student_cannot_have_two_open_requests(): void
    {
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($sibling));

        $this->expectException(InvalidValueException::class);

        app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($sibling));
    }

    public function test_a_decided_request_cannot_be_decided_again(): void
    {
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $request = app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($sibling));
        app(RequestCampusMove::class)->reject($request);

        $this->expectException(InvalidValueException::class);

        app(RequestCampusMove::class)->approve($request->fresh());
    }

    public function test_a_request_to_another_organization_is_refused(): void
    {
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);

        $this->expectException(InvalidValueException::class);

        app(RequestCampusMove::class)->request($enrollment, $this->cycleSection(School::factory()->create()));
    }

    public function test_a_closed_enrollment_cannot_be_requested(): void
    {
        $sibling = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'status'    => EnrollmentStatus::Graduated,
        ]);

        $this->expectException(InvalidValueException::class);

        app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($sibling));
    }

    /**
     * Make a person who administers one campus and nothing else.
     *
     * @param array<int, string> $permissions
     */
    private function campusAdministratorOf(School $school, array $permissions): User
    {
        $user = $this->memberOf($school);

        school_context()->set($school, remember: false);
        $user->givePermissionTo($permissions);
        school_context()->set($this->workingSchool(), remember: false);

        return $user->refresh();
    }

    /**
     * Make a person with authority over the working school's organization.
     *
     * @param array<int, OrganizationPermission> $permissions
     */
    private function organizationPersonWith(array $permissions): User
    {
        $organization = $this->workingSchool()->organization;

        // The organization must keep somebody who can manage its members, so
        // give it a full administrator before narrowing anybody else.
        app(GrantOrganizationMembership::class)->grant($this->nonMember(), $organization);

        $user = $this->nonMember();
        app(GrantOrganizationMembership::class)->grant($user, $organization);
        app(SetOrganizationMemberPermissions::class)->set($user, $organization, $permissions);

        return $user->refresh();
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
        $academicYear = AcademicYear::query()->where('school_id', $school->id)->first()
            ?? AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicLevel = AcademicLevel::query()->where('school_id', $school->id)->first()
            ?? AcademicLevel::factory()->create(['school_id' => $school->id]);

        return AcademicCycleSection::factory()->create([
            'school_id'         => $school->id,
            'academic_year_id'  => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'status'            => AcademicStructureStatus::Active,
        ]);
    }
}
