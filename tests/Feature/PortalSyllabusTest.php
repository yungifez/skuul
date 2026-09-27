<?php

namespace Tests\Feature;

use App\Enums\CourseOfferingStatus;
use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Enums\RosterMode;
use App\Enums\SyllabusStatus;
use App\Enums\TopicCoverageStatus;
use App\Models\CourseOffering;
use App\Models\ParentRecord;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\SyllabusTopicCoverage;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Learners and guardians read the published plan of each course the learner takes.
 */
class PortalSyllabusTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_guardian_reads_the_published_syllabi_of_their_childs_courses(): void
    {
        $enrollment = $this->enrollment();
        $taken = $this->syllabus($enrollment, 'Mathematics', SyllabusStatus::Published);
        $topic = $taken->topics()->firstOrFail();
        SyllabusTopicCoverage::factory()->create(['syllabus_topic_id' => $topic->id, 'status' => TopicCoverageStatus::Covered]);
        $this->syllabus($enrollment, 'Chemistry draft', SyllabusStatus::Draft);
        $this->syllabus(null, 'Not their course', SyllabusStatus::Published);

        $this->actingAsMemberOf($this->workingSchool(), $this->guardianOf($enrollment))
            ->get(route('portal.syllabi.index', $enrollment))
            ->assertOk()
            ->assertSee('Mathematics')
            ->assertSee($topic->title)
            ->assertSee('1 of 1')
            ->assertSee('Covered')
            ->assertDontSee('Chemistry draft')
            ->assertDontSee('Not their course');
    }

    public function test_the_overview_links_to_syllabi(): void
    {
        $enrollment = $this->enrollment();

        $this->actingAsMemberOf($this->workingSchool(), $enrollment->user)
            ->get(route('portal.overview'))
            ->assertOk()
            ->assertSee(route('portal.syllabi.index', $enrollment));
    }

    public function test_a_person_cannot_read_an_unrelated_enrollment(): void
    {
        $enrollment = $this->enrollment();

        $this->actingAs($this->memberOf($this->workingSchool()))
            ->get(route('portal.syllabi.index', $enrollment))
            ->assertForbidden();
    }

    public function test_the_school_can_close_the_syllabi_area(): void
    {
        $school = $this->workingSchool();
        $enrollment = $this->enrollment();
        features()->enable(Feature::Portal, $school->id, config: [PortalArea::Syllabi->value => false]);

        $this->actingAs($enrollment->user)
            ->get(route('portal.syllabi.index', $enrollment))
            ->assertNotFound();
    }

    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    private function syllabus(?StudentRecord $enrollment, string $subjectName, SyllabusStatus $status): Syllabus
    {
        $school = $this->workingSchool();
        $courseOffering = CourseOffering::factory()->create([
            'school_id' => $school->id,
            'subject_id' => Subject::factory()->create(['school_id' => $school->id, 'name' => $subjectName])->getKey(),
            'roster_mode' => RosterMode::IndividualRoster,
            'status' => CourseOfferingStatus::Active,
        ]);
        $offering = CourseOffering::query()->findOrFail($courseOffering->getKey());

        if ($enrollment !== null) {
            $offering->studentRecords()->attach($enrollment);
        }

        $syllabus = Syllabus::factory()->create(['course_offering_id' => $offering->id, 'status' => $status, 'name' => $subjectName.' plan']);
        SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => 1]);

        return $syllabus;
    }

    private function guardianOf(StudentRecord $enrollment): User
    {
        $guardian = $this->memberOf($this->workingSchool());
        ParentRecord::create(['user_id' => $guardian->id])->students()->syncWithoutDetaching($enrollment->user);

        return $guardian->fresh();
    }
}
