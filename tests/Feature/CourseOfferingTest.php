<?php

namespace Tests\Feature;

use App\Actions\Curriculum\AssignTeacher;
use App\Actions\Curriculum\ChangeCourseOfferingStatus;
use App\Actions\Curriculum\CreateCourseOffering;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\CourseOfferingStatus;
use App\Enums\InstructionalModel;
use App\Enums\Role;
use App\Enums\RosterMode;
use App\Enums\TeachingRole;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\InstructionalModelSetting;
use App\Models\School;
use App\Models\SchoolOperatingProfile;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Policies\CourseOfferingPolicy;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseOfferingTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_course_offering_keeps_its_exact_period_and_default_home_sections(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();

        $courseOffering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [$cycleSection->id],
        );

        $this->assertSame(CourseOfferingStatus::Draft, $courseOffering->status);
        $this->assertSame($academicPeriod->id, $courseOffering->academic_period_id);
        $this->assertSame([$cycleSection->id], $courseOffering->cycleSections->modelKeys());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::CourseOfferingCreated)->forSubject($courseOffering)->first());
    }

    public function test_a_course_offering_refuses_a_section_outside_its_academic_level(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext();
        $otherLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id]);
        $otherSection = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $otherLevel->id,
        ]);

        $this->expectException(InvalidValueException::class);

        app(CreateCourseOffering::class)->create($subject, $academicYear, $academicPeriod, $academicLevel, [$otherSection->id]);
    }

    public function test_only_one_matching_active_offering_can_exist(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();
        $create = app(CreateCourseOffering::class);
        $first = $create->create($subject, $academicYear, $academicPeriod, $academicLevel, [$cycleSection->id]);
        $second = $create->create($subject, $academicYear, $academicPeriod, $academicLevel, [$cycleSection->id]);

        app(ChangeCourseOfferingStatus::class)->change($first, CourseOfferingStatus::Active);

        $this->expectException(InvalidValueException::class);

        app(ChangeCourseOfferingStatus::class)->change($second, CourseOfferingStatus::Active);
    }

    public function test_an_offering_cannot_activate_until_its_period_is_operational(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext(AcademicPeriodStatus::Scheduled);
        $courseOffering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [],
            RosterMode::AcademicLevel,
        );

        $this->expectException(InvalidValueException::class);

        app(ChangeCourseOfferingStatus::class)->change($courseOffering, CourseOfferingStatus::Active);
    }

    public function test_a_subject_schedule_can_use_a_named_learner_roster(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();
        InstructionalModelSetting::create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $academicYear->id,
            'model' => InstructionalModel::SubjectBasedSchedule,
        ]);
        /** @var User $student */
        $student = User::factory()->create();
        $studentRecord = StudentRecord::create([
            'user_id' => $student->id,
            'school_id' => $this->workingSchool()->id,
            'academic_cycle_section_id' => $cycleSection->id,
            'admission_number' => fake()->unique()->bothify('####????'),
            'admission_date' => now()->toDateString(),
        ]);

        $courseOffering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [],
            RosterMode::IndividualRoster,
            [$studentRecord->id],
        );

        $this->assertSame(RosterMode::IndividualRoster, $courseOffering->roster_mode);
        $this->assertSame([$studentRecord->id], $courseOffering->studentRecords->modelKeys());
        $this->assertTrue($courseOffering->cycleSections->isEmpty());
    }

    public function test_a_course_offering_keeps_its_teacher_assignment(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();
        $courseOffering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [$cycleSection->id],
        );
        $teacher = $this->memberOf($this->workingSchool());
        $teacher->assignRole(Role::Teacher->value);

        $assignment = app(AssignTeacher::class)->assign(
            $courseOffering,
            $teacher,
            TeachingRole::Lead,
        );

        $this->assertSame($courseOffering->id, $assignment->course_offering_id);
        $this->assertSame($teacher->id, $courseOffering->fresh()->teachingAssignments()->sole()->user_id);
        $this->assertSame(1, TeachingAssignment::where('course_offering_id', $courseOffering->id)->count());
    }

    public function test_a_curriculum_manager_can_create_and_view_course_offerings(): void
    {
        $this->authorized_user(['create subject', 'read subject']);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();
        $secondPeriod = AcademicPeriod::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $academicYear->id,
            'position' => 2,
        ]);
        SchoolOperatingProfile::query()->updateOrCreate(
            ['school_id' => $this->workingSchool()->id],
            ['preset' => 'home_sections', 'labels' => array_merge(SchoolOperatingProfile::labelsFor('home_sections'), ['section' => 'Stream'])],
        );

        $this->get(route('course-offerings.create'))
            ->assertOk()
            ->assertSee('Who attends')
            ->assertSee('One stream')
            ->assertSee('Combined streams')
            ->assertSee('All '.strtolower(school_terms('period', 'periods')).' in the '.strtolower(school_term('academic_year', 'school year')))
            ->assertDontSee('home section');

        $this->post(route('course-offerings.store'), [
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => 'all',
            'subject_id' => $subject->id,
            'academic_level_id' => $academicLevel->id,
            'roster_mode' => RosterMode::HomeSection->value,
            'academic_cycle_section_ids' => [$cycleSection->id],
            'planned_periods_per_week' => 5,
        ])->assertRedirect(route('course-offerings.index'));

        $this->assertSame(2, CourseOffering::query()
            ->where('school_id', $this->workingSchool()->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('subject_id', $subject->id)
            ->count());
        $this->assertDatabaseHas('course_offerings', [
            'school_id' => $this->workingSchool()->id,
            'academic_period_id' => $academicPeriod->id,
            'subject_id' => $subject->id,
        ]);
        $this->assertDatabaseHas('course_offerings', [
            'school_id' => $this->workingSchool()->id,
            'academic_period_id' => $secondPeriod->id,
            'subject_id' => $subject->id,
        ]);

        $this->get(route('course-offerings.index'))->assertOk()->assertSee($subject->name);
    }

    public function test_a_school_user_cannot_update_an_offering_from_another_school(): void
    {
        $this->authorized_user(['update subject']);
        /** @var School $otherSchool */
        $otherSchool = School::factory()->create();
        school_context()->set($otherSchool, remember: false);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext(school: $otherSchool);
        $courseOffering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [],
            RosterMode::AcademicLevel,
        );
        school_context()->set($this->workingSchool(), remember: false);

        $this->assertFalse(app(CourseOfferingPolicy::class)->update(auth()->user(), $courseOffering));
    }

    /**
     * @return array{Subject, AcademicYear, AcademicPeriod, AcademicLevel, AcademicCycleSection}
     */
    private function courseContext(AcademicPeriodStatus $periodStatus = AcademicPeriodStatus::Open, ?School $school = null): array
    {
        $school ??= $this->workingSchool();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        /** @var Subject $subject */
        $subject = Subject::factory()->create([
            'school_id' => $school->id,
        ]);
        /** @var AcademicYear $academicYear */
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        /** @var AcademicPeriod $academicPeriod */
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'status' => $periodStatus,
        ]);
        $cycleSection = AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]);

        return [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection];
    }
}
