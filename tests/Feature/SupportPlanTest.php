<?php

namespace Tests\Feature;

use App\Actions\Wellbeing\ManageSupportPlan;
use App\Actions\Wellbeing\RecordHealthInformation;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Enums\SupportCategory;
use App\Enums\SupportPlanStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AuditEvent;
use App\Models\School;
use App\Models\StudentHealthRecord;
use App\Models\StudentRecord;
use App\Models\SupportPlan;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use RuntimeException;
use Tests\TestCase;

/**
 * Support plans and health records are kept, moved, and read only by the
 * people who should read them.
 */
class SupportPlanTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_plan_is_opened_for_a_child(): void
    {
        $this->authorized_user(['create support plan']);
        $enrollment = $this->enrollment();

        $plan = app(ManageSupportPlan::class)->open(
            $enrollment,
            'Extra reading each morning',
            summary: 'Twenty minutes with the reading assistant.',
            startsOn: now(),
            reviewOn: now()->addMonth(),
        );

        $this->assertSame(SupportPlanStatus::Draft, $plan->status);
        $this->assertSame(SupportCategory::Intervention, $plan->category);
        $this->assertFalse($plan->is_confidential);
        $this->assertSame($enrollment->id, $plan->student_record_id);
        $this->assertSame($enrollment->school_id, $plan->school_id);
    }

    public function test_a_health_plan_is_confidential_by_itself(): void
    {
        $this->authorized_user(['create support plan']);

        $plan = app(ManageSupportPlan::class)->open($this->enrollment(), 'Asthma care', SupportCategory::Health);

        $this->assertTrue($plan->is_confidential);
    }

    public function test_a_plan_needs_an_active_enrollment(): void
    {
        $this->authorized_user(['create support plan']);
        $enrollment = $this->enrollment();
        $enrollment->update(['status' => EnrollmentStatus::Graduated]);

        $this->expectException(InvalidValueException::class);

        app(ManageSupportPlan::class)->open($enrollment, 'Extra reading');
    }

    public function test_a_plan_cannot_be_reviewed_before_it_starts(): void
    {
        $this->authorized_user(['create support plan']);

        $this->expectException(InvalidValueException::class);

        app(ManageSupportPlan::class)->open(
            $this->enrollment(),
            'Extra reading',
            startsOn: now(),
            reviewOn: now()->subWeek(),
        );
    }

    public function test_a_plan_moves_through_its_states(): void
    {
        $this->authorized_user(['create support plan']);
        $action = app(ManageSupportPlan::class);
        $plan = $action->open($this->enrollment(), 'Extra reading');

        $action->changeStatus($plan, SupportPlanStatus::Active);
        $action->changeStatus($plan, SupportPlanStatus::Completed, reason: 'The child reads with the class now');

        $this->assertSame(SupportPlanStatus::Completed, $plan->fresh()->status);
        $this->assertNotNull($plan->fresh()->ends_on);
        $this->assertSame(2, $plan->statusChanges()->count());
        $this->assertSame(
            'The child reads with the class now',
            $plan->statusChanges()->reorder('id', 'desc')->firstOrFail()->reason
        );
    }

    public function test_a_plan_cannot_jump_from_draft_to_completed(): void
    {
        $this->authorized_user(['create support plan']);
        $action = app(ManageSupportPlan::class);
        $plan = $action->open($this->enrollment(), 'Extra reading');

        $this->expectException(InvalidValueException::class);

        $action->changeStatus($plan, SupportPlanStatus::Completed);
    }

    public function test_plan_history_cannot_be_changed(): void
    {
        $this->authorized_user(['create support plan']);
        $action = app(ManageSupportPlan::class);
        $plan = $action->open($this->enrollment(), 'Extra reading');
        $action->changeStatus($plan, SupportPlanStatus::Active);

        $this->expectException(RuntimeException::class);

        $plan->statusChanges()->firstOrFail()->update(['reason' => 'Something else']);
    }

    public function test_a_step_is_added_and_completed(): void
    {
        $this->authorized_user(['create support plan']);
        $manager = app(ManageSupportPlan::class);
        $plan = $manager->open($this->enrollment(), 'Extra reading');

        $step = $manager->addAction($plan, 'Meet the family', dueOn: now()->addWeek());

        $this->assertFalse($step->isDone());

        $manager->completeAction($step);

        $this->assertTrue($step->fresh()->isDone());
        $this->assertSame(auth()->id(), $step->fresh()->completed_by);
    }

    public function test_a_finished_plan_takes_no_more_work(): void
    {
        $this->authorized_user(['create support plan']);
        $manager = app(ManageSupportPlan::class);
        $plan = $manager->open($this->enrollment(), 'Extra reading');
        $manager->changeStatus($plan, SupportPlanStatus::Cancelled);

        $this->expectException(InvalidValueException::class);

        $manager->addAction($plan, 'Meet the family');
    }

    public function test_a_note_is_written_once(): void
    {
        $this->authorized_user(['create support plan']);
        $manager = app(ManageSupportPlan::class);
        $plan = $manager->open($this->enrollment(), 'Extra reading');

        $note = $manager->addNote($plan, 'The child read a whole page today.');

        $this->expectException(RuntimeException::class);

        $note->update(['body' => 'Something else']);
    }

    public function test_a_confidential_plan_is_readable_by_the_person_who_runs_it(): void
    {
        $this->authorized_user(['read support plan']);
        $owner = auth()->user();
        $plan = app(ManageSupportPlan::class)->open($this->enrollment(), 'Asthma care', SupportCategory::Health, owner: $owner);

        $this->assertTrue(Gate::forUser($owner->fresh())->allows('view', $plan));
    }

    public function test_a_confidential_plan_needs_its_own_permission(): void
    {
        $this->authorized_user(['create support plan']);
        $plan = app(ManageSupportPlan::class)->open($this->enrollment(), 'Asthma care', SupportCategory::Health);

        $this->authorized_user(['read support plan']);

        $this->assertFalse(Gate::forUser(auth()->user())->allows('view', $plan));

        $this->authorized_user(['read confidential support plan']);

        $this->assertTrue(Gate::forUser(auth()->user())->allows('view', $plan));
    }

    public function test_the_readable_scope_hides_confidential_plans(): void
    {
        $this->authorized_user(['create support plan']);
        $manager = app(ManageSupportPlan::class);
        $manager->open($this->enrollment(), 'Extra reading');
        $manager->open($this->enrollment(), 'Asthma care', SupportCategory::Health);

        $this->authorized_user(['read support plan']);

        $this->assertSame(1, SupportPlan::inSchool()->readableBy(auth()->user())->count());
    }

    public function test_another_school_never_reads_the_plan(): void
    {
        $this->authorized_user(['create support plan']);
        $plan = app(ManageSupportPlan::class)->open($this->enrollment(), 'Extra reading');

        $this->authorized_user(['read support plan', 'read confidential support plan'], School::factory()->create());

        $this->assertFalse(Gate::forUser(auth()->user())->allows('view', $plan));
    }

    public function test_a_plan_due_for_review_is_listed(): void
    {
        $this->authorized_user(['create support plan']);
        $manager = app(ManageSupportPlan::class);
        $due = $manager->open($this->enrollment(), 'Extra reading', startsOn: now()->subMonth(), reviewOn: now()->subDay());
        $manager->open($this->enrollment(), 'Extra writing', startsOn: now(), reviewOn: now()->addMonth());

        $plans = SupportPlan::inSchool()->dueForReview()->get();

        $this->assertSame([$due->id], $plans->pluck('id')->all());
    }

    public function test_a_health_record_is_kept_once_for_each_child(): void
    {
        $this->authorized_user(['update health record']);
        $enrollment = $this->enrollment();
        $action = app(RecordHealthInformation::class);

        $action->record($enrollment, ['blood_group' => 'O+', 'allergies' => 'Peanuts']);
        $record = $action->record($enrollment, ['allergies' => 'Peanuts and shellfish']);

        $this->assertSame(1, StudentHealthRecord::where('student_record_id', $enrollment->id)->count());
        $this->assertSame('O+', $record->blood_group);
        $this->assertSame('Peanuts and shellfish', $record->allergies);
        $this->assertSame(auth()->id(), $record->updated_by);
    }

    public function test_the_audit_log_names_the_fields_but_not_the_health_facts(): void
    {
        $this->authorized_user(['update health record']);

        $record = app(RecordHealthInformation::class)->record($this->enrollment(), ['allergies' => 'Peanuts']);

        $event = AuditEvent::ofAction(AuditAction::HealthRecordUpdated)->forSubject($record)->firstOrFail();

        $this->assertContains('allergies', $event->context['fields']);
        $this->assertStringNotContainsString('Peanuts', json_encode($event->context));
    }

    public function test_reading_a_student_does_not_open_the_health_record(): void
    {
        $this->authorized_user(['update health record']);
        $record = app(RecordHealthInformation::class)->record($this->enrollment(), ['allergies' => 'Peanuts']);

        $this->authorized_user(['read student']);

        $this->assertFalse(Gate::forUser(auth()->user())->allows('view', $record));

        $this->authorized_user(['read health record']);

        $this->assertTrue(Gate::forUser(auth()->user())->allows('view', $record));
    }

    public function test_opening_a_plan_is_written_to_the_audit_log(): void
    {
        $this->authorized_user(['create support plan']);

        $plan = app(ManageSupportPlan::class)->open($this->enrollment(), 'Extra reading');

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::SupportPlanOpened)->forSubject($plan)->first());
    }

    /**
     * Create an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }
}
