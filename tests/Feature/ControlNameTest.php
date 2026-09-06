<?php

namespace Tests\Feature;

use App\Actions\Boarding\AssignBoardingPlace;
use App\Actions\Boarding\StartBoardingRoll;
use App\Enums\BoardingRollType;
use App\Enums\Feature;
use App\Enums\GradeEntryState;
use App\Enums\GradeItemType;
use App\Livewire\CreateFeeInvoiceForm;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Dormitory;
use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\GradeItem;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A form control in a table row must say what it is for. A column heading
 * names the column, not the control, so a screen reader announces a row of
 * boxes that all sound the same.
 */
class ControlNameTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_every_box_on_a_house_roll_names_its_boarder(): void
    {
        $actor = $this->authorized_user(['read boarding', 'manage boarding']);
        features()->enable(Feature::Boarding);

        $student = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $house = Dormitory::factory()->create(['school_id' => $student->school_id]);
        $room = DormitoryRoom::factory()->create(['school_id' => $student->school_id, 'dormitory_id' => $house->id]);
        $bed = DormitoryBed::factory()->create(['school_id' => $student->school_id, 'dormitory_room_id' => $room->id]);
        app(AssignBoardingPlace::class)->assign($student, $bed);

        $roll = app(StartBoardingRoll::class)->start($house, BoardingRollType::Morning);
        $boarder = $student->user->name;

        $html = (string) $actor->get(route('boarding-rolls.show', $roll))->assertOk()->getContent();

        foreach (['Status', 'Location', 'Note'] as $column) {
            $this->assertStringContainsString(
                'aria-label="'.$column.' for '.e($boarder).'"',
                $html,
                "The {$column} box must name the boarder it belongs to."
            );
        }

        $this->assertEveryControlInATableIsNamed($html);
    }

    public function test_the_reject_box_on_the_gradebook_names_its_student(): void
    {
        $this->authorized_user(['read gradebook', 'manage gradebook', 'publish result', 'approve result', 'update subject']);
        [$courseOffering, $enrollment] = $this->offeringAndEnrollment();

        $this->post(route('course-offerings.gradebook.items.store', $courseOffering), [
            'name' => 'Spelling test',
            'type' => GradeItemType::Numeric->value,
            'max_points' => 20,
            'weight' => 1,
        ])->assertSessionHas('success');

        $this->post(route('course-offerings.gradebook.entries.store', $courseOffering), [
            'grade_item_id' => GradeItem::query()->whereBelongsTo($courseOffering)->sole()->id,
            'student_record_id' => $enrollment->id,
            'state' => GradeEntryState::Graded->value,
            'points' => 16,
        ])->assertSessionHas('success');

        // Only a result that waits for a decision draws the reject box.
        $this->post(route('course-offerings.gradebook.results.publish', $courseOffering), [
            'student_record_id' => $enrollment->id,
        ])->assertSessionHas('success');

        $html = (string) $this->get(route('course-offerings.gradebook.show', $courseOffering))->assertOk()->getContent();
        $student = $enrollment->user->name;

        $this->assertStringContainsString(
            'aria-label="Reason to reject the result for '.e($student).'"',
            $html,
            'The reject box must name the student whose result it refuses.'
        );

        $this->assertEveryControlInATableIsNamed($html);
    }

    public function test_every_box_on_a_fee_invoice_row_names_its_fee(): void
    {
        $this->authorized_user(['read fee invoice', 'create fee invoice']);
        $category = FeeCategory::factory()->create(['school_id' => $this->workingSchool()->id]);
        $fee = Fee::factory()->create([
            'fee_category_id' => $category->id,
            'name' => 'Bus pass',
        ]);

        $html = Livewire::test(CreateFeeInvoiceForm::class)
            ->call('addFee', $category->id, $fee->id)
            ->html();

        foreach (['Amount', 'Waiver', 'Fine'] as $column) {
            $this->assertStringContainsString(
                'aria-label="'.$column.' for Bus pass"',
                $html,
                "The {$column} box must name the fee it belongs to."
            );
        }

        $this->assertEveryControlInATableIsNamed($html);
    }

    /**
     * Fail when a table cell holds a control that nothing names.
     *
     * A control is named by aria-label, by aria-labelledby, or by a label
     * that points at its id.
     */
    private function assertEveryControlInATableIsNamed(string $html): void
    {
        preg_match_all('/<(input|select|textarea)\b([^>]*)>/i', $html, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as $index => [, $offset]) {
            $attributes = $matches[2][$index][0];

            if (preg_match('/type="(hidden|submit|button)"/i', $attributes) === 1) {
                continue;
            }

            if (!$this->sitsInAnOpenTableCell($html, $offset) || $this->sitsInAnOpenLabel($html, $offset)) {
                continue;
            }

            if (preg_match('/\baria-label(ledby)?="/i', $attributes) === 1) {
                continue;
            }

            $named = preg_match('/\bid="([^"]+)"/i', $attributes, $id) === 1
                && str_contains($html, 'for="'.$id[1].'"');

            $this->assertTrue($named, 'A control in a table cell carries no name: '.$matches[0][$index][0]);
        }
    }

    private function sitsInAnOpenTableCell(string $html, int $offset): bool
    {
        return $this->lastOpenTagWins($html, $offset, '<t[dh]\b', '<\/t[dh]>');
    }

    private function sitsInAnOpenLabel(string $html, int $offset): bool
    {
        return $this->lastOpenTagWins($html, $offset, '<label\b', '<\/label>');
    }

    private function lastOpenTagWins(string $html, int $offset, string $open, string $close): bool
    {
        $before = substr($html, 0, $offset);

        $opened = preg_match_all('/'.$open.'/i', $before, $ignored, PREG_OFFSET_CAPTURE, 0) > 0
            ? $ignored[0][array_key_last($ignored[0])][1]
            : -1;
        $closed = preg_match_all('/'.$close.'/i', $before, $ended, PREG_OFFSET_CAPTURE, 0) > 0
            ? $ended[0][array_key_last($ended[0])][1]
            : -1;

        return $opened > $closed;
    }

    /**
     * @return array{0: CourseOffering, 1: StudentRecord}
     */
    private function offeringAndEnrollment(): array
    {
        $school = $this->workingSchool();
        $academicYear = current_academic_year() ?? AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = current_academic_period() ?? AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $cycleSection = AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
        ]);
        $subject = Subject::factory()->create(['school_id' => $school->id]);
        $courseOffering = CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => $academicLevel->id,
            'subject_id' => $subject->id,
        ]);
        $courseOffering->cycleSections()->attach($cycleSection);

        $enrollment = StudentRecord::query()->create([
            'school_id' => $school->id,
            'academic_cycle_section_id' => $cycleSection->id,
            'user_id' => User::query()->create(User::factory()->raw())->id,
            'admission_date' => now(),
        ]);

        return [$courseOffering, $enrollment];
    }
}
