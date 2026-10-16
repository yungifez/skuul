<?php

namespace Tests\Feature;

use App\Actions\Syllabus\PublishSyllabus;
use App\Actions\Syllabus\ReturnSyllabus;
use App\Enums\CourseOfferingStatus;
use App\Enums\LessonNoteStatus;
use App\Enums\SyllabusStatus;
use App\Enums\TopicCoverageStatus;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\LessonNote;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\SyllabusTopicCoverage;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Notifications\SyllabusWorkNotification;
use App\Services\Syllabus\LessonNoteService;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SyllabusNotificationTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private const REVIEWER_PERMISSIONS = ['read syllabus', 'update syllabus', 'approve syllabus'];

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    public function test_the_author_hears_that_their_syllabus_was_approved(): void
    {
        $author = $this->user();
        $syllabus = $this->syllabus(SyllabusStatus::Submitted, ['submitted_by' => $author->id]);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        app(PublishSyllabus::class)->publish($syllabus, auth()->user());

        Notification::assertSentTo($author, SyllabusWorkNotification::class, fn (SyllabusWorkNotification $notification): bool => str_contains($notification->subject, 'approved')
            && $notification->url === route('syllabi.show', $syllabus));
    }

    public function test_the_author_hears_why_their_syllabus_was_sent_back(): void
    {
        $author = $this->user();
        $syllabus = $this->syllabus(SyllabusStatus::Submitted, ['submitted_by' => $author->id]);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        app(ReturnSyllabus::class)->sendBack($syllabus, 'Add week 3 objectives', auth()->user());

        Notification::assertSentTo($author, SyllabusWorkNotification::class, fn (SyllabusWorkNotification $notification): bool => in_array('Add week 3 objectives', $notification->lines, true)
            && $notification->url === route('syllabi.edit', $syllabus));
    }

    public function test_a_reviewer_who_publishes_their_own_draft_gets_no_message(): void
    {
        $this->authorized_user(self::REVIEWER_PERMISSIONS);
        $syllabus = $this->syllabus(SyllabusStatus::Submitted, ['submitted_by' => auth()->id()]);

        app(PublishSyllabus::class)->publish($syllabus, auth()->user());

        Notification::assertNothingSent();
    }

    public function test_the_author_hears_how_the_review_of_a_lesson_note_ended(): void
    {
        $syllabus = $this->syllabus(SyllabusStatus::Published);
        $author = $this->user();
        $approved = LessonNote::factory()->submitted()->create(['course_offering_id' => $syllabus->course_offering_id, 'user_id' => $author->id, 'week' => 1]);
        $returned = LessonNote::factory()->submitted()->create(['course_offering_id' => $syllabus->course_offering_id, 'user_id' => $author->id, 'week' => 2]);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        app(LessonNoteService::class)->approve($approved, auth()->user());
        app(LessonNoteService::class)->sendBack($returned, 'Say how you will check learning', auth()->user());

        $this->assertSame(LessonNoteStatus::Returned, $returned->fresh()->status);
        Notification::assertSentToTimes($author, SyllabusWorkNotification::class, 2);
        Notification::assertSentTo($author, SyllabusWorkNotification::class, fn (SyllabusWorkNotification $notification): bool => $notification->subject === 'Week 1 lesson note approved');
        Notification::assertSentTo($author, SyllabusWorkNotification::class, fn (SyllabusWorkNotification $notification): bool => in_array('Say how you will check learning', $notification->lines, true));
    }

    public function test_teachers_hear_which_classes_are_behind_the_plan(): void
    {
        $syllabus = $this->syllabus(SyllabusStatus::Published);
        $teacher = $this->user();
        $this->assign($syllabus, $teacher);
        $unrelated = $this->user();

        $this->artisan('skuul:send-syllabus-behind-reminders')->assertSuccessful();

        Notification::assertSentTo($teacher, SyllabusWorkNotification::class, fn (SyllabusWorkNotification $notification): bool => collect($notification->lines)->contains(fn (string $line): bool => str_contains($line, '1 topic behind')));
        Notification::assertNotSentTo($unrelated, SyllabusWorkNotification::class);
    }

    public function test_teachers_on_track_get_no_reminder(): void
    {
        $syllabus = $this->syllabus(SyllabusStatus::Published);
        SyllabusTopicCoverage::factory()->create(['syllabus_topic_id' => $syllabus->topics()->firstOrFail()->id, 'status' => TopicCoverageStatus::Covered]);
        $this->assign($syllabus, $this->user());

        $this->artisan('skuul:send-syllabus-behind-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    private function user(): User
    {
        return User::query()->findOrFail(User::factory()->create()->getKey());
    }

    private function assign(Syllabus $syllabus, User $teacher): void
    {
        $courseOffering = $syllabus->courseOffering;

        TeachingAssignment::create([
            'school_id' => $courseOffering->school_id,
            'subject_id' => $courseOffering->subject_id,
            'user_id' => $teacher->id,
            'academic_year_id' => $courseOffering->academic_year_id,
            'academic_period_id' => $courseOffering->academic_period_id,
            'course_offering_id' => $courseOffering->id,
            'starts_on' => now()->subMonth()->toDateString(),
        ]);
    }

    /**
     * Make a syllabus in a period that began three weeks ago, with one topic planned for week 1.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function syllabus(SyllabusStatus $status, array $attributes = []): Syllabus
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->getKey(),
            'starts_on' => now()->subWeeks(3)->toDateString(),
            'ends_on' => now()->addWeeks(9)->toDateString(),
        ]);
        $courseOffering = CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->getKey(),
            'academic_period_id' => $academicPeriod->getKey(),
            'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $school->id])->getKey(),
            'subject_id' => Subject::factory()->create(['school_id' => $school->id])->getKey(),
            'status' => CourseOfferingStatus::Active,
        ]);
        $syllabus = Syllabus::factory()->create(['course_offering_id' => $courseOffering->getKey(), 'status' => $status, ...$attributes]);
        SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => 1]);

        return $syllabus->load('courseOffering');
    }
}
