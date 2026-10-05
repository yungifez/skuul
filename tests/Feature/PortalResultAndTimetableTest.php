<?php

namespace Tests\Feature;

use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Enums\ResultApprovalStatus;
use App\Enums\TimetableStatus;
use App\Models\CourseOffering;
use App\Models\CustomTimetableItem;
use App\Models\ParentRecord;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Models\Weekday;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Families read the approved results and the published week of their learner.
 */
class PortalResultAndTimetableTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_guardian_reads_only_the_approved_result_of_each_course(): void
    {
        $enrollment = $this->enrollment();
        $mathematics = $this->offering('Mathematics');
        $this->snapshot($enrollment, $mathematics, 1, 64.5, ResultApprovalStatus::Approved);
        $this->snapshot($enrollment, $mathematics, 2, 91, ResultApprovalStatus::Pending);
        $this->snapshot($enrollment, $this->offering('Chemistry'), 1, 48, ResultApprovalStatus::Rejected);
        $this->snapshot($this->enrollment(), $this->offering('History'), 1, 77, ResultApprovalStatus::Approved);

        $this->actingAsMemberOf($this->workingSchool(), $this->guardianOf($enrollment))
            ->get(route('portal.results.index', $enrollment))
            ->assertOk()
            ->assertSee('Mathematics')
            ->assertSee('64.5%')
            ->assertDontSee('91%')
            ->assertDontSee('Chemistry')
            ->assertDontSee('History');
    }

    public function test_a_learner_without_approved_results_is_told_so(): void
    {
        $enrollment = $this->enrollment();

        $this->actingAs($enrollment->user)
            ->get(route('portal.results.index', $enrollment))
            ->assertOk()
            ->assertSee('No approved results yet');
    }

    public function test_a_learner_reads_the_published_week_of_their_class(): void
    {
        $enrollment = $this->enrollment();
        $this->timetableWith($enrollment->academic_cycle_section_id, 'Morning assembly', TimetableStatus::Published, 'Term one week');
        $this->timetableWith($enrollment->academic_cycle_section_id, 'Draft swim lesson', TimetableStatus::Draft, 'Next term draft');
        $this->timetableWith(null, 'Another class lesson', TimetableStatus::Published, 'Another class week');

        $this->actingAs($enrollment->user)
            ->get(route('portal.timetable.show', $enrollment))
            ->assertOk()
            ->assertSee('Term one week')
            ->assertSee('Morning assembly')
            ->assertDontSee('Draft swim lesson')
            ->assertDontSee('Another class lesson');
    }

    public function test_a_class_without_a_published_timetable_is_told_so(): void
    {
        $enrollment = $this->enrollment();
        $this->timetableWith($enrollment->academic_cycle_section_id, 'Draft swim lesson', TimetableStatus::Draft, 'Next term draft');

        $this->actingAs($enrollment->user)
            ->get(route('portal.timetable.show', $enrollment))
            ->assertOk()
            ->assertSee('No timetable yet')
            ->assertDontSee('Draft swim lesson');
    }

    public function test_the_overview_links_to_results_and_timetable(): void
    {
        $enrollment = $this->enrollment();

        $this->actingAsMemberOf($this->workingSchool(), $enrollment->user)
            ->get(route('portal.overview'))
            ->assertOk()
            ->assertSee(route('portal.results.index', $enrollment))
            ->assertSee(route('portal.timetable.show', $enrollment));
    }

    public function test_a_person_cannot_read_an_unrelated_learner(): void
    {
        $enrollment = $this->enrollment();
        $stranger = $this->memberOf($this->workingSchool());

        $this->actingAs($stranger)->get(route('portal.results.index', $enrollment))->assertForbidden();
        $this->actingAs($stranger)->get(route('portal.timetable.show', $enrollment))->assertForbidden();
    }

    public function test_the_school_can_close_results_and_timetable(): void
    {
        $enrollment = $this->enrollment();
        features()->enable(Feature::Portal, $this->workingSchool()->id, config: [
            PortalArea::Results->value => false,
            PortalArea::Timetable->value => false,
        ]);

        $this->actingAs($enrollment->user)->get(route('portal.results.index', $enrollment))->assertNotFound();
        $this->actingAs($enrollment->user)->get(route('portal.timetable.show', $enrollment))->assertNotFound();
        $this->actingAsMemberOf($this->workingSchool(), $enrollment->user)
            ->get(route('portal.overview'))
            ->assertDontSee(route('portal.results.index', $enrollment))
            ->assertDontSee(route('portal.timetable.show', $enrollment));
    }

    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    private function guardianOf(StudentRecord $enrollment): User
    {
        $guardian = $this->memberOf($this->workingSchool());
        ParentRecord::create(['user_id' => $guardian->id])->students()->syncWithoutDetaching($enrollment->user);

        return $guardian->fresh();
    }

    private function offering(string $subjectName): CourseOffering
    {
        return CourseOffering::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'subject_id' => Subject::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => $subjectName])->id,
        ]);
    }

    private function snapshot(StudentRecord $enrollment, CourseOffering $offering, int $revision, float $percentage, ResultApprovalStatus $status): void
    {
        ResultSnapshot::create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'course_offering_id' => $offering->id,
            'revision' => $revision,
            'percentage' => $percentage,
            'payload' => ['percentage' => $percentage],
            'approval_status' => $status,
            'approved_at' => $status === ResultApprovalStatus::Approved ? now() : null,
            'published_at' => now(),
        ]);
    }

    /**
     * Put one item on a new timetable of a class.
     */
    private function timetableWith(?int $sectionId, string $itemName, TimetableStatus $status, string $timetableName): void
    {
        $timetable = Timetable::factory()->create(array_filter([
            'name' => $timetableName,
            'academic_cycle_section_id' => $sectionId,
        ]));
        $item = CustomTimetableItem::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => $itemName]);
        $slot = TimetableTimeSlot::create(['timetable_id' => $timetable->id, 'start_time' => '10:00', 'stop_time' => '10:30']);
        TimetableRecord::create([
            'timetable_time_slot_id' => $slot->id,
            'weekday_id' => Weekday::firstOrFail()->id,
            'timetable_time_slot_weekdayable_id' => $item->id,
            'timetable_time_slot_weekdayable_type' => $item->getMorphClass(),
        ]);
        Timetable::query()->whereKey($timetable->id)->update(['status' => $status->value, 'published_at' => $status === TimetableStatus::Published ? now() : null]);
    }
}
