<?php

namespace Tests\Feature;

use App\Actions\Cohort\ChangeCohortMembership;
use App\Actions\Cohort\ChangeProgramParticipation;
use App\Enums\CohortType;
use App\Enums\EnrollmentStatus;
use App\Enums\Feature;
use App\Enums\ParticipationStatus;
use App\Enums\ProgramType;
use App\Exceptions\InvalidValueException;
use App\Models\Cohort;
use App\Models\GraduationExemption;
use App\Models\GraduationPlan;
use App\Models\GraduationRequirement;
use App\Models\Program;
use App\Models\ResultSnapshot;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Services\Graduation\GraduationProgress;
use App\Services\Ranking\ResultRanking;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Groups, programmes, graduation plans, and the positions worked out from
 * published results.
 */
class CohortTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_group_holds_students_from_its_own_school(): void
    {
        $this->authorized_user(['create cohort']);
        $cohort = $this->cohort();
        $enrollment = $this->enrollment();

        app(ChangeCohortMembership::class)->addStudent($cohort, $enrollment);

        $this->assertSame(1, $cohort->members()->count());
        $this->assertSame($enrollment->id, $cohort->studentRecords()->firstOrFail()->id);
    }

    public function test_a_student_from_another_school_cannot_join(): void
    {
        $this->authorized_user(['create cohort']);
        $cohort = $this->cohort();
        $stranger = StudentRecord::factory()->create(['school_id' => School::factory()->create()->id]);

        $this->expectException(InvalidValueException::class);

        app(ChangeCohortMembership::class)->addStudent($cohort, $stranger);
    }

    public function test_a_student_is_added_once(): void
    {
        $this->authorized_user(['create cohort']);
        $cohort = $this->cohort();
        $enrollment = $this->enrollment();
        $action = app(ChangeCohortMembership::class);

        $action->addStudent($cohort, $enrollment);
        $action->addStudent($cohort, $enrollment);

        $this->assertSame(1, $cohort->members()->count());
    }

    public function test_leaving_a_group_keeps_the_record(): void
    {
        $this->authorized_user(['create cohort']);
        $cohort = $this->cohort();
        $action = app(ChangeCohortMembership::class);
        $member = $action->addStudent($cohort, $this->enrollment());

        $action->remove($member);

        $this->assertSame(1, $cohort->members()->count());
        $this->assertSame(0, $cohort->members()->current()->count());
        $this->assertFalse($member->fresh()->isHeldOn());
    }

    public function test_a_watchlist_is_private_by_itself(): void
    {
        $this->authorized_user(['read cohort']);
        $watchlist = $this->cohort(['name' => 'Attendance watchlist', 'type' => CohortType::Watchlist]);

        $this->assertTrue($watchlist->is_restricted);
        $this->assertFalse(Gate::forUser(auth()->user())->allows('view', $watchlist));

        $this->authorized_user(['read cohort', 'read restricted cohort']);

        $this->assertTrue(Gate::forUser(auth()->user())->allows('view', $watchlist->fresh()));
    }

    public function test_another_school_never_reads_the_group(): void
    {
        $this->authorized_user(['read cohort']);
        $cohort = $this->cohort();

        $this->authorized_user(['read cohort', 'read restricted cohort'], School::factory()->create());

        $this->assertFalse(Gate::forUser(auth()->user())->allows('view', $cohort));
    }

    public function test_a_student_joins_a_programme_without_touching_enrollment(): void
    {
        $this->authorized_user(['create program']);
        $program = $this->program();
        $enrollment = $this->enrollment();

        $place = app(ChangeProgramParticipation::class)->join($program, $enrollment, schedule: 'Wednesday afternoons');

        $this->assertSame(ParticipationStatus::Requested, $place->status);
        $this->assertSame('Wednesday afternoons', $place->schedule);
        $this->assertSame(EnrollmentStatus::Active, $enrollment->fresh()->status);
    }

    public function test_a_closed_programme_takes_nobody(): void
    {
        $this->authorized_user(['create program']);
        $program = $this->program(['is_active' => false]);

        $this->expectException(InvalidValueException::class);

        app(ChangeProgramParticipation::class)->join($program, $this->enrollment());
    }

    public function test_a_place_moves_through_its_states(): void
    {
        $this->authorized_user(['create program']);
        $action = app(ChangeProgramParticipation::class);
        $place = $action->join($this->program(), $this->enrollment());

        $action->changeStatus($place, ParticipationStatus::Active);
        $action->changeStatus($place, ParticipationStatus::Completed, note: 'Finished the year');

        $this->assertSame(ParticipationStatus::Completed, $place->fresh()->status);
        $this->assertNotNull($place->fresh()->ends_on);
    }

    public function test_a_finished_place_cannot_go_back(): void
    {
        $this->authorized_user(['create program']);
        $action = app(ChangeProgramParticipation::class);
        $place = $action->join($this->program(), $this->enrollment());
        $action->changeStatus($place, ParticipationStatus::Active);
        $action->changeStatus($place, ParticipationStatus::Completed);

        $this->expectException(InvalidValueException::class);

        $action->changeStatus($place, ParticipationStatus::Active);
    }

    public function test_a_student_holds_one_place_in_a_programme(): void
    {
        $this->authorized_user(['create program']);
        $program = $this->program();
        $enrollment = $this->enrollment();
        $action = app(ChangeProgramParticipation::class);

        $first = $action->join($program, $enrollment);
        $second = $action->join($program, $enrollment);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, $program->participations()->count());
    }

    public function test_a_plan_is_finished_when_every_requirement_is_met(): void
    {
        $this->authorized_user(['manage graduation plan']);
        $enrollment = $this->enrollment();
        $plan = $this->plan();
        $subject = $this->subject();
        GraduationRequirement::create([
            'graduation_plan_id' => $plan->id,
            'subject_id' => $subject->id,
            'description' => 'Pass mathematics',
            'pass_mark' => 50,
        ]);

        $progress = app(GraduationProgress::class);

        $this->assertFalse($progress->isComplete($plan, $enrollment));

        $this->publishedResult($enrollment, $subject, 72.5);

        $this->assertTrue($progress->isComplete($plan, $enrollment));
    }

    public function test_a_failed_subject_leaves_the_plan_unfinished(): void
    {
        $this->authorized_user(['manage graduation plan']);
        $enrollment = $this->enrollment();
        $plan = $this->plan();
        $subject = $this->subject();
        GraduationRequirement::create([
            'graduation_plan_id' => $plan->id,
            'subject_id' => $subject->id,
            'description' => 'Pass mathematics',
            'pass_mark' => 50,
        ]);
        $this->publishedResult($enrollment, $subject, 41);

        $progress = app(GraduationProgress::class)->for($plan, $enrollment);

        $this->assertFalse($progress['is_complete']);
        $this->assertSame('not_met', $progress['requirements'][0]['state']);
        $this->assertSame(41.0, $progress['requirements'][0]['percentage']);
    }

    public function test_an_excused_student_finishes_without_the_subject(): void
    {
        $this->authorized_user(['manage graduation plan']);
        $enrollment = $this->enrollment();
        $plan = $this->plan();
        $requirement = GraduationRequirement::create([
            'graduation_plan_id' => $plan->id,
            'subject_id' => $this->subject()->id,
            'description' => 'Pass mathematics',
        ]);
        GraduationExemption::create([
            'graduation_requirement_id' => $requirement->id,
            'student_record_id' => $enrollment->id,
            'reason' => 'Studied it at another school',
        ]);

        $progress = app(GraduationProgress::class)->for($plan, $enrollment);

        $this->assertTrue($progress['is_complete']);
        $this->assertSame('exempt', $progress['requirements'][0]['state']);
    }

    public function test_a_school_that_counts_credits_needs_enough_of_them(): void
    {
        $this->authorized_user(['manage graduation plan']);
        $enrollment = $this->enrollment();
        $plan = $this->plan(['uses_credits' => true, 'required_credits' => 6]);
        $first = $this->subject();
        $second = $this->subject();
        GraduationRequirement::create([
            'graduation_plan_id' => $plan->id,
            'subject_id' => $first->id,
            'description' => 'Pass mathematics',
            'credits' => 4,
        ]);
        GraduationRequirement::create([
            'graduation_plan_id' => $plan->id,
            'subject_id' => $second->id,
            'description' => 'Pass english',
            'credits' => 4,
            'is_required' => false,
        ]);
        $this->publishedResult($enrollment, $first, 80);

        $progress = app(GraduationProgress::class)->for($plan, $enrollment);

        $this->assertSame(4, $progress['credits_earned']);
        $this->assertSame(6, $progress['credits_required']);
        $this->assertFalse($progress['is_complete']);

        $this->publishedResult($enrollment, $second, 65);

        $this->assertTrue(app(GraduationProgress::class)->isComplete($plan, $enrollment));
    }

    public function test_ranking_is_off_until_a_school_turns_it_on(): void
    {
        $this->authorized_user(['read cohort']);

        $this->expectException(InvalidValueException::class);

        app(ResultRanking::class)->rank([1, 2]);
    }

    public function test_students_are_ranked_by_their_published_results(): void
    {
        $this->authorized_user(['read cohort']);
        features()->enable(Feature::Ranking);
        $cohort = $this->cohort();
        $subject = $this->subject();
        $membership = app(ChangeCohortMembership::class);

        $best = $this->enrollment();
        $middle = $this->enrollment();
        $last = $this->enrollment();

        foreach ([$best, $middle, $last] as $enrollment) {
            $membership->addStudent($cohort, $enrollment);
        }

        $this->publishedResult($best, $subject, 90);
        $this->publishedResult($middle, $subject, 70);
        $this->publishedResult($last, $subject, 50);

        $ranking = app(ResultRanking::class)->forCohort($cohort);

        $this->assertSame([$best->id, $middle->id, $last->id], $ranking->pluck('student_record_id')->all());
        $this->assertSame([1, 2, 3], $ranking->pluck('position')->all());
        $this->assertSame(90.0, $ranking->first()['average']);
    }

    public function test_equal_averages_share_a_position(): void
    {
        $this->authorized_user(['read cohort']);
        features()->enable(Feature::Ranking);
        $cohort = $this->cohort();
        $subject = $this->subject();
        $membership = app(ChangeCohortMembership::class);

        $first = $this->enrollment();
        $second = $this->enrollment();
        $third = $this->enrollment();

        foreach ([$first, $second, $third] as $enrollment) {
            $membership->addStudent($cohort, $enrollment);
        }

        $this->publishedResult($first, $subject, 80);
        $this->publishedResult($second, $subject, 80);
        $this->publishedResult($third, $subject, 60);

        $ranking = app(ResultRanking::class)->forCohort($cohort);

        $this->assertSame([1, 1, 3], $ranking->pluck('position')->all());
    }

    public function test_a_corrected_result_counts_once(): void
    {
        $this->authorized_user(['read cohort']);
        features()->enable(Feature::Ranking);
        $cohort = $this->cohort();
        $subject = $this->subject();
        $enrollment = $this->enrollment();
        app(ChangeCohortMembership::class)->addStudent($cohort, $enrollment);

        $this->publishedResult($enrollment, $subject, 40);
        $this->publishedResult($enrollment, $subject, 85, revision: 2);

        $ranking = app(ResultRanking::class)->forCohort($cohort);

        $this->assertSame(1, $ranking->count());
        $this->assertSame(85.0, $ranking->first()['average']);
        $this->assertSame(1, $ranking->first()['subjects']);
    }

    public function test_somebody_who_left_the_group_is_not_ranked(): void
    {
        $this->authorized_user(['read cohort']);
        features()->enable(Feature::Ranking);
        $cohort = $this->cohort();
        $subject = $this->subject();
        $membership = app(ChangeCohortMembership::class);
        $stayed = $this->enrollment();
        $left = $this->enrollment();
        $membership->addStudent($cohort, $stayed);
        $membership->remove($membership->addStudent($cohort, $left));

        $this->publishedResult($stayed, $subject, 70);
        $this->publishedResult($left, $subject, 95);

        $ranking = app(ResultRanking::class)->forCohort($cohort);

        $this->assertSame([$stayed->id], $ranking->pluck('student_record_id')->all());
    }

    /**
     * Make a group in the working school.
     *
     * @param  array<string, mixed>  $values
     */
    private function cohort(array $values = []): Cohort
    {
        return Cohort::create($values + [
            'school_id' => $this->workingSchool()->id,
            'name' => 'Class of '.fake()->unique()->numberBetween(2030, 2999),
            'type' => CohortType::GraduationYear,
        ]);
    }

    /**
     * Make a programme in the working school.
     *
     * @param  array<string, mixed>  $values
     */
    private function program(array $values = []): Program
    {
        return Program::create($values + [
            'school_id' => $this->workingSchool()->id,
            'name' => 'Chess club '.fake()->unique()->numberBetween(1, 9999),
            'type' => ProgramType::Club,
        ]);
    }

    /**
     * Make a graduation plan in the working school.
     *
     * @param  array<string, mixed>  $values
     */
    private function plan(array $values = []): GraduationPlan
    {
        return GraduationPlan::create($values + [
            'school_id' => $this->workingSchool()->id,
            'name' => 'Leaving plan '.fake()->unique()->numberBetween(1, 9999),
        ]);
    }

    /**
     * Make an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    /**
     * Make a subject in the working school.
     */
    private function subject(): Subject
    {
        return Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    /**
     * Publish a result for one student in one subject.
     */
    private function publishedResult(StudentRecord $enrollment, Subject $subject, float $percentage, int $revision = 1): ResultSnapshot
    {
        return ResultSnapshot::create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'subject_id' => $subject->id,
            'academic_year_id' => current_academic_year_id(),
            'semester_id' => current_semester_id(),
            'revision' => $revision,
            'percentage' => $percentage,
            'payload' => ['percentage' => $percentage],
            'published_at' => now(),
        ]);
    }
}
