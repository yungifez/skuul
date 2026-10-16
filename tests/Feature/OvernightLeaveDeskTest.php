<?php

namespace Tests\Feature;

use App\Actions\Boarding\AssignBoardingPlace;
use App\Actions\Boarding\DecideOvernightLeave;
use App\Actions\Boarding\RequestOvernightLeave;
use App\Enums\Feature;
use App\Enums\OvernightLeaveStatus;
use App\Livewire\OvernightLeaveDesk;
use App\Models\Dormitory;
use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use App\Models\OvernightLeave;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A night away is answered once, and a learner stays in sight until they are
 * recorded back in the house.
 */
class OvernightLeaveDeskTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private const array HOUSE = ['read boarding', 'manage boarding'];

    private const array WARDEN = ['read boarding', 'manage boarding', 'decide overnight leave'];

    protected function setUp(): void
    {
        parent::setUp();

        features()->enable(Feature::Boarding);
    }

    public function test_the_house_asks_for_a_boarder_and_never_for_a_day_learner(): void
    {
        $this->authorized_user(self::HOUSE);
        $boarder = $this->boarder();
        $dayLearner = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);

        Livewire::test(OvernightLeaveDesk::class)
            ->call('startAsking')
            ->assertSee($boarder->user->name)
            ->assertDontSee($dayLearner->user->name)
            ->set('learnerId', (string) $dayLearner->id)
            ->set('destination', 'Home')
            ->call('ask')
            ->assertHasErrors(['learnerId' => 'in'])
            ->set('learnerId', (string) $boarder->id)
            ->call('ask')
            ->assertHasNoErrors()
            ->assertSet('isAsking', false)
            ->assertSee('Waiting for a decision');

        $this->assertSame($boarder->id, OvernightLeave::sole()->student_record_id);
    }

    public function test_a_clash_with_another_night_away_shows_on_the_dates(): void
    {
        $this->authorized_user(self::HOUSE);
        $boarder = $this->boarder();
        $this->leave($boarder);

        Livewire::test(OvernightLeaveDesk::class)
            ->call('startAsking')
            ->set('learnerId', (string) $boarder->id)
            ->set('destination', 'An aunt')
            ->call('ask')
            ->assertHasErrors('leavesOn')
            ->assertSee('This learner already has leave covering one of those nights.');

        $this->assertSame(1, OvernightLeave::query()->count());
    }

    public function test_the_second_of_two_answers_meets_the_first(): void
    {
        $this->authorized_user(self::WARDEN);
        $leave = $this->leave($this->boarder());

        $late = Livewire::test(OvernightLeaveDesk::class);
        Livewire::test(OvernightLeaveDesk::class)->call('approve', $leave->id)->assertDispatched('status-message', type: 'success');

        $late->call('startRefusing', $leave->id)
            ->set('refuseNote', 'Examinations start')
            ->call('refuse')
            ->assertDispatched('status-message', type: 'danger', message: 'This request was answered already: Approved.');

        $this->assertSame(OvernightLeaveStatus::Approved, $leave->fresh()->status);
    }

    public function test_a_refusal_says_why(): void
    {
        $this->authorized_user(self::WARDEN);
        $leave = $this->leave($this->boarder());

        Livewire::test(OvernightLeaveDesk::class)
            ->call('startRefusing', $leave->id)
            ->call('refuse')
            ->assertHasErrors(['refuseNote' => 'required'])
            ->set('refuseNote', 'Examinations start')
            ->call('refuse')
            ->assertHasNoErrors()
            ->assertSee('Examinations start');

        $this->assertSame(OvernightLeaveStatus::Refused, $leave->fresh()->status);
        $this->assertSame('Examinations start', $leave->fresh()->decision_note);
    }

    public function test_a_learner_who_did_not_come_back_stays_in_sight(): void
    {
        $this->authorized_user(self::WARDEN);
        $boarder = $this->boarder();
        $leave = $this->leave($boarder);
        app(DecideOvernightLeave::class)->decide($leave, OvernightLeaveStatus::Approved);

        $this->travel(3)->days();

        Livewire::test(OvernightLeaveDesk::class)
            ->assertSee('Not back yet')
            ->assertSee($boarder->user->name)
            ->call('markReturned', $leave->id)
            ->assertDontSee('Not back yet');

        $this->assertSame(OvernightLeaveStatus::Returned, $leave->fresh()->status);
    }

    public function test_nights_that_have_passed_are_not_approved(): void
    {
        $this->authorized_user(self::WARDEN);
        $leave = $this->leave($this->boarder());

        $this->travel(3)->days();

        Livewire::test(OvernightLeaveDesk::class)
            ->assertSee('These nights have passed')
            ->assertDontSeeHtml('wire:click="approve('.$leave->id.')"')
            ->call('approve', $leave->id)
            ->assertDispatched('status-message', type: 'danger', message: 'The nights on this request have passed. Refuse it instead.');

        $this->assertSame(OvernightLeaveStatus::Requested, $leave->fresh()->status);
    }

    public function test_a_night_under_way_is_ended_by_coming_back_not_by_calling_it_off(): void
    {
        $this->authorized_user(self::WARDEN);
        $today = $this->leave($this->boarder());
        $later = $this->leave($this->boarder(), from: 5, to: 6);
        app(DecideOvernightLeave::class)->decide($today, OvernightLeaveStatus::Approved);
        app(DecideOvernightLeave::class)->decide($later, OvernightLeaveStatus::Approved);

        Livewire::test(OvernightLeaveDesk::class)
            ->assertSee('Coming up')
            ->call('cancel', $today->id)
            ->assertDispatched('status-message', type: 'danger', message: 'This learner has left already. Record them back in the house instead.')
            ->call('markReturned', $later->id)
            ->assertDispatched('status-message', type: 'danger', message: 'This learner has not left yet. Cancel the night away instead.')
            ->call('cancel', $later->id)
            ->assertDispatched('status-message', type: 'success', message: 'Cancelled.');

        $this->assertSame(OvernightLeaveStatus::Approved, $today->fresh()->status);
        $this->assertSame(OvernightLeaveStatus::Cancelled, $later->fresh()->status);
    }

    public function test_the_house_can_call_off_a_night_but_not_approve_one(): void
    {
        $this->authorized_user(self::HOUSE);
        $first = $this->leave($this->boarder());
        $second = $this->leave($this->boarder());

        Livewire::test(OvernightLeaveDesk::class)
            ->assertDontSeeHtml('wire:click="approve(')
            ->call('cancel', $first->id)
            ->assertDispatched('status-message', type: 'success', message: 'Cancelled.')
            ->call('approve', $second->id)
            ->assertForbidden();

        $this->assertSame(OvernightLeaveStatus::Cancelled, $first->fresh()->status);
        $this->assertSame(OvernightLeaveStatus::Requested, $second->fresh()->status);
    }

    public function test_a_reader_only_reads(): void
    {
        $this->authorized_user(['read boarding']);
        $leave = $this->leave($this->boarder());

        Livewire::test(OvernightLeaveDesk::class)
            ->assertDontSee('Ask for a night away')
            ->call('ask')
            ->assertForbidden();

        Livewire::test(OvernightLeaveDesk::class)->call('cancel', $leave->id)->assertForbidden();
    }

    private function leave(StudentRecord $boarder, int $from = 0, int $to = 1): OvernightLeave
    {
        return app(RequestOvernightLeave::class)->request(
            $boarder,
            today()->addDays($from)->toDateString(),
            today()->addDays($to)->toDateString(),
            'Home',
        );
    }

    private function boarder(): StudentRecord
    {
        $school = $this->workingSchool();
        $learner = StudentRecord::factory()->create(['school_id' => $school->id]);
        $house = Dormitory::factory()->create(['school_id' => $school->id]);
        $room = DormitoryRoom::factory()->create(['school_id' => $school->id, 'dormitory_id' => $house->id]);
        $bed = DormitoryBed::factory()->create(['school_id' => $school->id, 'dormitory_room_id' => $room->id]);

        app(AssignBoardingPlace::class)->assign($learner, $bed);

        return $learner;
    }
}
