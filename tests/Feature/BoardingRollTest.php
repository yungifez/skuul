<?php

namespace Tests\Feature;

use App\Actions\Boarding\AssignBoardingPlace;
use App\Actions\Boarding\StartBoardingRoll;
use App\Enums\BoardingRollEntryStatus;
use App\Enums\BoardingRollType;
use App\Enums\Feature;
use App\Models\BoardingRoll;
use App\Models\BoardingRollEntry;
use App\Models\Dormitory;
use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Boarding houses can account for every resident during the day.
 */
class BoardingRollTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_staff_can_start_and_complete_a_house_roll(): void
    {
        $actor = $this->authorized_user(['read boarding', 'manage boarding']);
        features()->enable(Feature::Boarding);
        [$student, $house] = $this->boarder();

        $actor->post(route('boarding-rolls.store'), [
            'dormitory_id' => $house->id,
            'type' => BoardingRollType::Evening->value,
            'taken_on' => now()->toDateString(),
        ])->assertRedirect();

        $roll = BoardingRoll::sole();
        $entry = $roll->entries()->sole();

        $actor->get(route('boarding-rolls.index'))
            ->assertOk()
            ->assertSee('House checks');
        $actor->get(route('boarding-rolls.show', $roll))
            ->assertOk()
            ->assertSee('Boarder check')
            ->assertSee($student->user->name);

        $actor->put(route('boarding-rolls.update', $roll), [
            'complete' => true,
            'entries' => [
                $entry->id => [
                    'id' => $entry->id,
                    'status' => BoardingRollEntryStatus::Present->value,
                ],
            ],
        ])->assertRedirect();

        $this->assertTrue($roll->fresh()->isComplete());
        $this->assertSame(BoardingRollEntryStatus::Present, $entry->fresh()->status);
    }

    public function test_a_roll_cannot_be_completed_with_an_unanswered_boarder(): void
    {
        $actor = $this->authorized_user(['read boarding', 'manage boarding']);
        features()->enable(Feature::Boarding);
        [, $house] = $this->boarder();
        $roll = app(StartBoardingRoll::class)->start($house, BoardingRollType::Morning);

        $actor->put(route('boarding-rolls.update', $roll), [
            'complete' => true,
            'entries' => $roll->entries->mapWithKeys(fn (BoardingRollEntry $entry): array => [$entry->id => [
                'id' => $entry->id,
                'status' => BoardingRollEntryStatus::NotRecorded->value,
            ]])->all(),
        ])->assertSessionHas('danger', 'Record every boarder before completing the roll.');

        $this->assertFalse($roll->fresh()->isComplete());
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
}
