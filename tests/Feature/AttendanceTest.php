<?php

namespace Tests\Feature;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Actions\Attendance\RecordAttendance;
use App\Actions\Enrollment\ChangeEnrollmentStatus;
use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Services\Attendance\AttendanceSummary;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * The register says where a student was, and keeps every correction.
 */
class AttendanceTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_taking_the_register_writes_one_record_a_day(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();

        $record = app(RecordAttendance::class)->record($enrollment, AttendanceStatus::Present);

        $this->assertSame(AttendanceStatus::Present, $record->status);
        $this->assertSame(AttendanceKind::Daily, $record->kind);
        $this->assertSame(now()->toDateString(), $record->attended_on->toDateString());
        $this->assertSame(1, AttendanceRecord::where('student_record_id', $enrollment->id)->count());
    }

    public function test_taking_it_twice_corrects_it_and_keeps_the_first_answer(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $action = app(RecordAttendance::class);
        $action->record($enrollment, AttendanceStatus::Absent);

        $record = $action->record($enrollment, AttendanceStatus::Present, reason: 'The child was in the hall');

        $this->assertSame(AttendanceStatus::Present, $record->status);
        $this->assertSame(1, AttendanceRecord::where('student_record_id', $enrollment->id)->count());

        $change = $record->changes()->firstOrFail();

        $this->assertSame(AttendanceStatus::Absent, $change->from_status);
        $this->assertSame(AttendanceStatus::Present, $change->to_status);
        $this->assertSame('The child was in the hall', $change->reason);
    }

    public function test_the_same_answer_twice_is_not_a_correction(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $action = app(RecordAttendance::class);
        $action->record($enrollment, AttendanceStatus::Present);

        $record = $action->record($enrollment, AttendanceStatus::Present);

        $this->assertSame(0, $record->changes()->count());
    }

    public function test_a_correction_cannot_be_changed_or_deleted(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $action = app(RecordAttendance::class);
        $action->record($enrollment, AttendanceStatus::Absent);
        $change = $action->record($enrollment, AttendanceStatus::Present)->changes()->firstOrFail();

        $this->expectException(RuntimeException::class);

        $change->update(['reason' => 'Something else']);
    }

    public function test_the_daily_and_lesson_registers_are_separate(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $subject = $this->subject();
        $action = app(RecordAttendance::class);

        $action->record($enrollment, AttendanceStatus::Present);
        $action->record($enrollment, AttendanceStatus::Absent, kind: AttendanceKind::Period, subject: $subject);

        $this->assertSame(2, AttendanceRecord::where('student_record_id', $enrollment->id)->count());
        $this->assertSame(
            AttendanceStatus::Present,
            AttendanceRecord::where('student_record_id', $enrollment->id)->ofKind(AttendanceKind::Daily)->firstOrFail()->status
        );
    }

    public function test_a_lesson_register_needs_a_subject(): void
    {
        $this->authorized_user([]);

        $this->expectException(InvalidValueException::class);

        app(RecordAttendance::class)->record($this->enrollment(), AttendanceStatus::Present, kind: AttendanceKind::Period);
    }

    public function test_a_daily_register_refuses_a_subject(): void
    {
        $this->authorized_user([]);

        $this->expectException(InvalidValueException::class);

        app(RecordAttendance::class)->record($this->enrollment(), AttendanceStatus::Present, subject: $this->subject());
    }

    public function test_a_subject_of_another_school_is_refused(): void
    {
        $this->authorized_user([]);
        $other = School::factory()->create();
        $subject = Subject::factory()->create(['school_id' => $other->id]);

        $this->expectException(InvalidValueException::class);

        app(RecordAttendance::class)->record($this->enrollment(), AttendanceStatus::Present, kind: AttendanceKind::Period, subject: $subject);
    }

    public function test_a_future_day_is_refused(): void
    {
        $this->authorized_user([]);

        $this->expectException(InvalidValueException::class);

        app(RecordAttendance::class)->record($this->enrollment(), AttendanceStatus::Present, now()->addDay());
    }

    public function test_a_closed_enrollment_takes_no_register(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ChangeEnrollmentStatus::class)->graduate($enrollment);

        $this->expectException(InvalidValueException::class);

        app(RecordAttendance::class)->record($enrollment->fresh(), AttendanceStatus::Present);
    }

    public function test_a_closed_period_takes_no_register(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ChangeAcademicPeriodStatus::class)->close(current_academic_year());
        academic_period_context()->forget();
        academic_period_context()->resolveFor($this->workingSchool()->fresh());

        $this->expectException(ClosedPeriodException::class);

        app(RecordAttendance::class)->record($enrollment, AttendanceStatus::Present);
    }

    public function test_a_register_can_be_taken_for_a_whole_list(): void
    {
        $this->authorized_user([]);
        $first = $this->enrollment();
        $second = $this->enrollment();

        $records = app(RecordAttendance::class)->recordMany([
            ['enrollment' => $first, 'status' => AttendanceStatus::Present],
            ['enrollment' => $second, 'status' => AttendanceStatus::Absent],
        ]);

        $this->assertCount(2, $records);
        $this->assertSame(AttendanceStatus::Absent, $records[1]->status);
    }

    public function test_staff_can_save_a_home_section_register_from_the_screen(): void
    {
        $this->authorized_user(['read attendance', 'take attendance']);
        $first = $this->enrollment();
        $second = $this->enrollment();
        $second->update(['academic_cycle_section_id' => $first->academic_cycle_section_id]);

        $this->post(route('attendance.register.store'), [
            'academic_cycle_section_id' => $first->academic_cycle_section_id,
            'attended_on' => now()->toDateString(),
            'statuses' => [$first->id => AttendanceStatus::Present->value, $second->id => AttendanceStatus::Absent->value],
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertSame(AttendanceStatus::Absent, AttendanceRecord::query()->where('student_record_id', $second->id)->sole()->status);
    }

    public function test_the_summary_counts_the_days_that_were_recorded(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $action = app(RecordAttendance::class);
        $action->record($enrollment, AttendanceStatus::Present, now()->subDays(3));
        $action->record($enrollment, AttendanceStatus::Late, now()->subDays(2));
        $action->record($enrollment, AttendanceStatus::Absent, now()->subDay());

        $summary = app(AttendanceSummary::class)->forStudent($enrollment);

        $this->assertSame(2, $summary['present']);
        $this->assertSame(1, $summary['absent']);
        $this->assertSame(1, $summary['late']);
        $this->assertSame(3, $summary['recorded']);
        $this->assertSame(66.67, $summary['rate']);
    }

    public function test_a_day_nobody_recorded_does_not_count_against_the_student(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $action = app(RecordAttendance::class);
        $action->record($enrollment, AttendanceStatus::Present, now()->subDay());
        $action->record($enrollment, AttendanceStatus::NotRecorded, now());

        $summary = app(AttendanceSummary::class)->forStudent($enrollment);

        $this->assertSame(1, $summary['recorded']);
        $this->assertSame(100.0, $summary['rate']);
    }

    public function test_a_student_with_no_register_has_no_rate(): void
    {
        $this->authorized_user([]);

        $this->assertNull(app(AttendanceSummary::class)->forStudent($this->enrollment())['rate']);
    }

    /**
     * Create an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        $school = $this->workingSchool();
        $academicYear = current_academic_year() ?? AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $cycleSection = AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
        ]);

        return StudentRecord::factory()->create([
            'school_id' => $school->id,
            'academic_cycle_section_id' => $cycleSection->id,
        ]);
    }

    /**
     * Create a subject in the working school.
     */
    private function subject(): Subject
    {
        return Subject::factory()->create([
            'school_id' => $this->workingSchool()->id,
        ]);
    }
}
