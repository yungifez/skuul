<?php

namespace Tests\Feature;

use App\Actions\Boarding\AssignBoardingPlace;
use App\Actions\Boarding\StartBoardingRoll;
use App\Enums\BoardingRollEntryStatus;
use App\Enums\BoardingRollType;
use App\Enums\Feature;
use App\Livewire\BoardingRollBoard;
use App\Livewire\BoardingRollSheet;
use App\Models\BoardingRoll;
use App\Models\Dormitory;
use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Boarding houses can account for every resident during the day.
 */
class BoardingRollTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private const array STAFF = ['read boarding', 'manage boarding'];

    protected function setUp(): void
    {
        parent::setUp();

        features()->enable(Feature::Boarding);
    }

    public function test_staff_can_start_and_complete_a_house_roll(): void
    {
        $actor = $this->authorized_user(self::STAFF);
        [$student, $house] = $this->boarder();

        Livewire::test(BoardingRollBoard::class)
            ->call('start', $house->id, BoardingRollType::Evening->value)
            ->assertRedirect(route('boarding-rolls.show', BoardingRoll::sole()));

        $roll = BoardingRoll::sole();
        $entry = $roll->entries()->sole();

        $actor->get(route('boarding-rolls.index'))->assertOk()->assertSeeLivewire(BoardingRollBoard::class);
        $actor->get(route('boarding-rolls.show', $roll))
            ->assertOk()
            ->assertSeeLivewire(BoardingRollSheet::class)
            ->assertSee($student->user->name);

        Livewire::test(BoardingRollSheet::class, ['roll' => $roll])
            ->set("answers.{$entry->id}.status", BoardingRollEntryStatus::Present->value)
            ->call('complete')
            ->assertHasNoErrors()
            ->assertDispatched('status-message', type: 'success', message: 'The roll is complete.')
            ->assertSee('Completed')
            ->assertDontSee('Complete roll');

        $this->assertTrue($roll->fresh()->isComplete());
        $this->assertSame(BoardingRollEntryStatus::Present, $entry->fresh()->status);
    }

    public function test_a_roll_cannot_be_completed_with_an_unanswered_boarder(): void
    {
        $this->authorized_user(self::STAFF);
        [, $house] = $this->boarder();
        $roll = app(StartBoardingRoll::class)->start($house, BoardingRollType::Morning);

        Livewire::test(BoardingRollSheet::class, ['roll' => $roll])
            ->call('complete')
            ->assertHasErrors('roll')
            ->assertSee('Record every boarder before completing the roll.');

        $this->assertFalse($roll->fresh()->isComplete());
    }

    public function test_two_staff_taking_one_roll_keep_each_others_answers(): void
    {
        $this->authorized_user(self::STAFF);
        [$first, $house] = $this->boarder();
        $second = $this->boarderIn($house);
        $roll = app(StartBoardingRoll::class)->start($house, BoardingRollType::Curfew);
        $firstEntry = $roll->entries()->where('student_record_id', $first->id)->sole();
        $secondEntry = $roll->entries()->where('student_record_id', $second->id)->sole();

        $groundFloor = Livewire::test(BoardingRollSheet::class, ['roll' => $roll]);
        $upperFloor = Livewire::test(BoardingRollSheet::class, ['roll' => $roll]);

        $groundFloor->set("answers.{$firstEntry->id}.status", BoardingRollEntryStatus::Present->value)->call('save')->assertHasNoErrors();
        $upperFloor->set("answers.{$secondEntry->id}.status", BoardingRollEntryStatus::Late->value)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet("answers.{$firstEntry->id}.status", BoardingRollEntryStatus::Present->value);

        $this->assertSame(BoardingRollEntryStatus::Present, $firstEntry->fresh()->status);
        $this->assertSame(BoardingRollEntryStatus::Late, $secondEntry->fresh()->status);
    }

    public function test_an_answer_someone_else_saved_is_replaced_only_on_a_second_save(): void
    {
        $this->authorized_user(self::STAFF);
        [, $house] = $this->boarder();
        $roll = app(StartBoardingRoll::class)->start($house, BoardingRollType::Evening);
        $entry = $roll->entries()->sole();

        $mine = Livewire::test(BoardingRollSheet::class, ['roll' => $roll]);
        Livewire::test(BoardingRollSheet::class, ['roll' => $roll])
            ->set("answers.{$entry->id}.status", BoardingRollEntryStatus::Away->value)
            ->set("answers.{$entry->id}.location", 'Sick bay')
            ->call('save');

        $mine->set("answers.{$entry->id}.status", BoardingRollEntryStatus::Unaccounted->value)
            ->call('save')
            ->assertHasErrors("answers.{$entry->id}")
            ->assertSee('Someone else recorded &quot;Away, Sick bay&quot; since you opened the roll.', false);

        $this->assertSame(BoardingRollEntryStatus::Away, $entry->fresh()->status);

        $mine->call('save')->assertHasNoErrors();

        $this->assertSame(BoardingRollEntryStatus::Unaccounted, $entry->fresh()->status);
    }

    public function test_a_roll_completed_in_another_tab_takes_no_more_answers(): void
    {
        $this->authorized_user(self::STAFF);
        [, $house] = $this->boarder();
        $roll = app(StartBoardingRoll::class)->start($house, BoardingRollType::Evening);
        $entry = $roll->entries()->sole();

        $late = Livewire::test(BoardingRollSheet::class, ['roll' => $roll]);
        Livewire::test(BoardingRollSheet::class, ['roll' => $roll])
            ->set("answers.{$entry->id}.status", BoardingRollEntryStatus::Present->value)
            ->call('complete');

        $late->set("answers.{$entry->id}.status", BoardingRollEntryStatus::Unaccounted->value)
            ->call('save')
            ->assertHasErrors('roll')
            ->assertSee('Someone completed this roll while you worked on it. Nothing was saved.')
            ->assertDontSee('Complete roll');

        $this->assertSame(BoardingRollEntryStatus::Present, $entry->fresh()->status);
    }

    public function test_completing_with_a_boarder_unaccounted_for_says_so(): void
    {
        $this->authorized_user(self::STAFF);
        [, $house] = $this->boarder();
        $roll = app(StartBoardingRoll::class)->start($house, BoardingRollType::Curfew);
        $entry = $roll->entries()->sole();

        Livewire::test(BoardingRollSheet::class, ['roll' => $roll])
            ->set("answers.{$entry->id}.status", BoardingRollEntryStatus::Unaccounted->value)
            ->assertSee('Where (optional)')
            ->call('complete')
            ->assertDispatched('status-message', type: 'danger', message: 'The roll is complete with 1 boarder unaccounted for.');

        $this->assertTrue($roll->fresh()->isComplete());
        $this->assertSame(BoardingRollEntryStatus::Unaccounted, $entry->fresh()->status);
    }

    public function test_a_reader_sees_the_roll_but_cannot_answer_or_start_one(): void
    {
        $this->authorized_user(['read boarding']);
        [, $house] = $this->boarder();
        $roll = app(StartBoardingRoll::class)->start($house, BoardingRollType::Morning);

        Livewire::test(BoardingRollSheet::class, ['roll' => $roll])
            ->assertDontSee('Complete roll')
            ->call('save')
            ->assertForbidden();

        Livewire::test(BoardingRollBoard::class)
            ->assertSee('Not started')
            ->call('start', $house->id, BoardingRollType::Evening->value)
            ->assertForbidden();
    }

    public function test_the_board_starts_no_roll_for_a_future_day_or_a_closed_house(): void
    {
        $this->authorized_user(self::STAFF);
        [, $house] = $this->boarder();

        Livewire::test(BoardingRollBoard::class)
            ->set('date', today()->addDay()->toDateString())
            ->assertDontSee('Start the evening roll')
            ->call('start', $house->id, BoardingRollType::Evening->value)
            ->assertDispatched('status-message', type: 'danger', message: 'A roll can only be taken for today or a day before.')
            ->set('date', '20266-09-02')
            ->assertSee(today()->format('l, j F Y'));

        $house->update(['is_active' => false]);

        Livewire::test(BoardingRollBoard::class)
            ->call('start', $house->id, BoardingRollType::Evening->value)
            ->assertDispatched('status-message', type: 'danger', message: 'This house is no longer open. Reload the page.');

        $this->assertSame(0, BoardingRoll::query()->count());
    }

    public function test_starting_a_roll_twice_opens_the_one_already_started(): void
    {
        $this->authorized_user(self::STAFF);
        [, $house] = $this->boarder();
        $roll = app(StartBoardingRoll::class)->start($house, BoardingRollType::Evening);

        Livewire::test(BoardingRollBoard::class)
            ->call('start', $house->id, BoardingRollType::Evening->value)
            ->assertRedirect(route('boarding-rolls.show', $roll));

        $this->assertSame(1, BoardingRoll::query()->count());
    }

    public function test_a_student_can_see_the_latest_boarding_roll_in_their_portal(): void
    {
        $this->unauthorized_user();
        features()->enable(Feature::Portal, config: ['boarding' => true]);
        features()->enable(Feature::Boarding);
        [$student, $house] = $this->boarder();
        $roll = app(StartBoardingRoll::class)->start($house, BoardingRollType::Evening);
        $entry = $roll->entries()->sole();
        $entry->update(['status' => BoardingRollEntryStatus::Present, 'recorded_at' => now()]);

        $this->actingAs($student->user)
            ->get(route('portal.boarding.index', $student))
            ->assertOk()
            ->assertSee('Latest boarding check')
            ->assertSee('Present');
    }

    /**
     * @return array{0: StudentRecord, 1: Dormitory}
     */
    private function boarder(): array
    {
        $student = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $house = Dormitory::factory()->create(['school_id' => $student->school_id]);
        $room = DormitoryRoom::factory()->create(['school_id' => $student->school_id, 'dormitory_id' => $house->id]);
        $bed = DormitoryBed::factory()->create(['school_id' => $student->school_id, 'dormitory_room_id' => $room->id]);

        app(AssignBoardingPlace::class)->assign($student, $bed);

        return [$student, $house];
    }

    private function boarderIn(Dormitory $house): StudentRecord
    {
        $student = StudentRecord::factory()->create(['school_id' => $house->school_id]);
        $room = DormitoryRoom::factory()->create(['school_id' => $house->school_id, 'dormitory_id' => $house->id]);
        $bed = DormitoryBed::factory()->create(['school_id' => $house->school_id, 'dormitory_room_id' => $room->id]);

        app(AssignBoardingPlace::class)->assign($student, $bed);

        return $student;
    }
}
