<?php

namespace Tests\Feature;

use App\Actions\Curriculum\SetInstructionalModel;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AuditAction;
use App\Enums\InstructionalModel;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\InstructionalModelSetting;
use App\Models\School;
use App\Services\Curriculum\InstructionalModelResolver;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * A campus chooses how it teaches a cycle before the cycle starts.
 *
 * The answer sets defaults and validation for subject offerings. It never
 * creates a second data model, and it never changes under a cycle that
 * learners already work in.
 */
class InstructionalModelTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    /**
     * The three presets a campus may choose.
     *
     * @return array<string, array{0: string}>
     */
    public static function presets(): array
    {
        return [
            'one class group all day' => ['fixed_home_sections'],
            'hybrid'                  => ['hybrid'],
            'subject timetable'       => ['subject_based_schedule'],
        ];
    }

    /**
     * The cycle states that refuse a settings change.
     *
     * @return array<string, array{0: AcademicPeriodStatus}>
     */
    public static function startedCycleStates(): array
    {
        return [
            'open'     => [AcademicPeriodStatus::Open],
            'closing'  => [AcademicPeriodStatus::Closing],
            'closed'   => [AcademicPeriodStatus::Closed],
            'archived' => [AcademicPeriodStatus::Archived],
        ];
    }

    #[DataProvider('presets')]
    public function test_an_administrator_can_choose_the_model_of_a_future_cycle(string $preset): void
    {
        $actor = $this->authorized_user(['manage school settings']);
        $academicYear = $this->futureCycle();

        $actor->put("dashboard/academic-years/$academicYear->id/instructional-model", [
            'model'  => $preset,
            'reason' => 'the campus agreed this at the planning meeting',
        ])->assertRedirect();

        $this->assertDatabaseHas('instructional_model_settings', [
            'school_id'        => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'model'            => $preset,
        ]);

        $this->assertSame(
            InstructionalModel::from($preset),
            app(InstructionalModelResolver::class)->for($academicYear),
        );
    }

    public function test_a_cycle_nobody_answered_for_teaches_with_one_class_group(): void
    {
        $this->authorized_user([]);
        $academicYear = $this->futureCycle();

        $this->assertSame(InstructionalModel::FixedHomeSections, app(InstructionalModelResolver::class)->for($academicYear));
        $this->assertSame(InstructionalModel::FixedHomeSections, instructional_model($academicYear));
        $this->assertFalse(app(InstructionalModelResolver::class)->isChosen($academicYear));
    }

    public function test_a_campus_answer_does_not_reach_another_campus(): void
    {
        $this->authorized_user(['manage school settings']);
        $ours = $this->futureCycle();
        $other = School::factory()->create();
        $theirs = $this->futureCycle($other);

        app(SetInstructionalModel::class)->set($ours, InstructionalModel::SubjectBasedSchedule);

        $resolver = app(InstructionalModelResolver::class);
        $this->assertSame(InstructionalModel::SubjectBasedSchedule, $resolver->for($ours));
        $this->assertSame(InstructionalModel::FixedHomeSections, $resolver->for($theirs));
    }

    public function test_the_cycle_of_another_campus_cannot_be_read_or_changed(): void
    {
        $actor = $this->authorized_user(['manage school settings', 'update academic year', 'read academic year']);
        $theirs = $this->futureCycle(School::factory()->create());

        $actor->get("dashboard/academic-years/$theirs->id/edit")->assertForbidden();

        $actor->put("dashboard/academic-years/$theirs->id/instructional-model", [
            'model' => 'hybrid',
        ])->assertForbidden();

        $this->assertDatabaseCount('instructional_model_settings', 0);
    }

    public function test_a_person_without_the_permission_cannot_change_the_model(): void
    {
        $actor = $this->unauthorized_user();
        $academicYear = $this->futureCycle();

        $actor->put("dashboard/academic-years/$academicYear->id/instructional-model", [
            'model' => 'hybrid',
        ])->assertForbidden();

        $this->assertDatabaseCount('instructional_model_settings', 0);
    }

    #[DataProvider('startedCycleStates')]
    public function test_a_cycle_that_started_cannot_change_through_the_settings_form(AcademicPeriodStatus $status): void
    {
        $actor = $this->authorized_user(['manage school settings']);
        $academicYear = $this->futureCycle();
        $academicYear->forceFill([
            'status'    => $status,
            'starts_on' => now()->subMonth()->toDateString(),
        ])->save();

        $actor->put("dashboard/academic-years/$academicYear->id/instructional-model", [
            'model' => 'subject_based_schedule',
        ])->assertRedirect()->assertSessionHas('danger');

        $this->assertDatabaseCount('instructional_model_settings', 0);
    }

    public function test_the_action_refuses_a_cycle_that_started(): void
    {
        $this->authorized_user(['manage school settings']);
        $academicYear = $this->futureCycle();
        $academicYear->forceFill(['status' => AcademicPeriodStatus::Open])->save();

        $this->expectException(InvalidValueException::class);

        app(SetInstructionalModel::class)->set($academicYear, InstructionalModel::Hybrid);
    }

    public function test_a_dated_cycle_that_reached_its_first_day_is_no_longer_a_setting(): void
    {
        $this->authorized_user(['manage school settings']);
        $academicYear = $this->futureCycle();
        $academicYear->forceFill(['starts_on' => now()->toDateString()])->save();

        $this->assertFalse(app(SetInstructionalModel::class)->isFutureCycle($academicYear));
    }

    public function test_a_change_is_written_to_the_audit_log(): void
    {
        $actor = $this->authorized_user(['manage school settings']);
        $academicYear = $this->futureCycle();

        $actor->put("dashboard/academic-years/$academicYear->id/instructional-model", [
            'model'  => 'hybrid',
            'reason' => 'the campus combines music classes',
        ])->assertRedirect();

        $setting = InstructionalModelSetting::firstOrFail();
        $event = AuditEvent::ofAction(AuditAction::InstructionalModelChanged)->forSubject($setting)->firstOrFail();

        $this->assertSame($academicYear->school_id, $event->school_id);
        $this->assertNotNull($event->actor_id);
        $this->assertNull($event->context['from']);
        $this->assertSame('hybrid', $event->context['to']);
        $this->assertSame($academicYear->id, $event->context['academic_year_id']);
        $this->assertSame('the campus combines music classes', $event->context['reason']);
    }

    public function test_the_second_change_records_the_answer_it_replaced(): void
    {
        $this->authorized_user(['manage school settings']);
        $academicYear = $this->futureCycle();
        $action = app(SetInstructionalModel::class);

        $action->set($academicYear, InstructionalModel::Hybrid);
        $setting = $action->set($academicYear, InstructionalModel::SubjectBasedSchedule);

        $this->assertSame(InstructionalModel::SubjectBasedSchedule, $setting->fresh()->model);
        $this->assertSame(1, InstructionalModelSetting::count());

        $events = AuditEvent::ofAction(AuditAction::InstructionalModelChanged)->get();
        $this->assertCount(2, $events);
        $this->assertSame('hybrid', $events->last()->context['from']);
    }

    public function test_asking_for_the_answer_the_cycle_already_has_records_nothing(): void
    {
        $this->authorized_user(['manage school settings']);
        $academicYear = $this->futureCycle();
        $action = app(SetInstructionalModel::class);

        $action->set($academicYear, InstructionalModel::Hybrid);
        $action->set($academicYear, InstructionalModel::Hybrid);

        $this->assertCount(1, AuditEvent::ofAction(AuditAction::InstructionalModelChanged)->get());
    }

    public function test_the_setup_screen_asks_the_question_in_plain_language(): void
    {
        $actor = $this->authorized_user(['manage school settings']);
        $academicYear = $this->futureCycle();

        $page = $actor->get("dashboard/academic-years/$academicYear->id/instructional-model")->assertOk();

        $page->assertSee(InstructionalModel::SETUP_QUESTION, false);

        foreach (InstructionalModel::cases() as $model) {
            $page->assertSee($model->setupAnswer(), false);
            $page->assertSee($model->description(), false);
            $page->assertSee($model->example(), false);
        }

        $page->assertSee('Save teaching setup', false);
        $page->assertDontSee('education system');
    }

    public function test_the_setup_screen_says_which_rosters_each_answer_allows(): void
    {
        $actor = $this->authorized_user(['manage school settings']);
        $academicYear = $this->futureCycle();

        $actor->get("dashboard/academic-years/$academicYear->id/instructional-model")
            ->assertOk()
            ->assertSee('Rosters start as one home section', false)
            ->assertSee('Rosters start as named learners', false)
            ->assertSee('Combined class groups', false)
            ->assertSee('Named learners', false);
    }

    public function test_the_setup_screen_names_the_answer_the_cycle_uses_now(): void
    {
        $actor = $this->authorized_user(['manage school settings']);
        $academicYear = $this->futureCycle();

        $actor->get("dashboard/academic-years/$academicYear->id/instructional-model")
            ->assertOk()
            ->assertSee('Not answered yet', false)
            ->assertSee(InstructionalModel::default()->label(), false);

        app(SetInstructionalModel::class)->set($academicYear, InstructionalModel::Hybrid);

        $actor->get("dashboard/academic-years/$academicYear->id/instructional-model")
            ->assertOk()
            ->assertSee('Answered', false)
            ->assertSee(InstructionalModel::Hybrid->label(), false);
    }

    public function test_the_setup_screen_locks_a_cycle_that_started(): void
    {
        $actor = $this->authorized_user(['manage school settings']);
        $academicYear = $this->futureCycle();
        $academicYear->forceFill([
            'status'    => AcademicPeriodStatus::Open,
            'starts_on' => now()->subMonth()->toDateString(),
        ])->save();

        $actor->get("dashboard/academic-years/$academicYear->id/instructional-model")
            ->assertOk()
            ->assertSee('This cycle has already started', false)
            ->assertSee('Fixed for this cycle', false)
            ->assertDontSee('Save teaching setup', false);
    }

    public function test_a_reader_who_cannot_answer_is_told_who_can(): void
    {
        $actor = $this->authorized_user(['read academic year']);
        $academicYear = $this->futureCycle();

        $actor->get("dashboard/academic-years/$academicYear->id/instructional-model")
            ->assertOk()
            ->assertSee('Ask a campus administrator', false)
            ->assertDontSee('Save teaching setup', false);
    }

    public function test_the_cycle_workspace_links_to_the_setup_screen(): void
    {
        $actor = $this->authorized_user(['manage school settings', 'update academic year']);
        $academicYear = $this->futureCycle();

        $actor->get("dashboard/academic-years/$academicYear->id/edit")
            ->assertOk()
            ->assertSee('Teaching setup', false)
            ->assertSee(InstructionalModel::default()->label(), false)
            ->assertSee(route('academic-years.instructional-model.edit', $academicYear->id), false);
    }

    public function test_the_setup_screen_of_another_campus_cannot_be_opened(): void
    {
        $actor = $this->authorized_user(['manage school settings', 'read academic year']);
        $theirs = $this->futureCycle(School::factory()->create());

        $actor->get("dashboard/academic-years/$theirs->id/instructional-model")->assertForbidden();
    }

    public function test_the_presets_say_which_rosters_an_offering_may_use(): void
    {
        $fixed = InstructionalModel::FixedHomeSections;
        $this->assertSame(RosterMode::HomeSection, $fixed->defaultRosterMode());
        $this->assertFalse($fixed->allowsCombinedSections());
        $this->assertFalse($fixed->allowsIndividualRosters());
        $this->assertTrue($fixed->keepsLearnersTogether());
        $this->assertSame([RosterMode::HomeSection, RosterMode::AcademicLevel], $fixed->rosterModes());

        $hybrid = InstructionalModel::Hybrid;
        $this->assertSame(RosterMode::HomeSection, $hybrid->defaultRosterMode());
        $this->assertTrue($hybrid->allowsCombinedSections());
        $this->assertTrue($hybrid->allowsIndividualRosters());
        $this->assertTrue($hybrid->keepsLearnersTogether());

        $subjectBased = InstructionalModel::SubjectBasedSchedule;
        $this->assertSame(RosterMode::IndividualRoster, $subjectBased->defaultRosterMode());
        $this->assertTrue($subjectBased->allowsCombinedSections());
        $this->assertTrue($subjectBased->allowsIndividualRosters());
        $this->assertFalse($subjectBased->keepsLearnersTogether());
        $this->assertSame(RosterMode::cases(), $subjectBased->rosterModes());
    }

    public function test_the_default_preset_is_the_one_schools_teach_with_now(): void
    {
        $this->assertSame(InstructionalModel::FixedHomeSections, InstructionalModel::default());
        $this->assertSame(
            ['fixed_home_sections', 'hybrid', 'subject_based_schedule'],
            InstructionalModel::values(),
        );
    }

    /**
     * Create a cycle that is planned but has not started.
     */
    private function futureCycle(?School $school = null): AcademicYear
    {
        $school = $this->workingSchool($school);
        $year = (int) now()->addYear()->format('Y');

        return AcademicYear::factory()->create([
            'school_id'  => $school->id,
            'start_year' => $year,
            'stop_year'  => $year + 1,
            'status'     => AcademicPeriodStatus::Scheduled,
            'starts_on'  => now()->addMonths(6)->toDateString(),
            'ends_on'    => now()->addMonths(16)->toDateString(),
        ]);
    }
}
