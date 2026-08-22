<?php

namespace Tests\Feature;

use App\Actions\Enrollment\ChangeEnrollmentStatus;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\EnrollmentStatusChange;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

/**
 * An enrollment holds an explicit state and keeps its whole history.
 */
class EnrollmentStatusTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_graduation_boolean_is_gone(): void
    {
        $this->assertFalse(
            Schema::hasColumn('student_records', 'is_graduated'),
            'Enrollment state must live in the status column, not a boolean.'
        );

        $this->assertTrue(Schema::hasColumn('student_records', 'status'));
    }

    public function test_a_new_enrollment_starts_active(): void
    {
        $enrollment = StudentRecord::factory()->create();

        $this->assertSame(EnrollmentStatus::Active, $enrollment->status);
    }

    public function test_a_state_change_is_written_to_the_history(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $actor = $this->memberOf($this->workingSchool());

        app(ChangeEnrollmentStatus::class)->graduate($enrollment, $actor, 'finished the program');

        $change = $enrollment->fresh()->statusChanges()->firstOrFail();

        $this->assertSame(EnrollmentStatus::Graduated, $enrollment->fresh()->status);
        $this->assertSame(EnrollmentStatus::Active, $change->from_status);
        $this->assertSame(EnrollmentStatus::Graduated, $change->to_status);
        $this->assertSame($actor->id, $change->changed_by);
        $this->assertSame('finished the program', $change->reason);
        $this->assertNotNull($change->effective_on);
    }

    public function test_repeating_a_change_adds_no_second_record(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $action = app(ChangeEnrollmentStatus::class);

        $action->graduate($enrollment);
        $action->graduate($enrollment->fresh());

        $this->assertSame(1, $enrollment->fresh()->statusChanges()->count());
    }

    public function test_a_stale_enrollment_retry_adds_no_second_record(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $stale = $enrollment->fresh();
        $action = app(ChangeEnrollmentStatus::class);

        $action->graduate($enrollment);
        $action->graduate($stale);

        $this->assertSame(1, $enrollment->fresh()->statusChanges()->count());
    }

    public function test_a_state_cannot_skip_to_one_that_does_not_follow(): void
    {
        $enrollment = StudentRecord::factory()->create();

        app(ChangeEnrollmentStatus::class)->change($enrollment, EnrollmentStatus::Transferred);

        $this->expectException(InvalidValueException::class);

        app(ChangeEnrollmentStatus::class)->change($enrollment->fresh(), EnrollmentStatus::Graduated);
    }

    public function test_a_graduated_enrollment_can_return_to_attendance(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $action = app(ChangeEnrollmentStatus::class);

        $action->graduate($enrollment);
        $action->returnToAttendance($enrollment->fresh(), null, 'graduated by mistake');

        $this->assertSame(EnrollmentStatus::Active, $enrollment->fresh()->status);
        $this->assertSame(2, $enrollment->fresh()->statusChanges()->count());
    }

    public function test_history_cannot_be_changed(): void
    {
        $enrollment = StudentRecord::factory()->create();
        app(ChangeEnrollmentStatus::class)->graduate($enrollment);

        $change = EnrollmentStatusChange::firstOrFail();

        $this->expectException(RuntimeException::class);

        $change->update(['reason' => 'a different story']);
    }

    public function test_history_cannot_be_deleted(): void
    {
        $enrollment = StudentRecord::factory()->create();
        app(ChangeEnrollmentStatus::class)->graduate($enrollment);

        $change = EnrollmentStatusChange::firstOrFail();

        $this->expectException(RuntimeException::class);

        $change->delete();
    }

    public function test_authorized_user_can_graduate_a_student(): void
    {
        $enrollment = StudentRecord::factory()->create();

        $this->authorized_user(['graduate student'])
            ->post('dashboard/students/graduate', ['student_id' => [$enrollment->user_id]])
            ->assertRedirect();

        $this->assertSame(EnrollmentStatus::Graduated, $enrollment->fresh()->status);
    }

    public function test_a_graduated_student_cannot_use_the_dashboard(): void
    {
        $enrollment = StudentRecord::factory()->create();
        app(ChangeEnrollmentStatus::class)->graduate($enrollment);

        $student = User::findOrFail($enrollment->user_id);

        $this->actingAs($student)
            ->get('dashboard/students')
            ->assertRedirect('dashboard')
            ->assertSessionHas('danger');
    }

    public function test_authorized_user_can_reset_a_graduation(): void
    {
        $enrollment = StudentRecord::factory()->create();
        app(ChangeEnrollmentStatus::class)->graduate($enrollment);

        $this->authorized_user(['reset graduation'])
            ->delete("dashboard/students/graduations/$enrollment->user_id/reset")
            ->assertRedirect();

        $this->assertSame(EnrollmentStatus::Active, $enrollment->fresh()->status);
    }

    public function test_unauthorized_user_cannot_reset_a_graduation(): void
    {
        $enrollment = StudentRecord::factory()->create();
        app(ChangeEnrollmentStatus::class)->graduate($enrollment);

        $this->unauthorized_user()
            ->delete("dashboard/students/graduations/$enrollment->user_id/reset")
            ->assertForbidden();

        $this->assertSame(EnrollmentStatus::Graduated, $enrollment->fresh()->status);
    }

    public function test_a_graduated_student_leaves_the_cycle_section_and_level_lists(): void
    {
        $enrollment = StudentRecord::factory()->create();
        $student = User::findOrFail($enrollment->user_id);
        $cycleSection = $enrollment->academicCycleSection;

        $this->assertTrue($this->attendingSectionIds($cycleSection->id)->contains($student->id));
        $this->assertTrue($this->attendingLevelIds($cycleSection->academic_level_id)->contains($student->id));

        app(ChangeEnrollmentStatus::class)->graduate($enrollment);

        $this->assertFalse($this->attendingSectionIds($cycleSection->id)->contains($student->id));
        $this->assertFalse($this->attendingLevelIds($cycleSection->academic_level_id)->contains($student->id));
    }

    /**
     * Get the ids of the people who still attend one cycle section.
     *
     * @return Collection<int, int>
     */
    private function attendingSectionIds(int $cycleSectionId): Collection
    {
        return User::activeStudents()
            ->whereHas('studentRecord', fn ($query) => $query->where('academic_cycle_section_id', $cycleSectionId))
            ->pluck('id');
    }

    /**
     * Get the ids of the people who still attend any section of one level.
     *
     * @return Collection<int, int>
     */
    private function attendingLevelIds(int $academicLevelId): Collection
    {
        return User::activeStudents()
            ->whereHas(
                'studentRecord.academicCycleSection',
                fn ($query) => $query->where('academic_level_id', $academicLevelId),
            )
            ->pluck('id');
    }

    public function test_a_graduation_cannot_be_reset_in_another_school(): void
    {
        $enrollment = StudentRecord::factory()->create();
        app(ChangeEnrollmentStatus::class)->graduate($enrollment);

        $student = User::findOrFail($enrollment->user_id);
        $student->schoolMemberships()->delete();

        $this->authorized_user(['reset graduation'])
            ->delete("dashboard/students/graduations/$student->id/reset")
            ->assertForbidden();

        $this->assertSame(EnrollmentStatus::Graduated, $enrollment->fresh()->status);
    }
}
