<?php

namespace Tests\Feature;

use App\Actions\Syllabus\PublishSyllabus;
use App\Actions\Syllabus\ReviseSyllabus;
use App\Enums\GradeAggregation;
use App\Enums\GradeItemType;
use App\Livewire\SyllabusAssessmentPlan;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\GradeCategory;
use App\Models\GradeItem;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\TeachingAssignment;
use App\Services\Syllabus\AssessmentPlanService;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SyllabusAssessmentPlanTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_plan_gives_each_assessment_its_share_of_the_final_grade(): void
    {
        $syllabus = $this->publishedSyllabus();
        $continuous = $this->category($syllabus, 'Continuous assessment', 2);
        $exam = $this->category($syllabus, 'Examination', 3);
        $this->item($syllabus, 'Test 1', $continuous, weight: 1);
        $this->item($syllabus, 'Test 2', $continuous, weight: 3);
        $this->item($syllabus, 'Final exam', $exam);

        $plan = app(AssessmentPlanService::class)->plan($syllabus);

        $this->assertSame(['Continuous assessment', 'Examination'], array_column($plan, 'name'));
        $this->assertSame([40.0, 60.0], array_column($plan, 'share'));
        $this->assertSame([10.0, 30.0], array_column($plan[0]['items'], 'share'));
        $this->assertSame([60.0], array_column($plan[1]['items'], 'share'));
    }

    public function test_a_category_that_keeps_the_best_result_gives_no_item_share(): void
    {
        $syllabus = $this->publishedSyllabus();
        $quizzes = $this->category($syllabus, 'Quizzes', 1, GradeAggregation::Highest);
        $this->item($syllabus, 'Quiz 1', $quizzes);

        $plan = app(AssessmentPlanService::class)->plan($syllabus);

        $this->assertSame(100.0, $plan[0]['share']);
        $this->assertNull($plan[0]['items'][0]['share']);
    }

    public function test_a_teacher_links_an_assessment_to_the_topics_it_tests(): void
    {
        $syllabus = $this->publishedSyllabus();
        [$first, $second] = $syllabus->topics()->get()->all();
        $item = $this->item($syllabus, 'Test 1');
        $this->teacherOf($syllabus->courseOffering);

        Livewire::test(SyllabusAssessmentPlan::class, ['syllabus' => $syllabus])
            ->assertSee('Not tested by any assessment')
            ->call('edit', $item->id)
            ->set('topicIds', [(string) $first->id, (string) $second->id])
            ->call('saveTopics')
            ->assertSee('Tests: '.$first->title.', '.$second->title);

        $this->assertEqualsCanonicalizing([$first->id, $second->id], $item->syllabusTopics()->pluck('syllabus_topics.id')->all());
        $this->assertSame(['week' => 3], app(AssessmentPlanService::class)->untestedTopics($syllabus)->map->only('week')->sole());
    }

    public function test_a_topic_from_another_syllabus_cannot_be_linked(): void
    {
        $syllabus = $this->publishedSyllabus();
        $item = $this->item($syllabus, 'Test 1');
        $otherTopic = SyllabusTopic::factory()->create();
        $this->teacherOf($syllabus->courseOffering);

        Livewire::test(SyllabusAssessmentPlan::class, ['syllabus' => $syllabus])
            ->call('edit', $item->id)
            ->set('topicIds', [(string) $otherTopic->id])
            ->call('saveTopics');

        $this->assertSame(0, $item->syllabusTopics()->count());
    }

    public function test_staff_without_gradebook_rights_cannot_link_topics(): void
    {
        $syllabus = $this->publishedSyllabus();
        $item = $this->item($syllabus, 'Test 1');
        $this->authorized_user(['read syllabus']);

        Livewire::test(SyllabusAssessmentPlan::class, ['syllabus' => $syllabus])
            ->assertSee('Test 1')
            ->assertDontSee('Choose topics')
            ->call('edit', $item->id)
            ->assertForbidden();
    }

    public function test_an_assessment_due_before_its_topic_is_taught_is_flagged(): void
    {
        $syllabus = $this->publishedSyllabus();
        $late = $syllabus->topics()->where('week', 3)->firstOrFail();
        $item = $this->item($syllabus, 'Early test', dueOn: $syllabus->courseOffering->academicPeriod->starts_on->copy()->addDays(8)->toDateString());
        $item->syllabusTopics()->attach($late->id);

        $plan = app(AssessmentPlanService::class)->plan($syllabus);

        $this->assertSame(2, $plan[0]['items'][0]['week']);
        $this->assertSame([$late->title], $plan[0]['items'][0]['early_topics']);
    }

    public function test_a_new_revision_keeps_the_topics_each_assessment_tests(): void
    {
        $syllabus = $this->publishedSyllabus();
        $topic = $syllabus->topics()->firstOrFail();
        $item = $this->item($syllabus, 'Test 1');
        $item->syllabusTopics()->attach($topic->id);
        $this->authorized_user(['read syllabus', 'update syllabus', 'approve syllabus']);

        $revision = app(ReviseSyllabus::class)->revise($syllabus, ['change_note' => 'Reorder'], auth()->user());
        app(PublishSyllabus::class)->publish($revision, auth()->user());

        $this->assertSame(
            [$revision->topics()->where('copied_from_id', $topic->id)->firstOrFail()->id],
            $item->syllabusTopics()->pluck('syllabus_topics.id')->all(),
        );
    }

    public function test_the_syllabus_page_shows_the_assessment_plan(): void
    {
        $syllabus = $this->publishedSyllabus();
        $this->item($syllabus, 'Mid-term test');
        $this->authorized_user(['read syllabus']);

        $this->get(route('syllabi.show', $syllabus))->assertOk()->assertSee('Assessment plan')->assertSee('Mid-term test');
    }

    private function teacherOf(CourseOffering $courseOffering): void
    {
        $this->authorized_user(['read syllabus', 'update syllabus', 'read gradebook', 'manage gradebook']);

        TeachingAssignment::create([
            'school_id' => $courseOffering->school_id,
            'subject_id' => $courseOffering->subject_id,
            'user_id' => auth()->id(),
            'academic_year_id' => $courseOffering->academic_year_id,
            'academic_period_id' => $courseOffering->academic_period_id,
            'course_offering_id' => $courseOffering->id,
            'starts_on' => now()->toDateString(),
        ]);
    }

    private function category(Syllabus $syllabus, string $name, float $weight, GradeAggregation $aggregation = GradeAggregation::WeightedMean): GradeCategory
    {
        return GradeCategory::create([
            'school_id' => $syllabus->courseOffering->school_id,
            'course_offering_id' => $syllabus->course_offering_id,
            'name' => $name,
            'weight' => $weight,
            'aggregation' => $aggregation,
        ]);
    }

    private function item(Syllabus $syllabus, string $name, ?GradeCategory $category = null, float $weight = 1, ?string $dueOn = null): GradeItem
    {
        return GradeItem::create([
            'school_id' => $syllabus->courseOffering->school_id,
            'course_offering_id' => $syllabus->course_offering_id,
            'grade_category_id' => $category?->id,
            'name' => $name,
            'type' => GradeItemType::Numeric,
            'max_points' => 20,
            'weight' => $weight,
            'due_on' => $dueOn,
        ]);
    }

    private function publishedSyllabus(): Syllabus
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->getKey(),
            'starts_on' => now()->subWeeks(1)->toDateString(),
            'ends_on' => now()->addWeeks(11)->toDateString(),
        ]);
        $courseOffering = CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->getKey(),
            'academic_period_id' => $academicPeriod->getKey(),
            'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $school->id])->getKey(),
            'subject_id' => Subject::factory()->create(['school_id' => $school->id])->getKey(),
        ]);
        $syllabus = Syllabus::factory()->published()->create(['course_offering_id' => $courseOffering->getKey()]);

        foreach ([1, 2, 3] as $week) {
            SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => $week, 'position' => $week]);
        }

        return $syllabus->load('courseOffering.academicPeriod');
    }
}
