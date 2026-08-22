<?php

namespace Tests\Feature;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Actions\Curriculum\AssignTeacher;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Enums\TeachingRole;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\School;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Teaching belongs to one dated course offering, not a subject catalog entry.
 */
class TeachingAssignmentTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_teacher_takes_a_course_offering_for_its_academic_period(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $teacher = $this->teacher();

        $assignment = app(AssignTeacher::class)->assign($courseOffering, $teacher);

        $this->assertSame($courseOffering->subject_id, $assignment->subject_id);
        $this->assertSame($courseOffering->id, $assignment->course_offering_id);
        $this->assertSame($courseOffering->academic_period_id, $assignment->academic_period_id);
        $this->assertSame($teacher->id, $assignment->user_id);
        $this->assertSame(TeachingRole::Lead, $assignment->role);
        $this->assertTrue($assignment->isRunningOn());
    }

    public function test_two_teachers_can_share_one_course_offering(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();

        $action = app(AssignTeacher::class);
        $action->assign($courseOffering, $this->teacher());
        $action->assign($courseOffering, $this->teacher(), TeachingRole::Supporting);

        $this->assertSame(2, TeachingAssignment::query()->where('course_offering_id', $courseOffering->id)->runningOn()->count());
    }

    public function test_asking_twice_returns_the_same_assignment(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $teacher = $this->teacher();

        $action = app(AssignTeacher::class);
        $first = $action->assign($courseOffering, $teacher);
        $second = $action->assign($courseOffering, $teacher);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, TeachingAssignment::query()->where('course_offering_id', $courseOffering->id)->count());
    }

    public function test_an_assignment_can_cover_one_home_section_of_its_offering(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $cycleSection = $this->cycleSection($courseOffering);

        $assignment = app(AssignTeacher::class)->assign($courseOffering, $this->teacher(), TeachingRole::Lead, $cycleSection);

        $this->assertSame($cycleSection->id, $assignment->academic_cycle_section_id);
    }

    public function test_a_home_section_in_another_school_is_refused(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        $otherSchool = School::query()->findOrFail(School::factory()->create()->getKey());
        $year = AcademicYear::query()->findOrFail(AcademicYear::factory()->create(['school_id' => $otherSchool->id])->getKey());
        $level = AcademicLevel::query()->findOrFail(AcademicLevel::factory()->create(['school_id' => $otherSchool->id])->getKey());
        $cycleSection = AcademicCycleSection::query()->findOrFail(AcademicCycleSection::factory()->create([
            'school_id' => $otherSchool->id,
            'academic_year_id' => $year->id,
            'academic_level_id' => $level->id,
        ])->getKey());

        $this->expectException(InvalidValueException::class);

        app(AssignTeacher::class)->assign($courseOffering, $this->teacher(), TeachingRole::Lead, $cycleSection);
    }

    public function test_a_person_who_is_not_a_teacher_is_refused(): void
    {
        $this->authorized_user([]);
        $person = $this->memberOf($this->workingSchool());

        $this->expectException(InvalidValueException::class);

        app(AssignTeacher::class)->assign($this->courseOffering(), $person);
    }

    public function test_a_teacher_of_another_school_is_refused(): void
    {
        $this->authorized_user([]);
        $other = School::query()->findOrFail(School::factory()->create()->getKey());
        $stranger = $this->memberOf($other);
        school_context()->set($other, remember: false);
        $stranger->assignRole(Role::Teacher->value);
        school_context()->set($this->workingSchool(), remember: false);

        $this->expectException(InvalidValueException::class);

        app(AssignTeacher::class)->assign($this->courseOffering(), $stranger);
    }

    public function test_a_closed_academic_cycle_refuses_a_new_assignment(): void
    {
        $this->authorized_user([]);
        $courseOffering = $this->courseOffering();
        app(ChangeAcademicPeriodStatus::class)->close($courseOffering->academicYear);

        $this->expectException(InvalidValueException::class);

        app(AssignTeacher::class)->assign($courseOffering->fresh(), $this->teacher());
    }

    public function test_ending_an_assignment_keeps_the_record(): void
    {
        $this->authorized_user([]);
        $action = app(AssignTeacher::class);
        $assignment = $action->assign($this->courseOffering(), $this->teacher());

        $action->end($assignment);

        $assignment = $assignment->fresh();

        $this->assertNotNull($assignment->ends_on);
        $this->assertFalse($assignment->isRunningOn(now()->addDay()));
        $this->assertDatabaseHas('teaching_assignments', ['id' => $assignment->id]);
    }

    public function test_ending_an_assignment_twice_changes_nothing(): void
    {
        $this->authorized_user([]);
        $action = app(AssignTeacher::class);
        $assignment = $action->assign($this->courseOffering(), $this->teacher());

        $action->end($assignment, now()->subDay());
        $ended = $assignment->fresh()->ends_on;
        $action->end($assignment->fresh());

        $this->assertSame($ended->toDateString(), $assignment->fresh()->ends_on->toDateString());
    }

    public function test_assignments_are_written_to_the_audit_log(): void
    {
        $this->authorized_user([]);
        $assignment = app(AssignTeacher::class)->assign($this->courseOffering(), $this->teacher());

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::TeachingAssignmentCreated)->forSubject($assignment)->first());
    }

    public function test_the_subject_teacher_pivot_and_bulk_assignment_route_are_gone(): void
    {
        $this->assertFalse(Schema::hasTable('subject_user'));
        $this->assertFalse(Route::has('subjects.assign-teacher'));
        $this->assertFalse(Route::has('subjects.assign-teacher-to-subject'));
    }

    private function courseOffering(): CourseOffering
    {
        $academicYear = AcademicYear::query()->findOrFail(
            current_academic_year_id() ?? AcademicYear::factory()->create([
                'school_id' => $this->workingSchool()->id,
            ])->getKey()
        );
        $academicPeriod = AcademicPeriod::query()
            ->where('academic_year_id', $academicYear->id)
            ->firstOr(fn (): AcademicPeriod => AcademicPeriod::factory()->create([
                'school_id' => $this->workingSchool()->id,
                'academic_year_id' => $academicYear->id,
            ]));
        $academicLevel = AcademicLevel::query()->findOrFail(AcademicLevel::factory()->create([
            'school_id' => $this->workingSchool()->id,
        ])->getKey());
        $subject = Subject::query()->findOrFail(Subject::factory()->create([
            'school_id' => $this->workingSchool()->id,
        ])->getKey());

        return CourseOffering::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => $academicLevel->id,
            'subject_id' => $subject->id,
        ]);
    }

    private function cycleSection(CourseOffering $courseOffering): AcademicCycleSection
    {
        $cycleSection = AcademicCycleSection::query()->findOrFail(AcademicCycleSection::factory()->create([
            'school_id' => $courseOffering->school_id,
            'academic_year_id' => $courseOffering->academic_year_id,
            'academic_level_id' => $courseOffering->academic_level_id,
        ])->getKey());
        $courseOffering->cycleSections()->attach($cycleSection);

        return $cycleSection;
    }

    private function teacher(): User
    {
        $teacher = $this->memberOf($this->workingSchool());
        $teacher->assignRole(Role::Teacher->value);

        return $teacher->fresh();
    }
}
