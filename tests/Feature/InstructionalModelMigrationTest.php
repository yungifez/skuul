<?php

namespace Tests\Feature;

use App\Actions\Curriculum\MigrateInstructionalModel;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AuditAction;
use App\Enums\InstructionalModel;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\InstructionalModelMigration;
use App\Models\InstructionalModelSetting;
use App\Models\School;
use App\Services\Curriculum\InstructionalModelResolver;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

/**
 * Moving a running cycle to another instructional model.
 *
 * Choosing the model of a cycle that has not started is a setting. Moving one
 * learners already work in is a separate, recorded act: its own permission, a
 * written reason, and a record of what the cycle held at the time.
 */
class InstructionalModelMigrationTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    /**
     * The cycle states that refuse a mid-year move.
     *
     * @return array<string, array{0: AcademicPeriodStatus}>
     */
    public static function statesThatRefuseAMove(): array
    {
        return [
            'draft' => [AcademicPeriodStatus::Draft],
            'scheduled' => [AcademicPeriodStatus::Scheduled],
            'closed' => [AcademicPeriodStatus::Closed],
            'archived' => [AcademicPeriodStatus::Archived],
        ];
    }

    public function test_an_administrator_with_the_permission_can_move_a_running_cycle(): void
    {
        $actor = $this->authorized_user(['migrate instructional model']);
        $academicYear = $this->runningCycle();

        $actor->post("dashboard/academic-years/$academicYear->id/instructional-model/migration", [
            'model' => 'hybrid',
            'reason' => 'The campus agreed to combine two sections for music.',
            'confirm' => '1',
        ])->assertRedirect(route('academic-years.instructional-model.edit', $academicYear));

        $this->assertSame(InstructionalModel::Hybrid, app(InstructionalModelResolver::class)->for($academicYear));

        $migration = InstructionalModelMigration::firstOrFail();
        $this->assertSame(InstructionalModel::FixedHomeSections, $migration->from_model);
        $this->assertSame(InstructionalModel::Hybrid, $migration->to_model);
        $this->assertSame('The campus agreed to combine two sections for music.', $migration->reason);
        $this->assertNotNull($migration->migrated_by);
    }

    public function test_the_move_is_written_to_the_audit_log(): void
    {
        $this->authorized_user(['migrate instructional model']);
        $academicYear = $this->runningCycle();

        $migration = app(MigrateInstructionalModel::class)->migrate(
            $academicYear,
            InstructionalModel::SubjectBasedSchedule,
            'Senior learners chose their own subjects from this term.',
        );

        $event = AuditEvent::ofAction(AuditAction::InstructionalModelMigrated)->forSubject($migration)->firstOrFail();

        $this->assertSame('fixed_home_sections', $event->context['from']);
        $this->assertSame('subject_based_schedule', $event->context['to']);
        $this->assertSame($academicYear->id, $event->context['academic_year_id']);
    }

    public function test_the_move_records_what_the_cycle_held_at_the_time(): void
    {
        $this->authorized_user(['migrate instructional model']);
        $academicYear = $this->runningCycle();
        InstructionalModelSetting::create([
            'school_id' => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'model' => InstructionalModel::Hybrid,
        ]);

        CourseOffering::factory()->count(2)->create([
            'school_id' => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'roster_mode' => RosterMode::HomeSection,
        ]);
        CourseOffering::factory()->create([
            'school_id' => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'roster_mode' => RosterMode::CombinedHomeSections,
        ]);

        $migration = app(MigrateInstructionalModel::class)->migrate(
            $academicYear,
            InstructionalModel::FixedHomeSections,
            'The combined groups did not work, so every class stays together.',
        );

        $this->assertSame(3, $migration->impact['offerings']);
        $this->assertSame(1, $migration->impact['exceptions']);
        $this->assertSame(['combined_home_sections' => 1], $migration->impact['exception_rosters']);
    }

    public function test_a_subject_already_arranged_the_old_way_is_left_alone(): void
    {
        $this->authorized_user(['migrate instructional model']);
        $academicYear = $this->runningCycle();
        InstructionalModelSetting::create([
            'school_id' => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'model' => InstructionalModel::SubjectBasedSchedule,
        ]);
        $offering = CourseOffering::factory()->create([
            'school_id' => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'roster_mode' => RosterMode::IndividualRoster,
        ]);

        app(MigrateInstructionalModel::class)->migrate(
            $academicYear,
            InstructionalModel::FixedHomeSections,
            'The senior timetable moves back to whole classes next term.',
        );

        $this->assertSame(RosterMode::IndividualRoster, $offering->fresh()->roster_mode);
    }

    #[DataProvider('statesThatRefuseAMove')]
    public function test_only_a_running_cycle_can_be_moved(AcademicPeriodStatus $status): void
    {
        $this->authorized_user(['migrate instructional model']);
        $academicYear = $this->runningCycle();
        $academicYear->forceFill(['status' => $status])->save();

        $this->expectException(InvalidValueException::class);

        app(MigrateInstructionalModel::class)->migrate(
            $academicYear,
            InstructionalModel::Hybrid,
            'The campus asked for the change in writing.',
        );
    }

    public function test_a_cycle_that_has_not_started_is_sent_back_to_the_setup_form(): void
    {
        $actor = $this->authorized_user(['migrate instructional model']);
        $academicYear = $this->runningCycle();
        $academicYear->forceFill([
            'status' => AcademicPeriodStatus::Scheduled,
            'starts_on' => now()->addMonths(3)->toDateString(),
        ])->save();

        $actor->post("dashboard/academic-years/$academicYear->id/instructional-model/migration", [
            'model' => 'hybrid',
            'reason' => 'The campus asked for the change in writing.',
            'confirm' => '1',
        ])->assertRedirect()->assertSessionHas('danger');

        $this->assertDatabaseCount('instructional_model_migrations', 0);
    }

    public function test_the_move_refuses_the_model_the_cycle_already_teaches_with(): void
    {
        $this->authorized_user(['migrate instructional model']);
        $academicYear = $this->runningCycle();

        $this->expectException(InvalidValueException::class);

        app(MigrateInstructionalModel::class)->migrate(
            $academicYear,
            InstructionalModel::FixedHomeSections,
            'Somebody pressed the button twice by mistake.',
        );
    }

    public function test_the_move_needs_a_written_reason_and_a_confirmation(): void
    {
        $actor = $this->authorized_user(['migrate instructional model']);
        $academicYear = $this->runningCycle();

        $actor->post("dashboard/academic-years/$academicYear->id/instructional-model/migration", [
            'model' => 'hybrid',
            'reason' => 'because',
        ])->assertSessionHasErrors(['reason', 'confirm']);

        $this->assertDatabaseCount('instructional_model_migrations', 0);
    }

    public function test_the_settings_permission_alone_cannot_move_a_running_cycle(): void
    {
        $actor = $this->authorized_user(['manage school settings']);
        $academicYear = $this->runningCycle();

        $actor->post("dashboard/academic-years/$academicYear->id/instructional-model/migration", [
            'model' => 'hybrid',
            'reason' => 'The campus agreed to combine two sections for music.',
            'confirm' => '1',
        ])->assertForbidden();

        $this->assertDatabaseCount('instructional_model_migrations', 0);
    }

    public function test_a_running_cycle_of_another_campus_cannot_be_moved(): void
    {
        $actor = $this->authorized_user(['migrate instructional model']);
        $theirs = $this->runningCycle(School::factory()->create());

        $actor->post("dashboard/academic-years/$theirs->id/instructional-model/migration", [
            'model' => 'hybrid',
            'reason' => 'The campus agreed to combine two sections for music.',
            'confirm' => '1',
        ])->assertForbidden();

        $this->assertDatabaseCount('instructional_model_migrations', 0);
    }

    public function test_the_history_is_append_only(): void
    {
        $this->authorized_user(['migrate instructional model']);
        $academicYear = $this->runningCycle();

        $migration = app(MigrateInstructionalModel::class)->migrate(
            $academicYear,
            InstructionalModel::Hybrid,
            'The campus agreed to combine two sections for music.',
        );

        try {
            $migration->update(['reason' => 'a different reason']);
            $this->fail('The history accepted an edit.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        }

        try {
            $migration->delete();
            $this->fail('The history accepted a delete.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        }
    }

    public function test_the_screen_offers_the_move_and_then_shows_it(): void
    {
        $actor = $this->authorized_user(['migrate instructional model']);
        $academicYear = $this->runningCycle();

        $actor->get("dashboard/academic-years/$academicYear->id/instructional-model")
            ->assertOk()
            ->assertSee('Move this cycle mid-year', false)
            ->assertSee('Why the cycle is moving', false)
            ->assertDontSee('Moves recorded for this cycle', false);

        app(MigrateInstructionalModel::class)->migrate(
            $academicYear,
            InstructionalModel::Hybrid,
            'The campus agreed to combine two sections for music.',
        );

        $actor->get("dashboard/academic-years/$academicYear->id/instructional-model")
            ->assertOk()
            ->assertSee('Moves recorded for this cycle', false)
            ->assertSee('The campus agreed to combine two sections for music.', false)
            ->assertSee(InstructionalModel::Hybrid->label(), false);
    }

    public function test_a_settings_administrator_reads_the_closed_form_instead(): void
    {
        $actor = $this->authorized_user(['manage school settings']);
        $academicYear = $this->runningCycle();

        $actor->get("dashboard/academic-years/$academicYear->id/instructional-model")
            ->assertOk()
            ->assertSee('This cycle has already started', false)
            ->assertDontSee('Move this cycle mid-year', false);
    }

    /**
     * Create a cycle that learners already work in.
     */
    private function runningCycle(?School $school = null): AcademicYear
    {
        $school = $this->workingSchool($school);
        $year = (int) now()->format('Y');

        return AcademicYear::factory()->create([
            'school_id' => $school->id,
            'start_year' => $year,
            'stop_year' => $year + 1,
            'status' => AcademicPeriodStatus::Open,
            'starts_on' => now()->subMonths(2)->toDateString(),
            'ends_on' => now()->addMonths(8)->toDateString(),
        ]);
    }
}
