<?php

namespace Tests\Feature;

use App\Enums\SyllabusStatus;
use App\Livewire\CurriculumLibrary;
use App\Livewire\SaveSyllabusToLibrary;
use App\Livewire\SyllabusTopicImporter;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\CurriculumOutline;
use App\Models\CurriculumOutlineTopic;
use App\Models\School;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CurriculumLibraryTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private const REVIEWER_PERMISSIONS = ['read syllabus', 'update syllabus', 'approve syllabus'];

    public function test_a_reviewer_saves_a_published_syllabus_to_the_library(): void
    {
        $syllabus = $this->syllabus(SyllabusStatus::Published, topics: 2);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        Livewire::test(SaveSyllabusToLibrary::class, ['syllabus' => $syllabus])
            ->set('name', 'JSS1 Mathematics scheme')
            ->call('save');

        $outline = CurriculumOutline::query()->sole();
        $this->assertSame('JSS1 Mathematics scheme', $outline->name);
        $this->assertSame($syllabus->courseOffering->subject_id, $outline->subject_id);
        $this->assertSame($syllabus->courseOffering->academic_level_id, $outline->academic_level_id);
        $this->assertSame($syllabus->topics()->pluck('title')->all(), $outline->topics()->pluck('title')->all());
    }

    public function test_only_reviewers_add_to_the_library(): void
    {
        $syllabus = $this->syllabus(SyllabusStatus::Published, topics: 1);
        $this->authorized_user(['read syllabus', 'update syllabus']);

        Livewire::test(SaveSyllabusToLibrary::class, ['syllabus' => $syllabus])
            ->call('save')
            ->assertForbidden();

        $this->assertSame(0, CurriculumOutline::query()->count());
    }

    public function test_a_draft_copies_topics_from_a_library_outline(): void
    {
        $draft = $this->syllabus(SyllabusStatus::Draft, topics: 1);
        $outline = $this->outline($draft->courseOffering->subject_id, topics: 3);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        Livewire::test(SyllabusTopicImporter::class, ['syllabus' => $draft])
            ->assertSee($outline->name)
            ->set('source', 'outline:'.$outline->id)
            ->call('copy')
            ->assertRedirect(route('syllabi.edit', $draft));

        $this->assertSame(4, $draft->topics()->count());
        $this->assertSame([1, 2, 3, 4], $draft->topics()->reorder()->orderBy('position')->pluck('position')->all());
        $this->assertTrue($draft->topics()->where('title', $outline->topics()->firstOrFail()->title)->exists());
    }

    public function test_a_draft_copies_topics_forward_from_last_terms_syllabus(): void
    {
        $earlier = $this->syllabus(SyllabusStatus::Published, topics: 2);
        $draft = $this->syllabus(SyllabusStatus::Draft, subject: $earlier->courseOffering->subject);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        Livewire::test(SyllabusTopicImporter::class, ['syllabus' => $draft])
            ->set('source', 'syllabus:'.$earlier->id)
            ->call('copy');

        $this->assertSame($earlier->topics()->pluck('title')->all(), $draft->topics()->pluck('title')->all());
        $this->assertSame(2, $earlier->topics()->count());
    }

    public function test_topics_are_not_copied_from_another_subject(): void
    {
        $draft = $this->syllabus(SyllabusStatus::Draft);
        $other = $this->outline(Subject::factory()->create(['school_id' => $this->workingSchool()->id])->id, topics: 2);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        Livewire::test(SyllabusTopicImporter::class, ['syllabus' => $draft])
            ->assertDontSee($other->name)
            ->set('source', 'outline:'.$other->id)
            ->call('copy')
            ->assertNoRedirect();

        $this->assertSame(0, $draft->topics()->count());
    }

    public function test_topics_are_not_copied_from_another_school(): void
    {
        $draft = $this->syllabus(SyllabusStatus::Draft);
        $foreign = $this->outline($draft->courseOffering->subject_id, topics: 1, school: School::query()->findOrFail(School::factory()->create()->getKey()));
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        Livewire::test(SyllabusTopicImporter::class, ['syllabus' => $draft])
            ->set('source', 'outline:'.$foreign->id)
            ->call('copy')
            ->assertNoRedirect();

        $this->assertSame(0, $draft->topics()->count());
    }

    public function test_a_published_syllabus_takes_no_copied_topics(): void
    {
        $published = $this->syllabus(SyllabusStatus::Published, topics: 1);
        $outline = $this->outline($published->courseOffering->subject_id, topics: 2);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        Livewire::test(SyllabusTopicImporter::class, ['syllabus' => $published])
            ->set('source', 'outline:'.$outline->id)
            ->call('copy')
            ->assertNoRedirect();

        $this->assertSame(1, $published->topics()->count());
    }

    public function test_the_library_page_lists_outlines_and_their_topics(): void
    {
        $outline = $this->outline(Subject::factory()->create(['school_id' => $this->workingSchool()->id])->id, topics: 2);
        $this->authorized_user(['read syllabus']);

        $this->get(route('syllabi.library'))->assertOk()->assertSee($outline->name);

        Livewire::test(CurriculumLibrary::class)
            ->assertDontSee('Remove<span', false)
            ->call('toggle', $outline->id)
            ->assertSee($outline->topics()->firstOrFail()->title);
    }

    public function test_a_reviewer_removes_an_outline(): void
    {
        $outline = $this->outline(Subject::factory()->create(['school_id' => $this->workingSchool()->id])->id, topics: 1);
        $this->authorized_user(self::REVIEWER_PERMISSIONS);

        Livewire::test(CurriculumLibrary::class)->call('delete', $outline->id);

        $this->assertModelMissing($outline);
        $this->assertSame(0, CurriculumOutlineTopic::query()->count());
    }

    public function test_staff_without_review_rights_cannot_remove_an_outline(): void
    {
        $outline = $this->outline(Subject::factory()->create(['school_id' => $this->workingSchool()->id])->id, topics: 1);
        $this->authorized_user(['read syllabus']);

        Livewire::test(CurriculumLibrary::class)->call('delete', $outline->id)->assertForbidden();

        $this->assertModelExists($outline);
    }

    private function outline(int $subjectId, int $topics, ?School $school = null): CurriculumOutline
    {
        $outline = CurriculumOutline::factory()->create([
            'school_id' => ($school ?? $this->workingSchool())->id,
            'subject_id' => $subjectId,
        ]);
        CurriculumOutlineTopic::factory()->count($topics)->sequence(fn ($sequence) => ['week' => $sequence->index + 1, 'position' => $sequence->index + 1])->create(['curriculum_outline_id' => $outline->id]);

        return $outline;
    }

    private function syllabus(SyllabusStatus $status, int $topics = 0, ?Subject $subject = null): Syllabus
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create(['school_id' => $school->id, 'academic_year_id' => $academicYear->getKey()]);
        $courseOffering = CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->getKey(),
            'academic_period_id' => $academicPeriod->getKey(),
            'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $school->id])->getKey(),
            'subject_id' => ($subject ?? Subject::factory()->create(['school_id' => $school->id]))->getKey(),
        ]);
        $syllabus = Syllabus::factory()->create([
            'course_offering_id' => $courseOffering->getKey(),
            'status' => $status,
            'published_at' => $status === SyllabusStatus::Published ? now() : null,
        ]);

        for ($week = 1; $week <= $topics; $week++) {
            SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => $week, 'position' => $week]);
        }

        return $syllabus->load('courseOffering');
    }
}
