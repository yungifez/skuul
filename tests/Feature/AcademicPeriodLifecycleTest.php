<?php

namespace Tests\Feature;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Enums\AcademicPeriodStatus;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriodStatusChange;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamRecord;
use App\Models\ExamSlot;
use App\Models\School;
use App\Models\Semester;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Closing an academic period freezes the work done inside it.
 */
class AcademicPeriodLifecycleTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_period_starts_open(): void
    {
        $year = AcademicYear::factory()->create();

        $this->assertSame(AcademicPeriodStatus::Open, $year->status);
        $this->assertTrue($year->isOpen());
    }

    public function test_closing_a_period_records_who_closed_it_and_why(): void
    {
        $year = AcademicYear::factory()->create();
        $actor = $this->memberOf($this->workingSchool());

        app(ChangeAcademicPeriodStatus::class)->close($year, $actor, 'the year finished');

        $change = $year->fresh()->statusChanges()->firstOrFail();

        $this->assertTrue($year->fresh()->isClosed());
        $this->assertSame(AcademicPeriodStatus::Open, $change->from_status);
        $this->assertSame(AcademicPeriodStatus::Closed, $change->to_status);
        $this->assertSame($actor->id, $change->changed_by);
        $this->assertSame('the year finished', $change->reason);
    }

    public function test_closing_a_period_twice_adds_no_second_record(): void
    {
        $year = AcademicYear::factory()->create();
        $action = app(ChangeAcademicPeriodStatus::class);

        $action->close($year);
        $action->close($year->fresh());

        $this->assertSame(1, $year->fresh()->statusChanges()->count());
    }

    public function test_closing_a_year_closes_its_semesters(): void
    {
        $year = AcademicYear::factory()->create();
        $semester = Semester::factory()->create([
            'school_id'        => $year->school_id,
            'academic_year_id' => $year->id,
        ]);

        app(ChangeAcademicPeriodStatus::class)->close($year);

        $this->assertTrue($semester->fresh()->isClosed());
    }

    public function test_a_semester_cannot_reopen_while_its_year_is_closed(): void
    {
        $year = AcademicYear::factory()->create();
        $semester = Semester::factory()->create([
            'school_id'        => $year->school_id,
            'academic_year_id' => $year->id,
        ]);

        app(ChangeAcademicPeriodStatus::class)->close($year);

        $this->expectException(InvalidValueException::class);

        app(ChangeAcademicPeriodStatus::class)->reopen($semester->fresh());
    }

    public function test_a_semester_reopens_after_its_year_reopens(): void
    {
        $year = AcademicYear::factory()->create();
        $semester = Semester::factory()->create([
            'school_id'        => $year->school_id,
            'academic_year_id' => $year->id,
        ]);
        $action = app(ChangeAcademicPeriodStatus::class);

        $action->close($year);
        $action->reopen($year->fresh());
        $action->reopen($semester->fresh());

        $this->assertTrue($semester->fresh()->isOpen());
    }

    public function test_period_history_cannot_be_changed(): void
    {
        $year = AcademicYear::factory()->create();
        app(ChangeAcademicPeriodStatus::class)->close($year);

        $this->expectException(RuntimeException::class);

        AcademicPeriodStatusChange::firstOrFail()->update(['reason' => 'a different story']);
    }

    public function test_a_record_in_a_closed_period_cannot_be_changed(): void
    {
        $exam = Exam::factory()->create(['semester_id' => $this->openSemester()->id]);

        app(ChangeAcademicPeriodStatus::class)->close($exam->semester);

        $this->expectException(ClosedPeriodException::class);

        $exam->fresh()->update(['name' => 'a new name']);
    }

    public function test_a_record_in_a_closed_period_cannot_be_removed(): void
    {
        $exam = Exam::factory()->create(['semester_id' => $this->openSemester()->id]);

        app(ChangeAcademicPeriodStatus::class)->close($exam->semester);

        $this->expectException(ClosedPeriodException::class);

        $exam->fresh()->delete();
    }

    public function test_marks_cannot_be_entered_in_a_closed_period(): void
    {
        $semester = $this->openSemester();
        $exam = Exam::factory()->create(['semester_id' => $semester->id]);
        $slot = ExamSlot::factory()->create(['exam_id' => $exam->id]);

        app(ChangeAcademicPeriodStatus::class)->close($semester);

        $this->expectException(ClosedPeriodException::class);

        ExamRecord::factory()->create(['exam_slot_id' => $slot->id]);
    }

    public function test_exam_slots_cannot_be_changed_in_a_closed_period(): void
    {
        $semester = $this->openSemester();
        $exam = Exam::factory()->create(['semester_id' => $semester->id]);
        $slot = ExamSlot::factory()->create(['exam_id' => $exam->id]);

        app(ChangeAcademicPeriodStatus::class)->close($semester);

        $this->expectException(ClosedPeriodException::class);

        $slot->fresh()->update(['name' => 'new slot']);
    }

    public function test_timetable_slots_cannot_be_changed_in_a_closed_period(): void
    {
        $semester = $this->openSemester();
        $timetable = Timetable::factory()->create(['semester_id' => $semester->id]);
        $slot = TimetableTimeSlot::factory()->create(['timetable_id' => $timetable->id]);

        app(ChangeAcademicPeriodStatus::class)->close($semester);

        $this->expectException(ClosedPeriodException::class);

        $slot->fresh()->update(['start_time' => '09:00']);
    }

    public function test_records_of_an_open_period_still_change(): void
    {
        $exam = Exam::factory()->create(['semester_id' => $this->openSemester()->id]);

        $exam->update(['name' => 'a new name']);

        $this->assertSame('a new name', $exam->fresh()->name);
    }

    public function test_authorized_user_can_close_and_reopen_a_year(): void
    {
        $year = AcademicYear::factory()->create(['school_id' => current_school_id()]);

        // One signed-in person makes both requests. Signing in again would
        // invalidate the session.
        $actor = $this->authorized_user(['close academic period', 'reopen academic period']);

        $actor->post("dashboard/academic-years/$year->id/close", ['reason' => 'the year finished'])
            ->assertRedirect();

        $this->assertTrue($year->fresh()->isClosed());

        $actor->post("dashboard/academic-years/$year->id/reopen", ['reason' => 'a mark was wrong'])
            ->assertRedirect();

        $this->assertTrue($year->fresh()->isOpen());
        $this->assertSame(2, $year->fresh()->statusChanges()->count());
    }

    public function test_unauthorized_user_cannot_close_a_year(): void
    {
        $year = AcademicYear::factory()->create(['school_id' => current_school_id()]);

        $this->unauthorized_user()
            ->post("dashboard/academic-years/$year->id/close")
            ->assertForbidden();

        $this->assertTrue($year->fresh()->isOpen());
    }

    public function test_a_period_of_another_school_cannot_be_closed(): void
    {
        $other = School::factory()->create();
        $year = AcademicYear::factory()->create(['school_id' => $other->id]);

        $this->authorized_user(['close academic period'])
            ->post("dashboard/academic-years/$year->id/close")
            ->assertForbidden();

        $this->assertTrue($year->fresh()->isOpen());
    }

    public function test_authorized_user_can_close_a_semester(): void
    {
        $semester = $this->openSemester();

        $this->authorized_user(['close academic period'])
            ->post("dashboard/semesters/$semester->id/close")
            ->assertRedirect();

        $this->assertTrue($semester->fresh()->isClosed());
    }

    /**
     * Get a semester of the working school that still accepts writes.
     */
    private function openSemester(): Semester
    {
        $year = AcademicYear::factory()->create(['school_id' => current_school_id()]);

        return Semester::factory()->create([
            'school_id'        => current_school_id(),
            'academic_year_id' => $year->id,
        ]);
    }
}
