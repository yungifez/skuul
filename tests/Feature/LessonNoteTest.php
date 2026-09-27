<?php

namespace Tests\Feature;

use App\Actions\Syllabus\PublishSyllabus;
use App\Actions\Syllabus\ReviseSyllabus;
use App\Enums\AuditAction;
use App\Enums\LessonNoteStatus;
use App\Enums\RosterMode;
use App\Enums\SyllabusStatus;
use App\Livewire\LessonNoteBook;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\LessonNote;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LessonNoteTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private const TEACHER_PERMISSIONS = ['read syllabus', 'create syllabus', 'update syllabus', 'delete syllabus'];

    private const REVIEWER_PERMISSIONS = ['read syllabus', 'update syllabus', 'approve syllabus'];

    public function test_a_teacher_writes_a_draft_note_for_a_planned_topic(): void
    {
        $syllabus = $this->publishedSyllabus();
        $topic = $syllabus->topics()->firstOrFail();
        $teacher = $this->teacherOf($syllabus->courseOffering);

        Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus])
            ->set('week', 2)
            ->set('topicId', (string) $topic->id)
            ->set('objectives', 'Solve linear equations')
            ->set('activities', 'Worked examples, then pair practice')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Solve linear equations');

        $note = LessonNote::query()->sole();
        $this->assertSame(LessonNoteStatus::Draft, $note->status);
        $this->assertSame($teacher->id, $note->user_id);
        $this->assertSame($topic->id, $note->syllabus_topic_id);
        $this->assertSame(2, $note->week);
        $this->assertNull($note->academic_cycle_section_id);
    }

    public function test_a_note_needs_objectives_and_activities(): void
    {
        $syllabus = $this->publishedSyllabus();
        $this->teacherOf($syllabus->courseOffering);

        Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus])
            ->set('week', 1)
            ->call('save')
            ->assertHasErrors(['objectives' => 'required', 'activities' => 'required']);

        $this->assertSame(0, LessonNote::query()->count());
    }

    public function test_a_topic_from_another_syllabus_is_refused(): void
    {
        $syllabus = $this->publishedSyllabus();
        $otherTopic = SyllabusTopic::factory()->create();
        $this->teacherOf($syllabus->courseOffering);

        Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus])
            ->set('topicId', (string) $otherTopic->id)
            ->set('objectives', 'Objectives')
            ->set('activities', 'Activities')
            ->call('save')
            ->assertHasErrors('topicId');
    }

    public function test_a_teacher_writes_one_note_per_week_for_a_class(): void
    {
        $syllabus = $this->publishedSyllabus();
        $teacher = $this->teacherOf($syllabus->courseOffering);
        $this->note($syllabus, $teacher, ['week' => 3]);

        Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus])
            ->set('week', 3)
            ->set('objectives', 'Second note')
            ->set('activities', 'Activities')
            ->call('save');

        $this->assertSame(1, LessonNote::query()->count());
    }

    public function test_a_teacher_sends_a_note_for_review_and_cannot_change_it_afterwards(): void
    {
        $syllabus = $this->publishedSyllabus();
        $teacher = $this->teacherOf($syllabus->courseOffering);
        $note = $this->note($syllabus, $teacher);

        $component = Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus])
            ->call('submit', $note->id);

        $note->refresh();
        $this->assertSame(LessonNoteStatus::Submitted, $note->status);
        $this->assertNotNull($note->submitted_at);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::LessonNoteSubmitted)->forSubject($note)->first());

        $component->call('edit', $note->id)->assertForbidden();
    }

    public function test_a_teacher_cannot_approve_their_own_note(): void
    {
        $syllabus = $this->publishedSyllabus();
        $this->authorized_user(self::REVIEWER_PERMISSIONS);
        /** @var User $reviewer */
        $reviewer = auth()->user();
        $note = $this->note($syllabus, $reviewer, ['status' => LessonNoteStatus::Submitted]);

        Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus])
            ->assertDontSee('Approve<span class="sr-only"> the note for week', false)
            ->call('approve', $note->id)
            ->assertForbidden();

        $this->assertSame(LessonNoteStatus::Submitted, $note->fresh()->status);
    }

    public function test_a_reviewer_approves_a_note(): void
    {
        $syllabus = $this->publishedSyllabus();
        $note = LessonNote::factory()->submitted()->create(['course_offering_id' => $syllabus->course_offering_id]);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus])
            ->call('approve', $note->id);

        $note->refresh();
        $this->assertSame(LessonNoteStatus::Approved, $note->status);
        $this->assertSame(auth()->id(), $note->reviewed_by);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::LessonNoteApproved)->forSubject($note)->first());
    }

    public function test_a_reviewer_sends_a_note_back_with_the_changes_it_needs(): void
    {
        $syllabus = $this->publishedSyllabus();
        $note = LessonNote::factory()->submitted()->create(['course_offering_id' => $syllabus->course_offering_id]);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus])
            ->call('sendBack', $note->id)
            ->assertHasErrors(["reviewNotes.{$note->id}" => 'required'])
            ->set("reviewNotes.{$note->id}", 'Add an evaluation')
            ->call('sendBack', $note->id)
            ->assertHasNoErrors();

        $note->refresh();
        $this->assertSame(LessonNoteStatus::Returned, $note->status);
        $this->assertSame('Add an evaluation', $note->review_note);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::LessonNoteReturned)->forSubject($note)->first());
    }

    public function test_the_author_fixes_a_returned_note_and_sends_it_again(): void
    {
        $syllabus = $this->publishedSyllabus();
        $teacher = $this->teacherOf($syllabus->courseOffering);
        $note = $this->note($syllabus, $teacher, ['status' => LessonNoteStatus::Returned, 'review_note' => 'Add an evaluation']);

        Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus])
            ->assertSee('Add an evaluation')
            ->call('edit', $note->id)
            ->set('evaluation', 'Exit quiz of five questions')
            ->call('save')
            ->assertHasNoErrors()
            ->call('submit', $note->id);

        $note->refresh();
        $this->assertSame('Exit quiz of five questions', $note->evaluation);
        $this->assertSame(LessonNoteStatus::Submitted, $note->status);
    }

    public function test_a_teacher_deletes_a_draft_but_not_an_approved_note(): void
    {
        $syllabus = $this->publishedSyllabus();
        $teacher = $this->teacherOf($syllabus->courseOffering);
        $draft = $this->note($syllabus, $teacher, ['week' => 1]);
        $approved = $this->note($syllabus, $teacher, ['week' => 2, 'status' => LessonNoteStatus::Approved]);

        $component = Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus])
            ->call('delete', $draft->id);
        $this->assertModelMissing($draft);

        $component->call('delete', $approved->id)->assertForbidden();
        $this->assertModelExists($approved);
    }

    public function test_a_teacher_writes_notes_only_for_the_section_they_teach(): void
    {
        $courseOffering = $this->courseOffering(['roster_mode' => RosterMode::AcademicLevel]);
        $mine = $this->section($courseOffering, 'A');
        $theirs = $this->section($courseOffering, 'B');
        $syllabus = $this->publishedSyllabus($courseOffering);
        $this->teacherOf($courseOffering, $mine);

        Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus, 'track' => (string) $theirs->id])
            ->assertDontSee('Write a lesson note')
            ->set('objectives', 'Objectives')
            ->set('activities', 'Activities')
            ->call('save')
            ->assertForbidden();

        Livewire::test(LessonNoteBook::class, ['syllabus' => $syllabus, 'track' => (string) $mine->id])
            ->set('objectives', 'Objectives')
            ->set('activities', 'Activities')
            ->call('save');

        $this->assertSame([$mine->id], LessonNote::query()->pluck('academic_cycle_section_id')->all());
    }

    public function test_a_teacher_cannot_open_the_notes_of_an_offering_they_do_not_teach(): void
    {
        $syllabus = $this->publishedSyllabus();
        $this->teacherOf($this->courseOffering());

        $this->get(route('syllabi.lesson-notes', $syllabus))->assertForbidden();
    }

    public function test_lesson_notes_follow_the_published_syllabus_only(): void
    {
        $syllabus = $this->publishedSyllabus();
        $syllabus->update(['status' => SyllabusStatus::Draft]);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        $this->get(route('syllabi.lesson-notes', $syllabus))->assertForbidden();
    }

    public function test_a_teacher_opens_lesson_notes_from_the_syllabus(): void
    {
        $syllabus = $this->publishedSyllabus();
        $this->teacherOf($syllabus->courseOffering);

        $this->get(route('syllabi.show', $syllabus))->assertOk()->assertSee(route('syllabi.lesson-notes', $syllabus));
        $this->get(route('syllabi.lesson-notes', $syllabus))->assertOk()->assertSee('Write a lesson note');
    }

    public function test_the_index_lists_lesson_notes_waiting_for_review(): void
    {
        $syllabus = $this->publishedSyllabus();
        LessonNote::factory()->submitted()->count(2)->create(['course_offering_id' => $syllabus->course_offering_id]);

        $this->authorized_user(self::REVIEWER_PERMISSIONS)
            ->get(route('syllabi.index'))
            ->assertOk()
            ->assertSee('Lesson notes to review')
            ->assertSee('2 notes')
            ->assertSee(route('syllabi.lesson-notes', ['syllabus' => $syllabus, 'status' => 'submitted']), false);
    }

    public function test_a_new_revision_keeps_the_topic_of_each_note(): void
    {
        $syllabus = $this->publishedSyllabus();
        $topic = $syllabus->topics()->firstOrFail();
        $this->authorized_user(self::REVIEWER_PERMISSIONS);
        $note = LessonNote::factory()->create(['course_offering_id' => $syllabus->course_offering_id, 'syllabus_topic_id' => $topic->id]);

        $revision = app(ReviseSyllabus::class)->revise($syllabus, ['change_note' => 'Reorder'], auth()->user());
        app(PublishSyllabus::class)->publish($revision, auth()->user());

        $this->assertSame($revision->topics()->firstOrFail()->id, $note->fresh()->syllabus_topic_id);
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

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function note(Syllabus $syllabus, User $author, array $attributes = []): LessonNote
    {
        return LessonNote::factory()->create([
            'course_offering_id' => $syllabus->course_offering_id,
            'user_id' => $author->id,
            ...$attributes,
        ]);
    }

    private function publishedSyllabus(?CourseOffering $courseOffering = null): Syllabus
    {
        $syllabus = Syllabus::factory()->published()->create(['course_offering_id' => ($courseOffering ?? $this->courseOffering())->id]);
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
        $academicPeriod = AcademicPeriod::factory()->create(['school_id' => $school->id, 'academic_year_id' => $academicYear->getKey()]);

        return CourseOffering::query()->findOrFail(CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->getKey(),
            'academic_period_id' => $academicPeriod->getKey(),
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
