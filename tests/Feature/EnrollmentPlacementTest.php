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
use App\Models\EnrollmentPlacement;
use App\Models\School;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Where a student sits is a cycle-section history, never two mutable legacy pointers.
 */
class EnrollmentPlacementTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_placement_is_written_to_the_history_and_current_summary(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $actor = $this->memberOf($this->workingSchool());
        $cycleSection = $this->cycleSection($this->workingSchool());

        app(ChangeEnrollmentPlacement::class)->place($enrollment, $cycleSection, actor: $actor, reason: 'Admission');

        $enrollment = $enrollment->fresh();
        $placement = $enrollment->placements()->firstOrFail();
        $academicYearRecord = $enrollment->academicYears()->whereKey($cycleSection->academic_year_id)->firstOrFail();

        $this->assertSame($cycleSection->id, $placement->academic_cycle_section_id);
        $this->assertSame($cycleSection->academic_year_id, $placement->academic_year_id);
        $this->assertSame($cycleSection->id, $enrollment->academic_cycle_section_id);
        $this->assertSame($cycleSection->id, $academicYearRecord->studentAcademicYearBasedRecords->academic_cycle_section_id);
        $this->assertSame($actor->id, $placement->changed_by);
        $this->assertSame('Admission', $placement->reason);
    }

    public function test_the_enrollment_points_at_the_newest_cycle_section(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        $first = $this->cycleSection($this->workingSchool(), $academicYear);
        $second = $this->cycleSection($this->workingSchool(), $academicYear);

        $action = app(ChangeEnrollmentPlacement::class);
        $action->place($enrollment, $first);
        $action->place($enrollment, $second, reason: 'Promotion');

        $enrollment = $enrollment->fresh();

        $this->assertSame(2, $enrollment->placements()->count());
        $this->assertSame($second->id, $enrollment->currentPlacement->academic_cycle_section_id);
        $this->assertSame($second->id, $enrollment->academic_cycle_section_id);
    }

    public function test_repeating_a_placement_adds_no_second_record(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $cycleSection = $this->cycleSection($this->workingSchool());

        $action = app(ChangeEnrollmentPlacement::class);
        $action->place($enrollment, $cycleSection);
        $action->place($enrollment, $cycleSection);

        $this->assertSame(1, $enrollment->fresh()->placements()->count());
    }

    public function test_an_inactive_cycle_section_cannot_receive_a_placement(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $cycleSection = $this->cycleSection($this->workingSchool());
        $cycleSection->update(['status' => AcademicStructureStatus::Draft]);

        $this->expectException(InvalidValueException::class);

        app(ChangeEnrollmentPlacement::class)->place($enrollment, $cycleSection);
    }

    public function test_a_cycle_section_of_another_school_is_refused(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $cycleSection = $this->cycleSection(School::factory()->create());

        $this->expectException(InvalidValueException::class);

        app(ChangeEnrollmentPlacement::class)->place($enrollment, $cycleSection);
    }

    public function test_a_closed_academic_year_refuses_a_placement(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        $cycleSection = $this->cycleSection($this->workingSchool(), $academicYear);
        app(ChangeAcademicPeriodStatus::class)->close($academicYear);

        $this->expectException(InvalidValueException::class);

        app(ChangeEnrollmentPlacement::class)->place($enrollment, $cycleSection);
    }

    public function test_a_closed_enrollment_refuses_a_placement(): void
    {
        $enrollment = StudentRecord::factory()->create();
        app(ChangeEnrollmentStatus::class)->graduate($enrollment);

        $this->expectException(InvalidValueException::class);

        app(ChangeEnrollmentPlacement::class)->place($enrollment->fresh(), $this->cycleSection($this->workingSchool()));
    }

    public function test_placement_history_cannot_be_changed_or_deleted(): void
    {
        $placement = $this->placementFor(StudentRecord::factory()->create());

        try {
            $placement->update(['reason' => 'a better reason']);
            $this->fail('Placement history update should fail.');
        } catch (RuntimeException) {
            $this->assertModelExists($placement);
        }

        $this->expectException(RuntimeException::class);
        $placement->delete();
    }

    public function test_a_placement_is_recorded_in_the_audit_log(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $this->placementFor($enrollment);

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::EnrollmentPlaced)->forSubject($enrollment)->first());
    }

    public function test_a_person_can_hold_two_enrollments_at_once(): void
    {
        $school = $this->workingSchool();
        $other = School::factory()->create();
        $enrollment = StudentRecord::factory()->create(['school_id' => $school->id]);
        $student = $enrollment->user;
        $second = StudentRecord::factory()->create(['user_id' => $student->id, 'school_id' => $other->id, 'is_primary' => false]);

        $this->assertSame(2, $student->studentRecords()->count());

        school_context()->set($school, remember: false);
        $this->assertSame($enrollment->id, $student->fresh()->studentRecord->id);

        school_context()->set($other, remember: false);
        $this->assertSame($second->id, $student->fresh()->studentRecord->id);
    }

    public function test_a_transfer_closes_the_old_enrollment_and_opens_a_new_one(): void
    {
        $source = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $destination = School::factory()->create();
        $actor = $this->memberOf($this->workingSchool());
        $cycleSection = $this->cycleSection($destination);

        $transferred = app(TransferEnrollment::class)->transfer($source, $destination, $cycleSection, $actor, 'Family moved');

        $this->assertSame(EnrollmentStatus::Transferred, $source->fresh()->status);
        $this->assertSame($destination->id, $transferred->school_id);
        $this->assertSame($cycleSection->id, $transferred->academic_cycle_section_id);
        $this->assertSame($source->id, $transferred->transferred_from_id);
        $this->assertNotNull($transferred->admission_number);
    }

    public function test_a_transfer_keeps_the_history_of_the_old_enrollment(): void
    {
        $source = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->placementFor($source);

        app(TransferEnrollment::class)->transfer($source, School::factory()->create());

        $this->assertSame(1, $source->fresh()->placements()->count());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::EnrollmentTransferred)->first());
    }

    private function placementFor(StudentRecord $enrollment): EnrollmentPlacement
    {
        app(ChangeEnrollmentPlacement::class)->place($enrollment, $this->cycleSection($enrollment->school ?? $this->workingSchool()));

        return $enrollment->fresh()->placements()->firstOrFail();
    }

    private function cycleSection(School $school, ?AcademicYear $academicYear = null): AcademicCycleSection
    {
        $academicYear ??= AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);

        return AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]);
    }
}
