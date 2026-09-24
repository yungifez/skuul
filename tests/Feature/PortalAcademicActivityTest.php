<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Enums\Feature;
use App\Enums\ParticipationStatus;
use App\Enums\PortalArea;
use App\Enums\ProgramType;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\CourseOffering;
use App\Models\GraduationPlan;
use App\Models\GraduationRequirement;
use App\Models\Program;
use App\Models\ProgramParticipation;
use App\Models\ResultSnapshot;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Learners and guardians read only their own graduation progress and
 * student-facing programme places.
 */
class PortalAcademicActivityTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_learner_reads_published_progress_for_matching_plans(): void
    {
        $school = $this->workingSchool();
        $enrollment = $this->enrollment();
        $this->createPublishedGraduationPlan($school, $enrollment);

        $this->actingAsMemberOf($school, $enrollment->user)
            ->get(route('portal.graduation.show', $enrollment))
            ->assertOk()
            ->assertSee('Class of 2027 Diploma')
            ->assertSee('Every learner diploma')
            ->assertDontSee('Class of 2028 Diploma')
            ->assertSee('4 of 4 credits')
            ->assertSee('Requirements met')
            ->assertSee('75.00%');
    }

    public function test_a_guardian_reads_published_progress_for_their_child(): void
    {
        $school = $this->workingSchool();
        $enrollment = $this->enrollment();
        $this->createPublishedGraduationPlan($school, $enrollment);
        $guardian = $this->guardianOf($enrollment);

        $this->actingAsMemberOf($school, $guardian)
            ->get(route('portal.graduation.show', $enrollment))
            ->assertOk()
            ->assertSee('Class of 2027 Diploma')
            ->assertSee('Pass Mathematics');
    }

    public function test_a_guardian_sees_their_childs_club_places_without_staff_notes_or_support_programmes(): void
    {
        $school = $this->workingSchool();
        $enrollment = $this->enrollment();
        $club = Program::create([
            'school_id' => $school->id,
            'name' => 'Robotics club',
            'type' => ProgramType::Club,
            'description' => 'Build and program small robots.',
        ]);
        ProgramParticipation::create([
            'school_id' => $school->id,
            'program_id' => $club->id,
            'student_record_id' => $enrollment->id,
            'status' => ParticipationStatus::Active,
            'schedule' => 'Tuesday, 3:30–4:30 PM',
            'note' => 'Staff-only note: check in privately.',
        ]);
        $support = Program::create([
            'school_id' => $school->id,
            'name' => 'Reading intervention',
            'type' => ProgramType::Intervention,
        ]);
        ProgramParticipation::create([
            'school_id' => $school->id,
            'program_id' => $support->id,
            'student_record_id' => $enrollment->id,
            'status' => ParticipationStatus::Active,
        ]);

        $guardian = $this->guardianOf($enrollment);

        $this->actingAs($guardian)
            ->get(route('portal.programmes.index', $enrollment))
            ->assertOk()
            ->assertSee('Robotics club')
            ->assertSee('Taking part')
            ->assertSee('Tuesday, 3:30–4:30 PM')
            ->assertDontSee('Staff-only note')
            ->assertDontSee('Reading intervention');
    }

    public function test_a_person_cannot_read_an_unrelated_enrollment(): void
    {
        $enrollment = $this->enrollment();
        $stranger = $this->memberOf($this->workingSchool());

        $this->actingAs($stranger)
            ->get(route('portal.graduation.show', $enrollment))
            ->assertForbidden();
    }

    public function test_a_learner_cannot_read_an_inactive_enrollment(): void
    {
        $enrollment = $this->enrollment();
        $enrollment->update(['status' => EnrollmentStatus::Withdrawn]);

        $this->actingAsMemberOf($this->workingSchool(), $enrollment->user)
            ->get(route('portal.programmes.index', $enrollment))
            ->assertForbidden();
    }

    public function test_the_school_can_close_each_activity_portal_area_and_module(): void
    {
        $school = $this->workingSchool();
        $enrollment = $this->enrollment();

        features()->enable(Feature::Portal, $school->id, config: [
            PortalArea::Graduation->value => false,
            PortalArea::Programmes->value => false,
        ]);

        $this->actingAs($enrollment->user)
            ->get(route('portal.graduation.show', $enrollment))
            ->assertNotFound();
        $this->get(route('portal.programmes.index', $enrollment))->assertNotFound();

        features()->enable(Feature::Portal, $school->id, config: [
            PortalArea::Graduation->value => true,
            PortalArea::Programmes->value => true,
        ]);
        features()->disable(Feature::GraduationPlans, $school->id);
        features()->disable(Feature::Programmes, $school->id);

        $this->get(route('portal.graduation.show', $enrollment))->assertNotFound();
        $this->get(route('portal.programmes.index', $enrollment))->assertNotFound();
    }

    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    private function createPublishedGraduationPlan(School $school, StudentRecord $enrollment): void
    {
        $cohort = Cohort::create(['school_id' => $school->id, 'name' => 'Class of 2027 Graduates']);
        CohortMember::create([
            'cohort_id' => $cohort->id,
            'student_record_id' => $enrollment->id,
            'joined_on' => now()->toDateString(),
        ]);
        $plan = GraduationPlan::create([
            'school_id' => $school->id,
            'cohort_id' => $cohort->id,
            'name' => 'Class of 2027 Diploma',
            'uses_credits' => true,
            'required_credits' => 4,
        ]);
        GraduationPlan::create([
            'school_id' => $school->id,
            'name' => 'Every learner diploma',
        ]);
        $otherCohort = Cohort::create(['school_id' => $school->id, 'name' => 'Class of 2028 Graduates']);
        GraduationPlan::create([
            'school_id' => $school->id,
            'cohort_id' => $otherCohort->id,
            'name' => 'Class of 2028 Diploma',
        ]);

        $subject = Subject::factory()->create(['school_id' => $school->id, 'name' => 'Mathematics']);
        GraduationRequirement::create([
            'graduation_plan_id' => $plan->id,
            'subject_id' => $subject->id,
            'description' => 'Pass Mathematics',
            'credits' => 4,
            'pass_mark' => 50,
        ]);
        $this->publishResult($enrollment, $subject, 75);
    }

    private function guardianOf(StudentRecord $enrollment): User
    {
        $guardian = $this->memberOf($this->workingSchool());
        $guardian->parentRecord()->create(['user_id' => $guardian->id]);
        $guardian->refresh()->parentRecord->students()->syncWithoutDetaching($enrollment->user);

        return $guardian->fresh();
    }

    private function publishResult(StudentRecord $enrollment, Subject $subject, float $percentage): void
    {
        $offering = CourseOffering::factory()->create([
            'school_id' => $enrollment->school_id,
            'subject_id' => $subject->id,
            'academic_year_id' => current_academic_year_id(),
            'academic_period_id' => current_academic_period_id(),
        ]);

        ResultSnapshot::create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'course_offering_id' => $offering->id,
            'revision' => 1,
            'percentage' => $percentage,
            'payload' => [],
            'published_at' => now(),
        ]);
    }
}
