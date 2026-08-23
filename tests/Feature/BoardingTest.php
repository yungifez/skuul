<?php

namespace Tests\Feature;

use App\Actions\Boarding\AssignBoardingPlace;
use App\Actions\Boarding\AssignBoardingSupervisor;
use App\Actions\Boarding\DecideOvernightLeave;
use App\Actions\Boarding\RequestOvernightLeave;
use App\Enums\AuditAction;
use App\Enums\Feature;
use App\Enums\OvernightLeaveStatus;
use App\Enums\SupervisionRole;
use App\Exceptions\InvalidValueException;
use App\Models\AuditEvent;
use App\Models\BoardingPlace;
use App\Models\Dormitory;
use App\Models\DormitoryBed;
use App\Models\DormitoryRoom;
use App\Models\OvernightLeave;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Boarding\BoardingRoster;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Where a boarder sleeps, who is on duty, and who is out for the night.
 */
class BoardingTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_boarding_is_off_until_a_school_turns_it_on(): void
    {
        $this->assertFalse(app(FeatureManager::class)->enabled(Feature::Boarding));
    }

    public function test_a_learner_is_given_a_bed(): void
    {
        $this->authorized_user([]);
        $bed = $this->bed();
        $enrollment = $this->enrollment();

        $place = app(AssignBoardingPlace::class)->assign($enrollment, $bed);

        $this->assertSame($bed->id, $place->dormitory_bed_id);
        $this->assertTrue($bed->fresh()->isTaken());
        $this->assertSame($enrollment->id, $bed->occupant()?->id);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::BoardingPlaceChanged)->first());
    }

    public function test_two_learners_cannot_share_one_bed(): void
    {
        $this->authorized_user([]);
        $bed = $this->bed();
        app(AssignBoardingPlace::class)->assign($this->enrollment(), $bed);

        $this->expectException(InvalidValueException::class);

        app(AssignBoardingPlace::class)->assign($this->enrollment(), $bed);
    }

    public function test_giving_the_same_bed_again_writes_nothing_new(): void
    {
        $this->authorized_user([]);
        $bed = $this->bed();
        $enrollment = $this->enrollment();
        $assign = app(AssignBoardingPlace::class);

        $assign->assign($enrollment, $bed);
        $assign->assign($enrollment, $bed);

        $this->assertSame(1, BoardingPlace::where('student_record_id', $enrollment->id)->count());
    }

    public function test_moving_a_learner_keeps_the_old_place(): void
    {
        $this->authorized_user([]);
        $first = $this->bed();
        $second = DormitoryBed::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'dormitory_room_id' => $first->dormitory_room_id,
        ]);
        $enrollment = $this->enrollment();
        $assign = app(AssignBoardingPlace::class);

        $assign->assign($enrollment, $first);
        $assign->assign($enrollment, $second, reason: 'Moved to be near a friend');

        $this->assertSame(2, BoardingPlace::where('student_record_id', $enrollment->id)->count());
        $this->assertSame($second->id, BoardingPlace::currentFor($enrollment)?->dormitory_bed_id);
        $this->assertFalse($first->fresh()->isTaken());
    }

    public function test_boarding_history_cannot_be_changed_or_deleted(): void
    {
        $this->authorized_user([]);
        $place = app(AssignBoardingPlace::class)->assign($this->enrollment(), $this->bed());

        $this->expectException(RuntimeException::class);

        $place->delete();
    }

    public function test_a_bed_on_another_campus_is_refused(): void
    {
        $this->authorized_user([]);
        $elsewhere = School::factory()->create();
        $room = DormitoryRoom::factory()->create([
            'school_id' => $elsewhere->id,
            'dormitory_id' => Dormitory::factory()->create(['school_id' => $elsewhere->id])->id,
        ]);
        $bed = DormitoryBed::factory()->create(['school_id' => $elsewhere->id, 'dormitory_room_id' => $room->id]);

        $this->expectException(InvalidValueException::class);

        app(AssignBoardingPlace::class)->assign($this->enrollment(), $bed);
    }

    public function test_a_learner_who_leaves_the_house_frees_the_bed(): void
    {
        $this->authorized_user([]);
        $bed = $this->bed();
        $enrollment = $this->enrollment();
        $assign = app(AssignBoardingPlace::class);
        $assign->assign($enrollment, $bed);

        $assign->end($enrollment, 'The family became day parents');

        $this->assertFalse($bed->fresh()->isTaken());
        $this->assertFalse(BoardingPlace::currentFor($enrollment)?->isBoarding());
    }

    public function test_a_house_counts_its_beds_and_the_ones_that_are_taken(): void
    {
        $this->authorized_user([]);
        $bed = $this->bed();
        $dormitory = $bed->room->dormitory;
        DormitoryBed::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'dormitory_room_id' => $bed->dormitory_room_id,
        ]);
        app(AssignBoardingPlace::class)->assign($this->enrollment(), $bed);

        $counts = app(BoardingRoster::class)->occupancyOf($dormitory);

        $this->assertSame(2, $counts['beds']);
        $this->assertSame(1, $counts['taken']);
        $this->assertSame(1, $counts['free']);
    }

    public function test_a_learner_who_does_not_board_cannot_ask_for_a_night_away(): void
    {
        $this->authorized_user([]);

        $this->expectException(InvalidValueException::class);

        app(RequestOvernightLeave::class)->request(
            $this->enrollment(),
            now()->toDateString(),
            now()->addDay()->toDateString(),
            'Home',
        );
    }

    public function test_two_nights_away_cannot_cover_the_same_night(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->boarder();
        $ask = app(RequestOvernightLeave::class);
        $ask->request($enrollment, now()->toDateString(), now()->addDays(2)->toDateString(), 'Home');

        $this->expectException(InvalidValueException::class);

        $ask->request($enrollment, now()->addDay()->toDateString(), now()->addDays(3)->toDateString(), 'An aunt');
    }

    public function test_a_refused_night_away_cannot_be_approved_later(): void
    {
        $this->authorized_user([]);
        $leave = app(RequestOvernightLeave::class)->request(
            $this->boarder(),
            now()->toDateString(),
            now()->addDay()->toDateString(),
            'Home',
        );
        app(DecideOvernightLeave::class)->decide($leave, OvernightLeaveStatus::Refused, 'Examinations start');

        $this->expectException(InvalidValueException::class);

        app(DecideOvernightLeave::class)->decide($leave->fresh(), OvernightLeaveStatus::Approved);
    }

    public function test_the_house_can_say_who_is_out_tonight(): void
    {
        $this->authorized_user([]);
        $bed = $this->bed();
        $enrollment = $this->enrollment();
        app(AssignBoardingPlace::class)->assign($enrollment, $bed);
        $leave = app(RequestOvernightLeave::class)->request(
            $enrollment,
            now()->toDateString(),
            now()->addDay()->toDateString(),
            'Home with a guardian',
        );
        app(DecideOvernightLeave::class)->decide($leave, OvernightLeaveStatus::Approved);

        $away = app(BoardingRoster::class)->awayFrom($bed->room->dormitory);

        $this->assertCount(1, $away);
        $this->assertSame($enrollment->id, $away->first()->student_record_id);
    }

    public function test_a_learner_coming_back_leaves_the_tonight_list(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->boarder();
        $leave = app(RequestOvernightLeave::class)->request(
            $enrollment,
            now()->toDateString(),
            now()->addDay()->toDateString(),
            'Home',
        );
        app(DecideOvernightLeave::class)->decide($leave, OvernightLeaveStatus::Approved);
        app(DecideOvernightLeave::class)->decide($leave->fresh(), OvernightLeaveStatus::Returned);

        $this->assertTrue(OvernightLeave::awayOn()->get()->isEmpty());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::OvernightLeaveReturned)->first());
    }

    public function test_a_learner_cannot_supervise_a_house(): void
    {
        $this->authorized_user([]);
        $dormitory = Dormitory::factory()->create(['school_id' => $this->workingSchool()->id]);
        $learner = $this->enrollment()->user;
        $this->memberOf($this->workingSchool(), $learner);

        $this->expectException(InvalidValueException::class);

        app(AssignBoardingSupervisor::class)->assign($dormitory, $learner->fresh(), SupervisionRole::Warden);
    }

    public function test_a_duty_that_ended_still_says_who_was_on_it(): void
    {
        $this->authorized_user([]);
        $dormitory = Dormitory::factory()->create(['school_id' => $this->workingSchool()->id]);
        $staff = $this->memberOf($this->workingSchool());
        $supervise = app(AssignBoardingSupervisor::class);
        $duty = $supervise->assign($dormitory, $staff, SupervisionRole::Warden, now()->subMonth());

        $supervise->end($duty, now()->subWeek());

        $this->assertSame(0, $dormitory->supervisions()->onDuty()->count());
        $this->assertSame(1, $dormitory->supervisions()->onDuty(now()->subDays(20)->toDateString())->count());
    }

    public function test_the_screens_are_hidden_until_the_school_turns_boarding_on(): void
    {
        $actor = $this->authorized_user(['read boarding']);

        $actor->get(route('dormitories.index'))->assertNotFound();

        app(FeatureManager::class)->enable(Feature::Boarding);

        $actor->get(route('dormitories.index'))->assertOk()->assertSee('Houses');
    }

    public function test_the_office_can_open_a_house_and_fill_a_bed(): void
    {
        $actor = $this->authorized_user(['read boarding', 'manage boarding']);
        app(FeatureManager::class)->enable(Feature::Boarding);
        $enrollment = $this->enrollment();

        $actor->post(route('dormitories.store'), [
            'name' => 'Mandela House',
            'label' => 'House',
            'rooms' => 2,
            'beds_per_room' => 3,
        ])->assertRedirect();

        $dormitory = Dormitory::where('name', 'Mandela House')->sole();
        $this->assertSame(6, $dormitory->beds()->count());

        $bed = $dormitory->beds()->first();

        $actor->post(route('boarding-places.store'), [
            'student_record_id' => $enrollment->id,
            'dormitory_bed_id' => $bed->id,
        ])->assertRedirect();

        $this->assertTrue($bed->fresh()->isTaken());

        $actor->get(route('dormitories.show', $dormitory->id))
            ->assertOk()
            ->assertSee($enrollment->user->name);
    }

    public function test_reading_boarding_does_not_allow_answering_a_night_away(): void
    {
        $actor = $this->authorized_user(['read boarding', 'manage boarding']);
        app(FeatureManager::class)->enable(Feature::Boarding);
        $leave = app(RequestOvernightLeave::class)->request(
            $this->boarder(),
            now()->toDateString(),
            now()->addDay()->toDateString(),
            'Home',
        );

        $actor->put(route('overnight-leaves.update', $leave->id), ['status' => 'approved'])->assertForbidden();

        $this->assertSame(OvernightLeaveStatus::Requested, $leave->fresh()->status);
    }

    /**
     * Create a bed in the working school.
     */
    private function bed(): DormitoryBed
    {
        $dormitory = Dormitory::factory()->create(['school_id' => $this->workingSchool()->id]);
        $room = DormitoryRoom::factory()->create([
            'school_id' => $dormitory->school_id,
            'dormitory_id' => $dormitory->id,
        ]);

        return DormitoryBed::factory()->create([
            'school_id' => $dormitory->school_id,
            'dormitory_room_id' => $room->id,
        ]);
    }

    /**
     * Create an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        /** @var User $user */
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    /**
     * Create an enrollment that already has a bed.
     */
    private function boarder(): StudentRecord
    {
        $enrollment = $this->enrollment();
        app(AssignBoardingPlace::class)->assign($enrollment, $this->bed());

        return $enrollment;
    }
}
