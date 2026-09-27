<?php

namespace Tests\Feature;

use App\Actions\Syllabus\PublishSyllabus;
use App\Actions\Syllabus\ReviseSyllabus;
use App\Enums\AcademicPeriodStatus;
use App\Enums\CourseOfferingStatus;
use App\Enums\SyllabusStatus;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Livewire\ShowSyllabus;
use App\Livewire\SyllabusTopicsEditor;
use App\Livewire\SyllabusWorkflowControl;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SyllabusSchemeOfWorkTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_syllabus_can_be_created_without_a_pdf_and_starts_as_a_draft(): void
    {
        $courseOffering = $this->courseOffering();

        $this->authorized_user(['create syllabus', 'approve syllabus'])
            ->post('/dashboard/syllabi', [
                'name' => 'Algebra plan',
                'course_offering_id' => $courseOffering->id,
            ])->assertRedirect();

        $syllabus = Syllabus::query()->where('name', 'Algebra plan')->firstOrFail();
        $this->assertNull($syllabus->file);
        $this->assertSame(SyllabusStatus::Draft, $syllabus->status);
    }

    public function test_an_archived_offering_takes_no_new_syllabus(): void
    {
        $courseOffering = $this->courseOffering(['status' => CourseOfferingStatus::Archived]);

        $this->authorized_user(['create syllabus', 'approve syllabus'])
            ->post('/dashboard/syllabi', [
                'name' => 'Late plan',
                'course_offering_id' => $courseOffering->id,
            ])->assertSessionHas('danger');

        $this->assertDatabaseMissing('syllabi', ['name' => 'Late plan']);
    }

    public function test_a_draft_needs_a_topic_before_it_is_published(): void
    {
        $syllabus = $this->draft();
        $this->authorized_user(['update syllabus', 'approve syllabus']);

        Livewire::test(SyllabusWorkflowControl::class, ['syllabus' => $syllabus])
            ->call('publish')
            ->assertNoRedirect();
        $this->assertSame(SyllabusStatus::Draft, $syllabus->fresh()->status);

        SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id]);

        Livewire::test(SyllabusWorkflowControl::class, ['syllabus' => $syllabus->fresh()])
            ->call('publish')
            ->assertRedirect(route('syllabi.show', $syllabus));
        $this->assertSame(SyllabusStatus::Published, $syllabus->fresh()->status);
    }

    public function test_staff_plan_weekly_topics_on_a_draft(): void
    {
        $syllabus = $this->draft();
        $this->authorized_user(['update syllabus', 'approve syllabus']);

        $component = Livewire::test(SyllabusTopicsEditor::class, ['syllabus' => $syllabus])
            ->set('week', 2)
            ->set('title', 'Quadratic equations')
            ->set('objectives', 'Solve by factorising')
            ->call('saveTopic')
            ->assertHasNoErrors()
            ->assertSee('Quadratic equations');

        $topic = $syllabus->topics()->firstOrFail();
        $this->assertSame(2, $topic->week);
        $this->assertSame('Solve by factorising', $topic->objectives);

        $component->call('editTopic', $topic->id)
            ->set('title', 'Quadratic equations and graphs')
            ->call('saveTopic')
            ->assertHasNoErrors();
        $this->assertSame('Quadratic equations and graphs', $topic->fresh()->title);

        $component->call('deleteTopic', $topic->id);
        $this->assertModelMissing($topic);
    }

    public function test_a_topic_needs_a_title_and_a_sensible_week(): void
    {
        $syllabus = $this->draft();
        $this->authorized_user(['update syllabus', 'approve syllabus']);

        Livewire::test(SyllabusTopicsEditor::class, ['syllabus' => $syllabus])
            ->set('week', 0)
            ->set('title', '')
            ->call('saveTopic')
            ->assertHasErrors(['title' => 'required', 'week' => 'min']);

        $this->assertSame(0, $syllabus->topics()->count());
    }

    public function test_a_published_syllabus_refuses_topic_changes(): void
    {
        $syllabus = $this->published();
        $topic = $syllabus->topics()->firstOrFail();
        $this->authorized_user(['update syllabus', 'approve syllabus']);

        Livewire::test(SyllabusTopicsEditor::class, ['syllabus' => $syllabus])
            ->set('title', 'Sneaked in')
            ->call('saveTopic')
            ->call('deleteTopic', $topic->id);

        $this->assertSame(1, $syllabus->topics()->count());
        $this->assertModelExists($topic);
    }

    public function test_a_user_without_update_permission_cannot_change_topics(): void
    {
        $syllabus = $this->draft();
        $this->authorized_user(['read syllabus']);

        Livewire::test(SyllabusTopicsEditor::class, ['syllabus' => $syllabus])
            ->set('title', 'Not allowed')
            ->call('saveTopic')
            ->assertForbidden();

        $this->assertSame(0, $syllabus->topics()->count());
    }

    public function test_a_closed_period_freezes_the_topics(): void
    {
        $syllabus = $this->draft();
        $syllabus->courseOffering->academicPeriod->forceFill(['status' => AcademicPeriodStatus::Closed])->saveQuietly();

        $this->expectException(ClosedPeriodException::class);

        SyllabusTopic::query()->create(['syllabus_id' => $syllabus->id, 'title' => 'Late topic']);
    }

    public function test_staff_edit_the_details_of_a_draft_and_replace_its_pdf(): void
    {
        Storage::fake('public');
        $syllabus = $this->draft(['file' => UploadedFile::fake()->create('old.pdf', 10)->store('syllabus', 'public')]);
        $oldFile = $syllabus->file;

        $this->authorized_user(['update syllabus', 'approve syllabus'])
            ->put(route('syllabi.update', $syllabus), [
                'name' => 'Renamed plan',
                'description' => 'New overview',
                'file' => UploadedFile::fake()->create('new.pdf', 10, 'application/pdf'),
            ])->assertRedirect(route('syllabi.edit', $syllabus));

        $syllabus->refresh();
        $this->assertSame('Renamed plan', $syllabus->name);
        $this->assertNotSame($oldFile, $syllabus->file);
        Storage::disk('public')->assertExists($syllabus->file);
        Storage::disk('public')->assertMissing($oldFile);
    }

    public function test_a_published_syllabus_cannot_be_edited_in_place(): void
    {
        $syllabus = $this->published();
        $staff = $this->authorized_user(['update syllabus', 'approve syllabus']);

        $staff->get(route('syllabi.edit', $syllabus))->assertRedirect(route('syllabi.show', $syllabus));
        $staff->put(route('syllabi.update', $syllabus), ['name' => 'Changed'])->assertSessionHas('danger');

        $this->assertNotSame('Changed', $syllabus->fresh()->name);
    }

    public function test_the_edit_screen_renders_for_a_draft(): void
    {
        $syllabus = $this->draft();
        SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'title' => 'Fractions and decimals']);

        $this->authorized_user(['update syllabus', 'read syllabus', 'approve syllabus'])
            ->get(route('syllabi.edit', $syllabus))
            ->assertOk()
            ->assertSee('Fractions and decimals')
            ->assertSee('Add a topic');
    }

    public function test_a_revision_carries_the_topics_and_the_reason_for_the_change(): void
    {
        $syllabus = $this->published();
        $this->authorized_user(['update syllabus', 'approve syllabus']);

        $control = Livewire::test(SyllabusWorkflowControl::class, ['syllabus' => $syllabus])
            ->call('revise')
            ->assertHasErrors(['changeNote' => 'required']);

        $control->set('changeNote', 'Swap weeks 3 and 4')->call('revise');

        $revision = $syllabus->revisions()->firstOrFail();
        $control->assertRedirect(route('syllabi.edit', $revision));
        $this->assertSame('Swap weeks 3 and 4', $revision->change_note);
        $this->assertSame(
            $syllabus->topics()->pluck('title')->all(),
            $revision->topics()->pluck('title')->all(),
        );
    }

    public function test_only_one_draft_revision_can_be_open_at_a_time(): void
    {
        $syllabus = $this->published();
        app(ReviseSyllabus::class)->revise($syllabus, ['change_note' => 'First']);

        $this->expectException(InvalidValueException::class);

        app(ReviseSyllabus::class)->revise($syllabus, ['change_note' => 'Second']);
    }

    public function test_a_stale_draft_cannot_replace_a_newer_revision(): void
    {
        $syllabus = $this->published();
        $current = app(ReviseSyllabus::class)->revise($syllabus, ['change_note' => 'Current']);
        $stale = Syllabus::factory()->create([
            'course_offering_id' => $syllabus->course_offering_id,
            'revision' => 2,
            'revision_of_id' => $syllabus->id,
        ]);
        SyllabusTopic::factory()->create(['syllabus_id' => $stale->id]);
        app(PublishSyllabus::class)->publish($current);

        try {
            app(PublishSyllabus::class)->publish($stale);
            $this->fail('A stale draft was published.');
        } catch (InvalidValueException) {
            $this->assertSame(SyllabusStatus::Draft, $stale->fresh()->status);
            $this->assertSame(SyllabusStatus::Published, $current->fresh()->status);
        }
    }

    public function test_deleting_a_draft_revision_keeps_the_published_pdf(): void
    {
        Storage::fake('public');
        $syllabus = $this->published(['file' => UploadedFile::fake()->create('plan.pdf', 10)->store('syllabus', 'public')]);
        $revision = app(ReviseSyllabus::class)->revise($syllabus, ['change_note' => 'Try a new order']);

        $this->authorized_user(['delete syllabus', 'update syllabus', 'approve syllabus'])
            ->delete(route('syllabi.destroy', $revision))
            ->assertRedirect(route('syllabi.index'));

        $this->assertModelMissing($revision);
        Storage::disk('public')->assertExists($syllabus->file);
    }

    public function test_a_reader_downloads_the_pdf_through_the_policy(): void
    {
        Storage::fake('public');
        $syllabus = $this->published(['file' => UploadedFile::fake()->create('plan.pdf', 10)->store('syllabus', 'public')]);
        $syllabus->load('courseOffering');

        $this->authorized_user(['read syllabus']);
        Livewire::test(ShowSyllabus::class, ['syllabus' => $syllabus])
            ->call('download')
            ->assertFileDownloaded(str($syllabus->name)->slug().'.pdf');

        $this->unauthorized_user();
        Livewire::test(ShowSyllabus::class, ['syllabus' => $syllabus])
            ->assertForbidden();
    }

    public function test_the_show_screen_marks_the_current_teaching_week(): void
    {
        Carbon::setTestNow('2026-09-16');
        $courseOffering = $this->courseOffering();
        $courseOffering->academicPeriod->forceFill(['starts_on' => '2026-09-07', 'ends_on' => '2026-12-18'])->saveQuietly();
        $syllabus = Syllabus::factory()->published()->create(['course_offering_id' => $courseOffering->id]);
        SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => 2, 'title' => 'Ratio and proportion']);

        $this->assertSame(2, $syllabus->fresh()->teachingWeekOn());

        $this->authorized_user(['read syllabus'])
            ->get(route('syllabi.show', $syllabus))
            ->assertOk()
            ->assertSee('This is week 2 of the', false)
            ->assertSee('Ratio and proportion');
    }

    public function test_no_teaching_week_is_given_outside_the_period(): void
    {
        $courseOffering = $this->courseOffering();
        $courseOffering->academicPeriod->forceFill(['starts_on' => '2026-09-07', 'ends_on' => '2026-12-18'])->saveQuietly();
        $syllabus = Syllabus::factory()->create(['course_offering_id' => $courseOffering->id]);

        $this->assertNull($syllabus->teachingWeekOn(Carbon::parse('2026-09-06')));
        $this->assertNull($syllabus->teachingWeekOn(Carbon::parse('2026-12-19')));
        $this->assertSame(1, $syllabus->teachingWeekOn(Carbon::parse('2026-09-07')));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function draft(array $attributes = []): Syllabus
    {
        return Syllabus::factory()->create(['course_offering_id' => $this->courseOffering()->id, ...$attributes]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function published(array $attributes = []): Syllabus
    {
        $syllabus = Syllabus::factory()->published()->create(['course_offering_id' => $this->courseOffering()->id, ...$attributes]);
        SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => 1, 'title' => 'Number bases']);

        return $syllabus;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function courseOffering(array $attributes = []): CourseOffering
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
        ]);

        return CourseOffering::query()->findOrFail(CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $school->id])->id,
            'subject_id' => Subject::factory()->create(['school_id' => $school->id])->id,
            ...$attributes,
        ])->getKey());
    }
}
