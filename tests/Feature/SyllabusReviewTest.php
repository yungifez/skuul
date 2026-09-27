<?php

namespace Tests\Feature;

use App\Enums\AuditAction;
use App\Enums\RosterMode;
use App\Enums\SyllabusStatus;
use App\Enums\TopicCoverageStatus;
use App\Livewire\SyllabusCoverageTracker;
use App\Livewire\SyllabusTopicsEditor;
use App\Livewire\SyllabusWorkflowControl;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\SyllabusTopicCoverage;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SyllabusReviewTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private const TEACHER_PERMISSIONS = ['read syllabus', 'create syllabus', 'update syllabus', 'delete syllabus'];

    public function test_a_teacher_sends_their_draft_for_review_and_cannot_publish_it(): void
    {
        $syllabus = $this->draftWithTopic();
        $teacher = $this->teacherOf($syllabus->courseOffering);

        Livewire::test(SyllabusWorkflowControl::class, ['syllabus' => $syllabus])
            ->assertDontSee('Approve and publish')
            ->call('publish')
            ->assertForbidden();

        Livewire::test(SyllabusWorkflowControl::class, ['syllabus' => $syllabus])
            ->call('submit')
            ->assertRedirect(route('syllabi.show', $syllabus));

        $syllabus->refresh();
        $this->assertSame(SyllabusStatus::Submitted, $syllabus->status);
        $this->assertSame($teacher->id, $syllabus->submitted_by);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::SyllabusSubmitted)->forSubject($syllabus)->first());
    }

    public function test_a_draft_without_topics_cannot_be_sent_for_review(): void
    {
        $syllabus = Syllabus::factory()->create(['course_offering_id' => $this->courseOffering()->id]);
        $this->teacherOf($syllabus->courseOffering);

        Livewire::test(SyllabusWorkflowControl::class, ['syllabus' => $syllabus])
            ->call('submit')
            ->assertNoRedirect();

        $this->assertSame(SyllabusStatus::Draft, $syllabus->fresh()->status);
    }

    public function test_a_submitted_syllabus_is_locked_until_it_is_taken_back(): void
    {
        $syllabus = $this->draftWithTopic();
        $syllabus->update(['status' => SyllabusStatus::Submitted]);
        $this->teacherOf($syllabus->courseOffering);

        Livewire::test(SyllabusTopicsEditor::class, ['syllabus' => $syllabus])
            ->set('title', 'Added during review')
            ->call('saveTopic');
        $this->assertSame(1, $syllabus->topics()->count());

        Livewire::test(SyllabusWorkflowControl::class, ['syllabus' => $syllabus])
            ->call('withdraw')
            ->assertRedirect(route('syllabi.edit', $syllabus));
        $this->assertSame(SyllabusStatus::Draft, $syllabus->fresh()->status);
    }

    public function test_a_reviewer_sends_a_syllabus_back_with_a_note(): void
    {
        $syllabus = $this->draftWithTopic();
        $syllabus->update(['status' => SyllabusStatus::Submitted]);
        $this->authorized_user(['read syllabus', 'update syllabus', 'approve syllabus']);

        Livewire::test(SyllabusWorkflowControl::class, ['syllabus' => $syllabus])
            ->call('sendBack')
            ->assertHasErrors(['reviewNote' => 'required'])
            ->set('reviewNote', 'Add objectives to week 2')
            ->call('sendBack')
            ->assertRedirect(route('syllabi.show', $syllabus));

        $syllabus->refresh();
        $this->assertSame(SyllabusStatus::Draft, $syllabus->status);
        $this->assertSame('Add objectives to week 2', $syllabus->review_note);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::SyllabusReturned)->forSubject($syllabus)->first());
    }

    public function test_a_reviewer_approves_a_submitted_syllabus(): void
    {
        $syllabus = $this->draftWithTopic();
        $syllabus->update(['status' => SyllabusStatus::Submitted, 'review_note' => 'Earlier note']);
        $this->authorized_user(['read syllabus', 'update syllabus', 'approve syllabus']);

        Livewire::test(SyllabusWorkflowControl::class, ['syllabus' => $syllabus])
            ->assertSee('Approve and publish')
            ->call('publish')
            ->assertRedirect(route('syllabi.show', $syllabus));

        $syllabus->refresh();
        $this->assertSame(SyllabusStatus::Published, $syllabus->status);
        $this->assertNull($syllabus->review_note);
        $this->assertSame(auth()->id(), $syllabus->published_by);
    }

    public function test_the_index_lists_syllabi_waiting_for_review_to_reviewers(): void
    {
        $syllabus = $this->draftWithTopic();
        $syllabus->update(['status' => SyllabusStatus::Submitted, 'submitted_at' => now()]);

        $this->authorized_user(['read syllabus', 'approve syllabus'])
            ->get(route('syllabi.index'))
            ->assertOk()
            ->assertSee('Awaiting your review')
            ->assertSee(route('syllabi.show', $syllabus));
    }

    public function test_the_review_queue_is_hidden_from_staff_who_do_not_review(): void
    {
        $syllabus = $this->draftWithTopic();
        $syllabus->update(['status' => SyllabusStatus::Submitted, 'submitted_at' => now()]);

        $this->authorized_user(['read syllabus'])
            ->get(route('syllabi.index'))
            ->assertOk()
            ->assertDontSee('Awaiting your review');
    }

    public function test_a_teacher_cannot_change_a_syllabus_for_an_offering_they_do_not_teach(): void
    {
        $syllabus = $this->draftWithTopic();
        $this->teacherOf($this->courseOffering());

        $this->get(route('syllabi.edit', $syllabus))->assertForbidden();

        Livewire::test(SyllabusTopicsEditor::class, ['syllabus' => $syllabus])
            ->set('title', 'Not mine')
            ->call('saveTopic')
            ->assertForbidden();
    }

    public function test_a_teacher_adds_a_syllabus_only_for_an_offering_they_teach(): void
    {
        $taught = $this->courseOffering();
        $other = $this->courseOffering();
        $this->teacherOf($taught);

        $this->post(route('syllabi.store'), ['name' => 'Not my class', 'course_offering_id' => $other->id])
            ->assertSessionHasErrors('course_offering_id');

        $this->post(route('syllabi.store'), ['name' => 'My class', 'course_offering_id' => $taught->id])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('syllabi', ['name' => 'My class', 'course_offering_id' => $taught->id]);
        $this->assertDatabaseMissing('syllabi', ['name' => 'Not my class']);
    }

    public function test_a_teacher_records_coverage_only_for_the_section_they_teach(): void
    {
        $courseOffering = $this->courseOffering(['roster_mode' => RosterMode::AcademicLevel]);
        $mine = $this->section($courseOffering, 'A');
        $theirs = $this->section($courseOffering, 'B');
        $syllabus = $this->draftWithTopic($courseOffering);
        $syllabus->update(['status' => SyllabusStatus::Published]);
        $topic = $syllabus->topics()->firstOrFail();
        $this->teacherOf($courseOffering, $mine);

        Livewire::test(SyllabusCoverageTracker::class, ['syllabus' => $syllabus, 'track' => (string) $mine->id])
            ->call('mark', $topic->id, TopicCoverageStatus::Covered->value);

        Livewire::test(SyllabusCoverageTracker::class, ['syllabus' => $syllabus, 'track' => (string) $theirs->id])
            ->call('mark', $topic->id, TopicCoverageStatus::Covered->value)
            ->assertForbidden();

        $this->assertSame([$mine->id], SyllabusTopicCoverage::query()->pluck('academic_cycle_section_id')->all());
    }

    public function test_the_coverage_report_is_for_reviewers(): void
    {
        $this->teacherOf($this->courseOffering());

        $this->get(route('syllabi.coverage'))->assertForbidden();
    }

    public function test_school_administrators_can_approve_syllabi(): void
    {
        $this->assertTrue(
            Role::query()->where('name', 'admin')->firstOrFail()->hasPermissionTo('approve syllabus'),
        );
    }

    private function teacherOf(CourseOffering $courseOffering, ?AcademicCycleSection $section = null): User
    {
        $this->authorized_user(self::TEACHER_PERMISSIONS);
        /** @var User $teacher */
        $teacher = auth()->user();

        TeachingAssignment::create([
            'school_id' => $courseOffering->school_id,
            'subject_id' => $courseOffering->subject_id,
            'user_id' => $teacher->id,
            'academic_year_id' => $courseOffering->academic_year_id,
            'academic_period_id' => $courseOffering->academic_period_id,
            'course_offering_id' => $courseOffering->id,
            'academic_cycle_section_id' => $section?->id,
            'starts_on' => now()->toDateString(),
        ]);

        return $teacher;
    }

    private function draftWithTopic(?CourseOffering $courseOffering = null): Syllabus
    {
        $syllabus = Syllabus::factory()->create(['course_offering_id' => ($courseOffering ?? $this->courseOffering())->id]);
        SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => 1]);

        return $syllabus->load('courseOffering');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function courseOffering(array $attributes = []): CourseOffering
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create(['school_id' => $school->id, 'academic_year_id' => $academicYear->id]);

        return CourseOffering::query()->findOrFail(CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $school->id])->id,
            'subject_id' => Subject::factory()->create(['school_id' => $school->id])->id,
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
