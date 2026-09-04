<?php

namespace Tests\Feature;

use App\Actions\Report\PublishReportCard;
use App\Actions\Report\PublishTranscript;
use App\Enums\AcademicPeriodStatus;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\ResultSnapshot;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicRecordScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_report_card_screen_explains_itself_before_any_card_exists(): void
    {
        $this->authorized_user(['read report', 'create report']);

        $this->get(route('report-cards.index'))
            ->assertOk()
            ->assertSee('No report cards yet')
            ->assertSee('Publish a report card');
    }

    public function test_the_report_card_screen_narrows_the_list_to_one_learner(): void
    {
        $school = $this->workingSchool();
        $actor = $this->memberOf($school);
        $this->authorized_user(['read report', 'create report'], $school);
        $period = $this->closingPeriod($school);
        $kept = $this->learnerWithAResult($school, $period, 'Ada Kept');
        $hidden = $this->learnerWithAResult($school, $period, 'Ben Hidden');
        $keptCard = app(PublishReportCard::class)->publish($kept, $period, $actor);
        $hiddenCard = app(PublishReportCard::class)->publish($hidden, $period, $actor);

        // Every learner stays in the filter menu, so the rows are what proves
        // the filter worked, not the name on its own.
        $this->get(route('report-cards.index', ['student_record_id' => $kept->id]))
            ->assertOk()
            ->assertSee('Ada Kept')
            ->assertSee(route('report-cards.show', $keptCard))
            ->assertDontSee(route('report-cards.show', $hiddenCard));
    }

    public function test_the_report_card_screen_says_when_a_filter_hides_everything(): void
    {
        $school = $this->workingSchool();
        $actor = $this->memberOf($school);
        $this->authorized_user(['read report', 'create report'], $school);
        $period = $this->closingPeriod($school);
        $learner = $this->learnerWithAResult($school, $period, 'Ada Kept');
        $card = app(PublishReportCard::class)->publish($learner, $period, $actor);
        $other = StudentRecord::factory()->create(['school_id' => $school->id]);

        $this->get(route('report-cards.index', ['student_record_id' => $other->id]))
            ->assertOk()
            ->assertSee('Nothing matches this filter')
            ->assertDontSee(route('report-cards.show', $card));
    }

    public function test_the_report_card_list_links_to_the_issued_card(): void
    {
        $school = $this->workingSchool();
        $actor = $this->memberOf($school);
        $this->authorized_user(['read report', 'create report'], $school);
        $period = $this->closingPeriod($school);
        $learner = $this->learnerWithAResult($school, $period, 'Ada Kept');
        $card = app(PublishReportCard::class)->publish($learner, $period, $actor);

        $this->get(route('report-cards.index'))
            ->assertOk()
            ->assertSee(route('report-cards.show', $card));
    }

    public function test_a_card_lists_every_revision_and_warns_when_a_later_one_exists(): void
    {
        $school = $this->workingSchool();
        $actor = $this->memberOf($school);
        $this->authorized_user(['read report', 'create report'], $school);
        $period = $this->closingPeriod($school);
        $learner = $this->learnerWithAResult($school, $period, 'Ada Kept');
        $first = app(PublishReportCard::class)->publish($learner, $period, $actor);
        $second = app(PublishReportCard::class)->publish($learner, $period, $actor, 'Corrected a mark.');

        $this->get(route('report-cards.show', $first))
            ->assertOk()
            ->assertSee('A later version exists')
            ->assertSee('Revision history')
            ->assertSee('Corrected a mark.')
            ->assertSee(route('report-cards.show', $second));

        $this->get(route('report-cards.show', $second))
            ->assertOk()
            ->assertDontSee('A later version exists');
    }

    public function test_the_report_card_screen_distinguishes_the_same_period_across_years(): void
    {
        $school = $this->workingSchool();
        $actor = $this->memberOf($school);
        $this->authorized_user(['read report', 'create report'], $school);
        $historicalYear = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'start_year' => 2024,
            'stop_year' => 2025,
        ]);
        $historicalPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $historicalYear->id,
            'name' => 'Term 1',
            'label' => 'Term 1',
            'status' => AcademicPeriodStatus::Closing,
        ]);
        $currentPeriod = $this->closingPeriod($school);
        $currentPeriod->update(['name' => 'Term 1', 'label' => 'Term 1']);
        $historicalLearner = $this->learnerWithAResult($school, $historicalPeriod, 'Historical Learner');
        $currentLearner = $this->learnerWithAResult($school, $currentPeriod, 'Current Learner');
        $historicalCard = app(PublishReportCard::class)->publish($historicalLearner, $historicalPeriod, $actor);
        $currentCard = app(PublishReportCard::class)->publish($currentLearner, $currentPeriod, $actor);

        $this->get(route('report-cards.index', ['academic_year_id' => $historicalYear->id]))
            ->assertOk()
            ->assertSee($historicalYear->name)
            ->assertSee(route('report-cards.show', $historicalCard))
            ->assertDontSee(route('report-cards.show', $currentCard));
    }

    public function test_report_card_history_rejects_a_period_from_another_selected_year(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user(['read report', 'create report'], $school);
        $year = AcademicYear::factory()->create(['school_id' => $school->id]);
        $otherYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $period = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $otherYear->id,
        ]);

        $this->get(route('report-cards.index', [
            'academic_year_id' => $year->id,
            'academic_period_id' => $period->id,
        ]))->assertNotFound();
    }

    public function test_report_card_history_rejects_another_schools_year(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user(['read report', 'create report'], $school);
        $otherSchool = School::factory()->create();
        $otherYear = AcademicYear::factory()->create(['school_id' => $otherSchool->id]);

        $this->get(route('report-cards.index', ['academic_year_id' => $otherYear->id]))
            ->assertNotFound();
    }

    public function test_the_transcript_screen_explains_itself_before_any_transcript_exists(): void
    {
        $this->authorized_user(['read report', 'create report']);

        $this->get(route('transcripts.index'))
            ->assertOk()
            ->assertSee('No transcripts yet')
            ->assertSee('Issue a transcript');
    }

    public function test_the_transcript_screen_narrows_the_list_to_one_learner(): void
    {
        $school = $this->workingSchool();
        $actor = $this->memberOf($school);
        $this->authorized_user(['read report', 'create report'], $school);
        $period = $this->closingPeriod($school);
        $kept = $this->learnerWithAResult($school, $period, 'Ada Kept', 'Kept Subject');
        $hidden = $this->learnerWithAResult($school, $period, 'Ben Hidden', 'Hidden Subject');
        app(PublishTranscript::class)->publish($kept, $actor);
        app(PublishTranscript::class)->publish($hidden, $actor);

        // Every learner stays in the filter menu, so the subjects each
        // transcript carries are what proves the filter worked.
        $this->get(route('transcripts.index', ['student_record_id' => $kept->id]))
            ->assertOk()
            ->assertSee('Kept Subject')
            ->assertDontSee('Hidden Subject');
    }

    public function test_a_transcript_row_carries_the_subjects_it_holds(): void
    {
        $school = $this->workingSchool();
        $actor = $this->memberOf($school);
        $this->authorized_user(['read report', 'create report'], $school);
        $period = $this->closingPeriod($school);
        $learner = $this->learnerWithAResult($school, $period, 'Ada Kept', 'Applied Physics');
        app(PublishTranscript::class)->publish($learner, $actor);

        $this->get(route('transcripts.index'))
            ->assertOk()
            ->assertSee('Read subjects')
            ->assertSee('Applied Physics');
    }

    public function test_a_long_list_of_report_cards_offers_a_second_page(): void
    {
        $school = $this->workingSchool();
        $actor = $this->memberOf($school);
        $this->authorized_user(['read report', 'create report'], $school);
        $period = $this->closingPeriod($school);
        $offering = $this->offering($school, $period);

        foreach (range(1, 21) as $index) {
            $learner = StudentRecord::factory()->create(['school_id' => $school->id]);
            ResultSnapshot::create(['school_id' => $school->id, 'student_record_id' => $learner->id, 'course_offering_id' => $offering->id, 'revision' => 1, 'percentage' => 70, 'payload' => ['percentage' => 70], 'published_at' => now()]);
            app(PublishReportCard::class)->publish($learner, $period, $actor);
        }

        $this->get(route('report-cards.index'))
            ->assertOk()
            ->assertSee('Pagination Navigation')
            ->assertSee(route('report-cards.index', ['page' => 2]));
    }

    private function closingPeriod(School $school): AcademicPeriod
    {
        $year = AcademicYear::factory()->create(['school_id' => $school->id]);

        return AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $year->id,
            'status' => AcademicPeriodStatus::Closing,
        ]);
    }

    private function learnerWithAResult(School $school, AcademicPeriod $period, string $name, ?string $subjectName = null): StudentRecord
    {
        $learner = StudentRecord::factory()->create([
            'school_id' => $school->id,
            'user_id' => $this->memberOf($school, User::factory()->create(['name' => $name]))->id,
        ]);
        $offering = $this->offering($school, $period, $subjectName);
        ResultSnapshot::create(['school_id' => $school->id, 'student_record_id' => $learner->id, 'course_offering_id' => $offering->id, 'revision' => 1, 'percentage' => 70, 'payload' => ['percentage' => 70], 'published_at' => now()]);

        return $learner;
    }

    private function offering(School $school, AcademicPeriod $period, ?string $subjectName = null): CourseOffering
    {
        $subject = Subject::factory()->create(array_filter([
            'school_id' => $school->id,
            'name' => $subjectName,
        ], fn (mixed $value): bool => $value !== null));

        return CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $period->academic_year_id,
            'academic_period_id' => $period->id,
            'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $school->id])->id,
            'subject_id' => $subject->id,
        ]);
    }
}
