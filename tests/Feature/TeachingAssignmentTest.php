<?php

namespace Tests\Feature;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Actions\Curriculum\AssignTeacher;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Enums\TeachingRole;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\ClassGroup;
use App\Models\MyClass;
use App\Models\School;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Services\Subject\SubjectService;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Teaching is a dated assignment, so several teachers can share a subject.
 */
class TeachingAssignmentTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_teacher_takes_a_subject_for_the_working_year(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $teacher = $this->teacher();

        $assignment = app(AssignTeacher::class)->assign($subject, $teacher);

        $this->assertSame($subject->id, $assignment->subject_id);
        $this->assertSame($teacher->id, $assignment->user_id);
        $this->assertSame(current_academic_year_id(), $assignment->academic_year_id);
        $this->assertSame(TeachingRole::Lead, $assignment->role);
        $this->assertTrue($assignment->isRunningOn());
        $this->assertTrue($subject->fresh()->teachers->contains($teacher));
    }

    public function test_two_teachers_can_share_one_subject(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $lead = $this->teacher();
        $support = $this->teacher();

        $action = app(AssignTeacher::class);
        $action->assign($subject, $lead);
        $action->assign($subject, $support, TeachingRole::Supporting);

        $this->assertSame(2, TeachingAssignment::where('subject_id', $subject->id)->runningOn()->count());
        $this->assertSame(2, $subject->fresh()->teachers->count());
    }

    public function test_asking_twice_returns_the_same_assignment(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $teacher = $this->teacher();

        $action = app(AssignTeacher::class);
        $first = $action->assign($subject, $teacher);
        $second = $action->assign($subject, $teacher);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, TeachingAssignment::where('subject_id', $subject->id)->count());
    }

    public function test_an_assignment_can_cover_one_section(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $section = Section::factory()->create(['my_class_id' => $subject->my_class_id]);

        $assignment = app(AssignTeacher::class)->assign($subject, $this->teacher(), TeachingRole::Lead, $section);

        $this->assertSame($section->id, $assignment->section_id);
    }

    public function test_a_section_outside_the_class_is_refused(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $otherClass = MyClass::factory()->create(['class_group_id' => ClassGroup::factory()->create(['school_id' => $this->workingSchool()->id])->id]);
        $section = Section::factory()->create(['my_class_id' => $otherClass->id]);

        $this->expectException(InvalidValueException::class);

        app(AssignTeacher::class)->assign($subject, $this->teacher(), TeachingRole::Lead, $section);
    }

    public function test_a_person_who_is_not_a_teacher_is_refused(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $person = $this->memberOf($this->workingSchool());

        $this->expectException(InvalidValueException::class);

        app(AssignTeacher::class)->assign($subject, $person);
    }

    public function test_a_teacher_of_another_school_is_refused(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $other = School::factory()->create();
        $stranger = $this->memberOf($other);
        school_context()->set($other, remember: false);
        $stranger->assignRole(Role::Teacher->value);
        school_context()->set($this->workingSchool(), remember: false);

        $this->expectException(InvalidValueException::class);

        app(AssignTeacher::class)->assign($subject, $stranger);
    }

    public function test_a_closed_year_refuses_a_new_assignment(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $year = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        app(ChangeAcademicPeriodStatus::class)->close($year);

        $this->expectException(InvalidValueException::class);

        app(AssignTeacher::class)->assign($subject, $this->teacher(), TeachingRole::Lead, null, $year->fresh());
    }

    public function test_ending_an_assignment_keeps_the_record(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $teacher = $this->teacher();
        $action = app(AssignTeacher::class);
        $assignment = $action->assign($subject, $teacher);

        $action->end($assignment);

        $assignment = $assignment->fresh();

        $this->assertNotNull($assignment->ends_on);
        $this->assertFalse($assignment->isRunningOn(now()->addDay()));
        $this->assertFalse($subject->fresh()->teachers->contains($teacher));
        $this->assertDatabaseHas('teaching_assignments', ['id' => $assignment->id]);
    }

    public function test_ending_an_assignment_twice_changes_nothing(): void
    {
        $this->authorized_user([]);
        $action = app(AssignTeacher::class);
        $assignment = $action->assign($this->subject(), $this->teacher());

        $action->end($assignment, now()->subDay());
        $ended = $assignment->fresh()->ends_on;
        $action->end($assignment->fresh());

        $this->assertSame($ended->toDateString(), $assignment->fresh()->ends_on->toDateString());
    }

    public function test_assignments_are_written_to_the_audit_log(): void
    {
        $this->authorized_user([]);
        $assignment = app(AssignTeacher::class)->assign($this->subject(), $this->teacher());

        $this->assertNotNull(
            AuditEvent::ofAction(AuditAction::TeachingAssignmentCreated)->forSubject($assignment)->first()
        );
    }

    public function test_the_subject_screen_writes_assignments(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $teacher = $this->teacher();

        app(SubjectService::class)->syncTeachers($subject, [$teacher->id]);

        $this->assertSame(1, TeachingAssignment::where('subject_id', $subject->id)->runningOn()->count());
    }

    public function test_removing_a_teacher_from_the_screen_ends_the_assignment(): void
    {
        $this->authorized_user([]);
        $subject = $this->subject();
        $teacher = $this->teacher();
        $service = app(SubjectService::class);
        $service->syncTeachers($subject, [$teacher->id]);

        $service->syncTeachers($subject, []);

        $this->assertSame(0, TeachingAssignment::where('subject_id', $subject->id)->runningOn(now()->addDay())->count());
        $this->assertSame(1, TeachingAssignment::where('subject_id', $subject->id)->count());
    }

    /**
     * Create a subject in the working school.
     */
    private function subject(): Subject
    {
        $classGroup = ClassGroup::factory()->create(['school_id' => $this->workingSchool()->id]);
        $class = MyClass::factory()->create(['class_group_id' => $classGroup->id]);

        return Subject::factory()->create([
            'school_id'   => $this->workingSchool()->id,
            'my_class_id' => $class->id,
        ]);
    }

    /**
     * Create a teacher of the working school.
     */
    private function teacher(): User
    {
        $teacher = $this->memberOf($this->workingSchool());
        $teacher->assignRole(Role::Teacher->value);

        return $teacher->fresh();
    }
}
