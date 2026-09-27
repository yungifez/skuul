<?php

namespace Tests\Feature;

use App\Actions\Syllabus\PublishSyllabus;
use App\Actions\Syllabus\ReviseSyllabus;
use App\Enums\RosterMode;
use App\Enums\TopicCoverageStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\SyllabusCoverageReport;
use App\Livewire\SyllabusCoverageTracker;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\SyllabusTopicCoverage;
use App\Services\Syllabus\SyllabusCoverageService;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class SyllabusCoverageTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_teacher_marks_a_topic_as_covered_for_the_class(): void
    {
        $syllabus = $this->publishedSyllabus();
        $topic = $syllabus->topics()->firstOrFail();
        $this->authorized_user(['read syllabus', 'update syllabus']);

        Livewire::test(SyllabusCoverageTracker::class, ['syllabus' => $syllabus])
            ->call('mark', $topic->id, TopicCoverageStatus::Covered->value)
            ->assertSee('1 of 3');

        $coverage = SyllabusTopicCoverage::query()->sole();
        $this->assertSame(TopicCoverageStatus::Covered, $coverage->status);
        $this->assertNull($coverage->academic_cycle_section_id);
        $this->assertSame(now()->toDateString(), $coverage->covered_on->toDateString());
        $this->assertSame(auth()->id(), $coverage->recorded_by);
    }

    public function test_a_note_is_kept_and_the_mark_can_be_cleared(): void
    {
        $syllabus = $this->publishedSyllabus();
        $topic = $syllabus->topics()->firstOrFail();
        $this->authorized_user(['read syllabus', 'update syllabus']);

        $tracker = Livewire::test(SyllabusCoverageTracker::class, ['syllabus' => $syllabus])
            ->call('mark', $topic->id, TopicCoverageStatus::Partial->value)
            ->call('saveNote', $topic->id, 'Finish in week 3');
        $this->assertSame('Finish in week 3', SyllabusTopicCoverage::query()->sole()->note);

        $tracker->call('mark', $topic->id, TopicCoverageStatus::Covered->value);
        $this->assertSame('Finish in week 3', SyllabusTopicCoverage::query()->sole()->note);

        $tracker->call('mark', $topic->id, '');
        $this->assertSame(0, SyllabusTopicCoverage::query()->count());
    }

    public function test_a_reader_without_update_access_cannot_record_coverage(): void
    {
        $syllabus = $this->publishedSyllabus();
        $this->authorized_user(['read syllabus']);

        Livewire::test(SyllabusCoverageTracker::class, ['syllabus' => $syllabus])
            ->call('mark', $syllabus->topics()->firstOrFail()->id, TopicCoverageStatus::Covered->value)
            ->assertForbidden();

        $this->assertSame(0, SyllabusTopicCoverage::query()->count());
    }

    public function test_coverage_is_recorded_against_the_published_syllabus_only(): void
    {
        $syllabus = Syllabus::factory()->create(['course_offering_id' => $this->courseOffering()->id]);
        $topic = SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id]);

        $this->expectException(InvalidValueException::class);

        app(SyllabusCoverageService::class)->record($syllabus, $topic, null, TopicCoverageStatus::Covered);
    }

    public function test_a_whole_level_offering_is_tracked_section_by_section(): void
    {
        $courseOffering = $this->courseOffering(['roster_mode' => RosterMode::AcademicLevel]);
        $sectionA = $this->section($courseOffering, 'A');
        $sectionB = $this->section($courseOffering, 'B');
        $syllabus = $this->publishedSyllabus($courseOffering);
        $topic = $syllabus->topics()->firstOrFail();
        $service = app(SyllabusCoverageService::class);

        $this->assertSame([$sectionA->id, $sectionB->id], array_column($service->tracks($syllabus), 'id'));

        $service->record($syllabus, $topic, $sectionA->id, TopicCoverageStatus::Covered);

        $summary = collect($service->summary($syllabus))->keyBy('id');
        $this->assertSame(1, $summary[$sectionA->id]['covered']);
        $this->assertSame(0, $summary[$sectionB->id]['covered']);

        $this->expectException(InvalidValueException::class);
        $service->record($syllabus, $topic, null, TopicCoverageStatus::Covered);
    }

    public function test_a_class_is_behind_when_past_weeks_are_not_covered_or_skipped(): void
    {
        $syllabus = $this->publishedSyllabus();
        [$weekOne, $weekTwo] = $syllabus->topics()->get()->all();
        $service = app(SyllabusCoverageService::class);
        $service->record($syllabus, $weekOne, null, TopicCoverageStatus::Skipped);

        $summary = collect($service->summary($syllabus, Carbon::parse('2026-09-23')))->sole();

        $this->assertSame(3, $summary['total']);
        $this->assertSame(2, $summary['expected']);
        $this->assertSame(1, $summary['behind']);

        $service->record($syllabus, $weekTwo, null, TopicCoverageStatus::Partial);
        $this->assertSame(1, collect($service->summary($syllabus, Carbon::parse('2026-09-23')))->sole()['behind']);
    }

    public function test_coverage_moves_onto_the_new_revision_when_it_is_published(): void
    {
        $syllabus = $this->publishedSyllabus();
        $topic = $syllabus->topics()->firstOrFail();
        app(SyllabusCoverageService::class)->record($syllabus, $topic, null, TopicCoverageStatus::Covered);

        $revision = app(ReviseSyllabus::class)->revise($syllabus, ['change_note' => 'Reorder']);
        app(PublishSyllabus::class)->publish($revision);

        $coverage = SyllabusTopicCoverage::query()->sole();
        $this->assertSame($revision->id, $coverage->topic->syllabus_id);
        $this->assertSame($topic->id, $coverage->topic->copied_from_id);
    }

    public function test_the_report_lists_classes_most_behind_first(): void
    {
        Carbon::setTestNow('2026-09-23');
        $onTrack = $this->publishedSyllabus(subjectName: 'Chemistry');
        $behind = $this->publishedSyllabus($this->courseOffering([
            'academic_year_id' => $onTrack->courseOffering->academic_year_id,
            'academic_period_id' => $onTrack->courseOffering->academic_period_id,
        ], 'Geography'));
        $service = app(SyllabusCoverageService::class);
        $onTrack->topics()->get()->take(2)->each(fn (SyllabusTopic $topic) => $service->record($onTrack, $topic, null, TopicCoverageStatus::Covered));

        $this->authorized_user(['update syllabus']);

        Livewire::test(SyllabusCoverageReport::class, ['academicPeriodId' => (string) $behind->courseOffering->academic_period_id])
            ->assertSeeInOrder(['Geography', '2 topics', 'Chemistry', 'On track']);

        Livewire::test(SyllabusCoverageReport::class, ['academicPeriodId' => (string) $onTrack->courseOffering->academic_period_id])
            ->set('onlyBehind', true)
            ->assertSee('Geography')
            ->assertDontSee('Chemistry');
    }

    public function test_the_report_page_needs_update_access(): void
    {
        $this->authorized_user(['read syllabus'])
            ->get(route('syllabi.coverage'))
            ->assertForbidden();
    }

    public function test_the_report_page_renders_for_staff(): void
    {
        $this->publishedSyllabus();

        $this->authorized_user(['read syllabus', 'update syllabus'])
            ->get(route('syllabi.coverage'))
            ->assertOk()
            ->assertSee('Syllabus coverage');
    }

    public function test_the_show_page_carries_the_tracker_for_staff(): void
    {
        $syllabus = $this->publishedSyllabus();

        $this->authorized_user(['read syllabus', 'update syllabus'])
            ->get(route('syllabi.show', $syllabus))
            ->assertOk()
            ->assertSee('Not yet taught');
    }

    private function publishedSyllabus(?CourseOffering $courseOffering = null, string $subjectName = 'Mathematics'): Syllabus
    {
        $courseOffering ??= $this->courseOffering(subjectName: $subjectName);
        $syllabus = Syllabus::factory()->published()->create(['course_offering_id' => $courseOffering->id]);

        foreach ([1, 2, 3] as $week) {
            SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => $week, 'position' => $week, 'title' => "Week {$week} topic"]);
        }

        return $syllabus->load('courseOffering');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function courseOffering(array $attributes = [], string $subjectName = 'Mathematics'): CourseOffering
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'starts_on' => '2026-09-07',
            'ends_on' => '2026-12-18',
        ]);

        return CourseOffering::query()->findOrFail(CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $school->id])->id,
            'subject_id' => Subject::factory()->create(['school_id' => $school->id, 'name' => $subjectName])->id,
            ...$attributes,
        ])->getKey());
    }

    private function section(CourseOffering $courseOffering, string $name): AcademicCycleSection
    {
        return AcademicCycleSection::factory()->create([
            'school_id' => $courseOffering->school_id,
            'academic_year_id' => $courseOffering->academic_year_id,
            'academic_level_id' => $courseOffering->academic_level_id,
            'name' => $name,
        ]);
    }
}
