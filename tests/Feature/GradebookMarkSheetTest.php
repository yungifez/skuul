<?php

namespace Tests\Feature;

use App\Actions\Gradebook\ApproveResult;
use App\Actions\Gradebook\PublishResult;
use App\Actions\Gradebook\RecordGrade;
use App\Enums\AcademicPeriodStatus;
use App\Enums\GradeEntryState;
use App\Enums\GradeItemType;
use App\Enums\ResultApprovalStatus;
use App\Enums\TeachingRole;
use App\Exceptions\InvalidValueException;
use App\Livewire\GradebookMarkSheet;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\GradeEntry;
use App\Models\GradeItem;
use App\Models\GradingScale;
use App\Models\GradingScaleOption;
use App\Models\ResultSnapshot;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A teacher types the marks for one assessment down the class list, then
 * sends each result on. Two teachers of one class must not undo each other.
 */
class GradebookMarkSheetTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private const array TEACHER = ['read gradebook', 'manage gradebook', 'publish result', 'approve result', 'update subject'];

    private ?AcademicCycleSection $cycleSection = null;

    public function test_a_person_without_the_gradebook_cannot_open_the_sheet(): void
    {
        $courseOffering = $this->courseOffering();
        $this->unauthorized_user();

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $courseOffering])->assertForbidden();
    }

    public function test_another_schools_gradebook_is_refused(): void
    {
        $this->authorized_user(self::TEACHER);
        $foreign = $this->courseOffering(School::factory()->create());

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $foreign])->assertForbidden();
    }

    public function test_a_teacher_whose_assignment_ended_reads_the_marks_but_cannot_change_them(): void
    {
        $this->authorized_user(['read gradebook', 'manage gradebook', 'publish result']);
        $courseOffering = $this->courseOffering();
        $assignment = TeachingAssignment::create([
            'school_id' => $courseOffering->school_id,
            'subject_id' => $courseOffering->subject_id,
            'user_id' => auth()->id(),
            'academic_year_id' => $courseOffering->academic_year_id,
            'academic_period_id' => $courseOffering->academic_period_id,
            'course_offering_id' => $courseOffering->id,
            'role' => TeachingRole::Lead,
            'starts_on' => today()->subMonth(),
        ]);
        $this->assertTrue(Gate::allows('manageGradebook', $courseOffering));

        $assignment->update(['ends_on' => today()->subDay()]);

        $this->assertTrue(Gate::allows('viewGradebook', $courseOffering));
        $this->assertFalse(Gate::allows('manageGradebook', $courseOffering));
        $this->assertFalse(Gate::allows('publishResult', $courseOffering));
    }

    public function test_the_teacher_types_marks_down_the_list_and_saves_once(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        [$ada, $ben, $cy] = [$this->enrollment('Ada'), $this->enrollment('Ben'), $this->enrollment('Cy')];

        $this->get(route('course-offerings.gradebook.show', $item->courseOffering))
            ->assertOk()
            ->assertSeeLivewire(GradebookMarkSheet::class)
            ->assertDontSee('Gradebook workflow');

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $item->courseOffering])
            ->assertSee('0 of 3 marked')
            ->set("marks.$ada->id.points", '16')
            ->set("marks.$ben->id.state", GradeEntryState::Absent->value)
            ->assertSee('2 not saved')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('status-message', type: 'success', message: '2 marks saved.')
            ->assertSee('2 of 3 marked')
            ->assertDontSee('not saved');

        $this->assertSame(16.0, $this->entry($item, $ada)->points);
        $this->assertSame(GradeEntryState::Absent, $this->entry($item, $ben)->state);
        $this->assertNull($this->entry($item, $ben)->points);
        $this->assertNull($this->entry($item, $cy), 'An untouched row writes nothing.');
    }

    public function test_a_mark_above_the_maximum_is_refused_and_the_others_still_save(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        [$ada, $ben] = [$this->enrollment('Ada'), $this->enrollment('Ben')];

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $item->courseOffering])
            ->set("marks.$ada->id.points", '25')
            ->set("marks.$ben->id.points", '12')
            ->call('save')
            ->assertHasErrors("marks.$ada->id")
            ->assertSee('A mark cannot be more than 20.');

        $this->assertSame(0, GradeEntry::query()->count(), 'Validation stops the whole save before anything is written.');
    }

    public function test_a_graded_row_left_blank_is_refused_on_its_own_row(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        [$ada, $ben] = [$this->enrollment('Ada'), $this->enrollment('Ben')];
        app(RecordGrade::class)->record($item, $ada, points: 10);

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $item->courseOffering])
            ->set("marks.$ada->id.points", '')
            ->set("marks.$ben->id.points", '14')
            ->call('save')
            ->assertHasErrors("marks.$ada->id")
            ->assertDispatched('status-message', type: 'danger', message: '1 saved. Check the marks in red.');

        $this->assertSame(10.0, $this->entry($item, $ada)->points);
        $this->assertSame(14.0, $this->entry($item, $ben)->points);
    }

    public function test_a_mark_another_teacher_changed_needs_a_second_save(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        $ada = $this->enrollment('Ada');
        app(RecordGrade::class)->record($item, $ada, points: 10);

        $sheet = Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $item->courseOffering]);

        // The co-teacher corrects the same mark in their own tab.
        app(RecordGrade::class)->record($item, $ada, points: 12);

        $sheet->set("marks.$ada->id.points", '15')
            ->call('save')
            ->assertHasErrors("marks.$ada->id")
            ->assertSee('Someone else changed this to 12 since you opened the sheet. Save again to replace it.');

        $this->assertSame(12.0, $this->entry($item, $ada)->points);

        $sheet->call('save')->assertHasNoErrors();

        $this->assertSame(15.0, $this->entry($item, $ada)->points);
    }

    public function test_a_second_save_of_a_saved_mark_is_not_taken_for_someone_elses_change(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        $ada = $this->enrollment('Ada');

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $item->courseOffering])
            ->set("marks.$ada->id.points", '16.50')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet("marks.$ada->id.points", '16.5')
            ->set("marks.$ada->id.points", '17')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(17.0, $this->entry($item, $ada)->points);
    }

    public function test_another_assessment_does_not_open_over_unsaved_marks(): void
    {
        $this->authorized_user(self::TEACHER);
        $first = $this->item(['name' => 'Spelling', 'max_points' => 20]);
        $second = $this->item(['name' => 'Reading', 'max_points' => 10]);
        $ada = $this->enrollment('Ada');

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $first->courseOffering])
            ->assertSet('gradeItemId', (string) $first->id)
            ->set("marks.$ada->id.points", '16')
            ->set('gradeItemId', (string) $second->id)
            ->assertSet('gradeItemId', (string) $first->id)
            ->assertHasErrors('gradeItemId')
            ->assertSet("marks.$ada->id.points", '16')
            ->call('discard')
            ->set('gradeItemId', (string) $second->id)
            ->assertHasNoErrors()
            ->assertSet('gradeItemId', (string) $second->id);

        $this->assertSame(0, GradeEntry::query()->count());
    }

    public function test_a_learner_off_the_roster_cannot_be_marked(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        $this->enrollment('Ada');
        $elsewhere = StudentRecord::factory()->create(['school_id' => $item->school_id]);

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $item->courseOffering])
            ->set("marks.$elsewhere->id", ['state' => 'graded', 'points' => '20', 'option' => '', 'comment' => ''])
            ->call('save');

        $this->assertSame(0, GradeEntry::query()->count());
    }

    public function test_a_grade_from_another_scale_is_refused(): void
    {
        $this->authorized_user(self::TEACHER);
        $courseOffering = $this->courseOffering();
        $scale = GradingScale::factory()->create(['school_id' => $courseOffering->school_id]);
        $secure = GradingScaleOption::factory()->create(['grading_scale_id' => $scale->id, 'label' => 'Secure', 'points' => 3]);
        $outside = GradingScaleOption::factory()->create(['label' => 'Outside', 'points' => 5]);
        $item = $this->item(['type' => GradeItemType::Scale->value, 'grading_scale_id' => $scale->id, 'max_points' => 5]);
        $ada = $this->enrollment('Ada');

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $courseOffering])
            ->assertDontSee('Outside')
            ->set("marks.$ada->id.option", (string) $outside->id)
            ->call('save')
            ->assertHasErrors("marks.$ada->id")
            ->set("marks.$ada->id.option", (string) $secure->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame($secure->id, $this->entry($item, $ada)->grading_scale_option_id);
    }

    public function test_a_closed_period_shows_marks_without_boxes_and_refuses_a_save(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        $ada = $this->enrollment('Ada');
        app(RecordGrade::class)->record($item, $ada, points: 11);
        $item->courseOffering->academicPeriod()->update(['status' => AcademicPeriodStatus::Closed->value]);

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $item->courseOffering->fresh()])
            ->assertSee('11')
            ->assertDontSee('Save marks')
            ->assertDontSee('Send for approval')
            ->set("marks.$ada->id.points", '19')
            ->call('save')
            ->assertHasErrors('sheet')
            ->call('submitResult', $ada->id)
            ->assertDispatched('status-message', type: 'danger', message: 'This gradebook takes no results now.');

        $this->assertSame(11.0, $this->entry($item, $ada)->points);
        $this->assertSame(0, ResultSnapshot::query()->count());
    }

    public function test_a_result_goes_for_approval_once_and_is_approved(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        $ada = $this->enrollment('Ada');
        app(RecordGrade::class)->record($item, $ada, points: 16);

        $sheet = Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $item->courseOffering])
            ->assertSee('Not sent yet')
            ->call('submitResult', $ada->id)
            ->assertDispatched('status-message', type: 'success', message: 'Sent for approval.')
            ->assertSee('Revision 1 waiting for approval · 80.00%');

        $sheet->call('submitResult', $ada->id)
            ->assertDispatched('status-message', type: 'danger', message: 'Revision 1 with this result is already waiting for approval.');

        $this->assertSame(1, ResultSnapshot::query()->count());

        $sheet->call('approveResult', ResultSnapshot::sole()->id)
            ->assertSee('80.00%')
            ->assertSee('approved');

        $this->assertSame(ResultApprovalStatus::Approved, ResultSnapshot::sole()->approval_status);
    }

    public function test_sending_a_result_back_needs_a_reason(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        $ada = $this->enrollment('Ada');
        app(RecordGrade::class)->record($item, $ada, points: 16);
        $result = app(PublishResult::class)->publish($item->courseOffering, $ada);

        Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $item->courseOffering])
            ->call('startRejecting', $result->id)
            ->assertSee('Reason to send back the result for Ada')
            ->call('rejectResult')
            ->assertHasErrors(['rejectReason' => 'required'])
            ->set('rejectReason', 'Recount the second paper.')
            ->call('rejectResult')
            ->assertHasNoErrors()
            ->assertSet('rejectingResultId', null)
            ->assertSee('Revision 1 sent back: Recount the second paper.');

        $this->assertSame(ResultApprovalStatus::Rejected, $result->fresh()->approval_status);
    }

    public function test_an_older_revision_cannot_be_approved_over_a_newer_one(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        $ada = $this->enrollment('Ada');
        app(RecordGrade::class)->record($item, $ada, points: 16);
        $first = app(PublishResult::class)->publish($item->courseOffering, $ada);
        app(RecordGrade::class)->record($item, $ada, points: 18);
        app(PublishResult::class)->publish($item->courseOffering, $ada);

        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('The teacher sent revision 2 after this one. Approve that one instead.');

        app(ApproveResult::class)->approve($first, auth()->user());
    }

    public function test_a_result_of_another_offering_cannot_be_approved_here(): void
    {
        $this->authorized_user(self::TEACHER);
        $item = $this->item(['max_points' => 20]);
        $ada = $this->enrollment('Ada');
        $other = $this->courseOffering(fresh: true);
        $other->cycleSections()->attach($this->cycleSection);
        $otherItem = $this->item(['max_points' => 20], $other);
        app(RecordGrade::class)->record($otherItem, $ada, points: 16);
        $foreignResult = app(PublishResult::class)->publish($other, $ada);

        $sheet = Livewire::test(GradebookMarkSheet::class, ['courseOffering' => $item->courseOffering]);

        $this->assertThrows(fn () => $sheet->call('approveResult', $foreignResult->id), ModelNotFoundException::class);

        $this->assertSame(ResultApprovalStatus::Pending, $foreignResult->fresh()->approval_status);
    }

    private function entry(GradeItem $item, StudentRecord $enrollment): ?GradeEntry
    {
        return GradeEntry::query()->whereBelongsTo($item)->where('student_record_id', $enrollment->id)->first();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function item(array $attributes = [], ?CourseOffering $courseOffering = null): GradeItem
    {
        $courseOffering ??= $this->courseOffering();

        return GradeItem::create($attributes + [
            'school_id' => $courseOffering->school_id,
            'course_offering_id' => $courseOffering->id,
            'name' => 'Assessment',
            'type' => GradeItemType::Numeric->value,
        ]);
    }

    private function enrollment(string $name): StudentRecord
    {
        $courseOffering = $this->courseOffering();

        return StudentRecord::factory()->create([
            'school_id' => $courseOffering->school_id,
            'academic_cycle_section_id' => $this->cycleSection?->id,
            'user_id' => User::factory()->create(['name' => $name])->id,
        ]);
    }

    private ?CourseOffering $courseOffering = null;

    private function courseOffering(?School $school = null, bool $fresh = false): CourseOffering
    {
        if ($school === null && !$fresh && $this->courseOffering !== null) {
            return $this->courseOffering;
        }

        $school ??= $this->workingSchool();
        $isWorkingSchool = $school->is($this->workingSchool());
        $academicYear = ($isWorkingSchool ? current_academic_year() : null) ?? AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = ($isWorkingSchool ? current_academic_period() : null) ?? AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $cycleSection = AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
        ]);
        $courseOffering = CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => $academicLevel->id,
            'subject_id' => Subject::factory()->create(['school_id' => $school->id])->id,
        ]);
        $courseOffering->cycleSections()->attach($cycleSection);

        if ($isWorkingSchool && !$fresh) {
            $this->cycleSection = $cycleSection;
            $this->courseOffering = $courseOffering;
        }

        return $courseOffering;
    }
}
