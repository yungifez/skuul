<?php

namespace Tests\Feature;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Actions\Enrollment\ChangeEnrollmentStatus;
use App\Actions\Enrollment\TransferEnrollment;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\ClassGroup;
use App\Models\EnrollmentPlacement;
use App\Models\MyClass;
use App\Models\School;
use App\Models\Section;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Where a student sits is a history, not one changing field.
 */
class EnrollmentPlacementTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_placement_is_written_to_the_history(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $actor = $this->memberOf($this->workingSchool());
        [$class, $section] = $this->classAndSection($this->workingSchool());

        app(ChangeEnrollmentPlacement::class)->place($enrollment, $class, $section, actor: $actor, reason: 'Admission');

        $placement = $enrollment->fresh()->placements()->firstOrFail();

        $this->assertSame($class->id, $placement->my_class_id);
        $this->assertSame($section->id, $placement->section_id);
        $this->assertSame(current_academic_year_id(), $placement->academic_year_id);
        $this->assertNull($placement->academic_cycle_section_id);
        $this->assertSame($actor->id, $placement->changed_by);
        $this->assertSame('Admission', $placement->reason);
    }

    public function test_the_enrollment_points_at_the_newest_placement(): void
    {
        $enrollment = StudentRecord::factory()->create();
        [$first, $firstSection] = $this->classAndSection($this->workingSchool());
        [$second, $secondSection] = $this->classAndSection($this->workingSchool());

        $action = app(ChangeEnrollmentPlacement::class);
        $action->place($enrollment, $first, $firstSection);
        $action->place($enrollment, $second, $secondSection, reason: 'Promotion');

        $enrollment = $enrollment->fresh();

        $this->assertSame(2, $enrollment->placements()->count());
        $this->assertSame($second->id, $enrollment->currentPlacement->my_class_id);
        $this->assertSame($second->id, $enrollment->my_class_id);
        $this->assertSame($secondSection->id, $enrollment->section_id);
    }

    public function test_repeating_a_placement_adds_no_second_record(): void
    {
        $enrollment = StudentRecord::factory()->create();
        [$class, $section] = $this->classAndSection($this->workingSchool());

        $action = app(ChangeEnrollmentPlacement::class);
        $action->place($enrollment, $class, $section);
        $action->place($enrollment, $class, $section);

        $this->assertSame(1, $enrollment->fresh()->placements()->count());
    }

    public function test_a_placement_can_name_its_exact_active_cycle_section(): void
    {
        $enrollment = StudentRecord::factory()->create();
        [$class, $section] = $this->classAndSection($this->workingSchool());
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        $cycleSection = $this->cycleSection($academicYear, $class, $section);

        app(ChangeEnrollmentPlacement::class)->place(
            $enrollment,
            $class,
            $section,
            academicYear: $academicYear,
            academicCycleSection: $cycleSection,
        );

        $enrollment = $enrollment->fresh();
        $placement = $enrollment->currentPlacement;
        $academicYearRecord = $enrollment->academicYears()->whereKey($academicYear)->firstOrFail();

        $this->assertSame($cycleSection->id, $placement->academic_cycle_section_id);
        $this->assertSame($cycleSection->id, $enrollment->academic_cycle_section_id);
        $this->assertSame($cycleSection->id, $academicYearRecord->studentAcademicYearBasedRecords->academic_cycle_section_id);

        app(ChangeEnrollmentPlacement::class)->place($enrollment, $class, $section, academicYear: $academicYear);

        $this->assertSame(1, $enrollment->fresh()->placements()->count());
        $this->assertSame($cycleSection->id, $enrollment->fresh()->academic_cycle_section_id);
    }

    public function test_an_inactive_cycle_section_cannot_receive_a_placement(): void
    {
        $enrollment = StudentRecord::factory()->create();
        [$class, $section] = $this->classAndSection($this->workingSchool());
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        $cycleSection = $this->cycleSection($academicYear, $class, $section);
        $cycleSection->update(['status' => AcademicStructureStatus::Draft]);

        $this->expectException(InvalidValueException::class);

        app(ChangeEnrollmentPlacement::class)->place(
            $enrollment,
            $class,
            $section,
            academicYear: $academicYear,
            academicCycleSection: $cycleSection,
        );
    }

    public function test_a_cycle_section_requires_matching_legacy_class_and_section_bridges(): void
    {
        $enrollment = StudentRecord::factory()->create();
        [$class, $section] = $this->classAndSection($this->workingSchool());
        $otherSection = Section::factory()->create(['my_class_id' => $class->id]);
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        $cycleSection = $this->cycleSection($academicYear, $class, $section);

        $this->expectException(InvalidValueException::class);

        app(ChangeEnrollmentPlacement::class)->place(
            $enrollment,
            $class,
            $otherSection,
            academicYear: $academicYear,
            academicCycleSection: $cycleSection,
        );
    }

    public function test_a_section_outside_the_class_is_refused(): void
    {
        $enrollment = StudentRecord::factory()->create();
        [$class] = $this->classAndSection($this->workingSchool());
        [, $otherSection] = $this->classAndSection($this->workingSchool());

        $this->expectException(InvalidValueException::class);

        app(ChangeEnrollmentPlacement::class)->place($enrollment, $class, $otherSection);
    }

    public function test_a_class_of_another_school_is_refused(): void
    {
        $enrollment = StudentRecord::factory()->create();
        [$class, $section] = $this->classAndSection(School::factory()->create());

        $this->expectException(InvalidValueException::class);

        app(ChangeEnrollmentPlacement::class)->place($enrollment, $class, $section);
    }

    public function test_a_closed_academic_year_refuses_a_placement(): void
    {
        $enrollment = StudentRecord::factory()->create();
        [$class, $section] = $this->classAndSection($this->workingSchool());
        $year = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        app(ChangeAcademicPeriodStatus::class)->close($year);

        $this->expectException(InvalidValueException::class);

        app(ChangeEnrollmentPlacement::class)->place($enrollment, $class, $section, $year->fresh());
    }

    public function test_a_closed_enrollment_refuses_a_placement(): void
    {
        $enrollment = StudentRecord::factory()->create();
        app(ChangeEnrollmentStatus::class)->graduate($enrollment);
        [$class, $section] = $this->classAndSection($this->workingSchool());

        $this->expectException(InvalidValueException::class);

        app(ChangeEnrollmentPlacement::class)->place($enrollment->fresh(), $class, $section);
    }

    public function test_placement_history_cannot_be_changed(): void
    {
        $placement = $this->placementFor(StudentRecord::factory()->create());

        $this->expectException(RuntimeException::class);

        $placement->update(['reason' => 'a better reason']);
    }

    public function test_placement_history_cannot_be_deleted(): void
    {
        $placement = $this->placementFor(StudentRecord::factory()->create());

        $this->expectException(RuntimeException::class);

        $placement->delete();
    }

    public function test_a_placement_is_recorded_in_the_audit_log(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $this->placementFor($enrollment);

        $this->assertNotNull(
            AuditEvent::ofAction(AuditAction::EnrollmentPlaced)->forSubject($enrollment)->first()
        );
    }

    public function test_a_person_can_hold_two_enrollments_at_once(): void
    {
        $school = $this->workingSchool();
        $other = School::factory()->create();
        $enrollment = StudentRecord::factory()->create(['school_id' => $school->id]);
        $student = $enrollment->user;

        $second = StudentRecord::factory()->create([
            'user_id' => $student->id,
            'school_id' => $other->id,
            'is_primary' => false,
        ]);

        $this->assertSame(2, $student->studentRecords()->count());

        school_context()->set($school, remember: false);
        $this->assertSame($enrollment->id, $student->fresh()->studentRecord->id);

        school_context()->set($other, remember: false);
        $this->assertSame($second->id, $student->fresh()->studentRecord->id);
    }

    public function test_creating_a_primary_enrollment_demotes_the_previous_primary(): void
    {
        $enrollment = StudentRecord::factory()->create(['is_primary' => true]);
        $student = $enrollment->user;

        $second = StudentRecord::factory()->create([
            'user_id' => $student->id,
            'school_id' => $this->workingSchool()->id,
            'is_primary' => true,
        ]);

        $this->assertFalse($enrollment->fresh()->is_primary);
        $this->assertTrue($second->fresh()->is_primary);
        $this->assertSame(1, $student->fresh()->studentRecords()->where('is_primary', true)->count());
    }

    public function test_a_transfer_closes_the_old_enrollment_and_opens_a_new_one(): void
    {
        $source = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $destination = School::factory()->create();
        $actor = $this->memberOf($this->workingSchool());

        $transferred = app(TransferEnrollment::class)->transfer($source, $destination, actor: $actor, reason: 'Family moved');

        $source = $source->fresh();

        $this->assertSame(EnrollmentStatus::Transferred, $source->status);
        $this->assertFalse($source->is_primary);
        $this->assertSame($destination->id, $transferred->school_id);
        $this->assertSame(EnrollmentStatus::Active, $transferred->status);
        $this->assertTrue($transferred->is_primary);
        $this->assertSame($source->id, $transferred->transferred_from_id);
        $this->assertNotNull($transferred->admission_number);
    }

    public function test_a_transfer_keeps_the_history_of_the_old_enrollment(): void
    {
        $source = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->placementFor($source);
        $destination = School::factory()->create();

        app(TransferEnrollment::class)->transfer($source, $destination);

        $this->assertSame(1, $source->fresh()->placements()->count());
        $this->assertNotNull(
            AuditEvent::ofAction(AuditAction::EnrollmentTransferred)->first()
        );
    }

    public function test_a_transfer_to_the_same_school_is_refused(): void
    {
        $school = $this->workingSchool();
        $source = StudentRecord::factory()->create(['school_id' => $school->id]);

        $this->expectException(InvalidValueException::class);

        app(TransferEnrollment::class)->transfer($source, $school);
    }

    /**
     * Create a class with one section in the given school.
     *
     * @return array{0: MyClass, 1: Section}
     */
    private function classAndSection(School $school): array
    {
        $classGroup = ClassGroup::factory()->create(['school_id' => $school->id]);
        $class = MyClass::factory()->create(['class_group_id' => $classGroup->id]);
        $section = Section::factory()->create(['my_class_id' => $class->id]);

        return [$class, $section];
    }

    /**
     * Place the enrollment somewhere and return the placement.
     */
    private function placementFor(StudentRecord $enrollment): EnrollmentPlacement
    {
        [$class, $section] = $this->classAndSection($enrollment->school ?? $this->workingSchool());

        app(ChangeEnrollmentPlacement::class)->place($enrollment, $class, $section);

        return $enrollment->fresh()->placements()->firstOrFail();
    }

    private function cycleSection(AcademicYear $academicYear, MyClass $class, Section $section): AcademicCycleSection
    {
        $academicLevel = AcademicLevel::factory()->create([
            'school_id' => $academicYear->school_id,
            'legacy_my_class_id' => $class->id,
        ]);

        return AcademicCycleSection::factory()->create([
            'school_id' => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'legacy_section_id' => $section->id,
            'status' => AcademicStructureStatus::Active,
        ]);
    }
}
