<?php

namespace Tests\Feature;

use App\Actions\Facility\BookFacility;
use App\Actions\Timetable\PublishTimetable;
use App\Enums\AuditAction;
use App\Enums\FacilityKind;
use App\Exceptions\InvalidValueException;
use App\Exceptions\TimetableConflictException;
use App\Livewire\FacilityBoard;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\School;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\Weekday;
use App\Services\Timetable\FacilityAvailability;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The halls, laboratories, vehicles, and kit a campus shares.
 */
class FacilityTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_something_shared_can_be_booked(): void
    {
        $this->authorized_user([]);
        $hall = $this->facility();

        $booking = app(BookFacility::class)->book(
            $hall,
            now()->addDay()->setTime(9, 0),
            now()->addDay()->setTime(11, 0),
            'Speech day rehearsal',
        );

        $this->assertTrue($booking->isRunning());
        $this->assertSame($hall->id, $booking->facility_id);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::FacilityBooked)->first());
    }

    public function test_two_bookings_cannot_overlap(): void
    {
        $this->authorized_user([]);
        $hall = $this->facility();
        $book = app(BookFacility::class);
        $book->book($hall, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(11, 0), 'Rehearsal');

        $this->expectException(InvalidValueException::class);

        $book->book($hall, now()->addDay()->setTime(10, 0), now()->addDay()->setTime(12, 0), 'Assembly');
    }

    public function test_one_booking_can_start_when_another_ends(): void
    {
        $this->authorized_user([]);
        $hall = $this->facility();
        $book = app(BookFacility::class);
        $book->book($hall, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(11, 0), 'Rehearsal');

        $second = $book->book($hall, now()->addDay()->setTime(11, 0), now()->addDay()->setTime(12, 0), 'Assembly');

        $this->assertTrue($second->isRunning());
    }

    public function test_a_booking_must_end_after_it_starts(): void
    {
        $this->authorized_user([]);

        $this->expectException(InvalidValueException::class);

        app(BookFacility::class)->book(
            $this->facility(),
            now()->addDay()->setTime(11, 0),
            now()->addDay()->setTime(9, 0),
            'Backwards',
        );
    }

    public function test_something_out_of_use_cannot_be_booked(): void
    {
        $this->authorized_user([]);
        $hall = $this->facility();
        $hall->is_active = false;
        $hall->save();

        $this->expectException(InvalidValueException::class);

        app(BookFacility::class)->book($hall, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(10, 0), 'Assembly');
    }

    public function test_a_booking_given_up_frees_the_time(): void
    {
        $this->authorized_user([]);
        $hall = $this->facility();
        $book = app(BookFacility::class);
        $booking = $book->book($hall, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(11, 0), 'Rehearsal');

        $book->cancel($booking, 'The choir is away');

        $this->assertFalse($booking->fresh()->isRunning());
        $this->assertTrue(app(FacilityAvailability::class)->isFree(
            $hall,
            now()->addDay()->setTime(9, 0),
            now()->addDay()->setTime(11, 0),
        ));
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::FacilityBookingCancelled)->first());
    }

    public function test_a_booking_cannot_be_given_up_twice(): void
    {
        $this->authorized_user([]);
        $book = app(BookFacility::class);
        $booking = $book->book($this->facility(), now()->addDay()->setTime(9, 0), now()->addDay()->setTime(11, 0), 'Rehearsal');
        $book->cancel($booking, 'The choir is away');

        $this->expectException(InvalidValueException::class);

        $book->cancel($booking->fresh(), 'Again');
    }

    public function test_only_a_vehicle_or_a_room_is_offered_where_it_makes_sense(): void
    {
        $this->authorized_user([]);
        Facility::factory()->create(['school_id' => $this->workingSchool()->id, 'kind' => FacilityKind::Vehicle]);
        $hall = $this->facility();

        $lessonPlaces = Facility::inSchool()->holdsLessons()->pluck('id');

        $this->assertTrue($lessonPlaces->contains($hall->id));
        $this->assertSame(1, $lessonPlaces->count());
    }

    public function test_a_campus_cannot_book_another_campus_s_hall(): void
    {
        $this->authorized_user(['read facility', 'book facility', 'manage facility']);
        $elsewhere = Facility::factory()->create(['school_id' => School::factory()->create()->id, 'name' => 'Their hall']);
        $booking = FacilityBooking::factory()->create(['school_id' => $elsewhere->school_id, 'facility_id' => $elsewhere->id]);

        Livewire::test(FacilityBoard::class)
            ->assertDontSee('Their hall')
            ->call('startBooking')
            ->set('facilityId', (string) $elsewhere->id)
            ->set('startsAt', now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i'))
            ->set('endsAt', now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'))
            ->set('purpose', 'Assembly')
            ->call('book')
            ->assertHasErrors('facilityId');

        $this->assertThrows(fn () => Livewire::test(FacilityBoard::class)->call('retire', $elsewhere->id), ModelNotFoundException::class);
        $this->assertThrows(fn () => Livewire::test(FacilityBoard::class)->call('startGivingUp', $booking->id), ModelNotFoundException::class);

        $this->assertSame(1, FacilityBooking::count());
        $this->assertTrue($elsewhere->fresh()->is_active);
    }

    public function test_an_unauthorized_user_cannot_see_what_the_campus_shares(): void
    {
        $this->unauthorized_user()->get(route('facilities.index'))->assertForbidden();
    }

    public function test_staff_can_share_something_and_book_it_from_the_screen(): void
    {
        $actor = $this->authorized_user(['read facility', 'manage facility', 'book facility']);

        $actor->get(route('facilities.index'))->assertOk()->assertSeeLivewire(FacilityBoard::class);

        Livewire::test(FacilityBoard::class)
            ->assertSee('Nothing is shared yet.')
            ->call('startSharing')
            ->set('name', 'Main hall')
            ->set('capacity', '300')
            ->call('saveFacility')
            ->assertHasNoErrors()
            ->assertSee('Main hall');

        $hall = Facility::where('name', 'Main hall')->sole();
        $this->assertSame(FacilityKind::Hall, $hall->kind);

        Livewire::test(FacilityBoard::class)
            ->call('startBooking', $hall->id)
            ->assertSet('facilityId', (string) $hall->id)
            ->set('startsAt', now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i'))
            ->set('endsAt', now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'))
            ->set('purpose', 'Assembly')
            ->call('book')
            ->assertHasNoErrors()
            ->assertSet('isBooking', false)
            ->assertSee('Assembly');

        $this->assertSame(1, FacilityBooking::where('facility_id', $hall->id)->count());

        Livewire::test(FacilityBoard::class)
            ->call('startChanging', $hall->id)
            ->assertSet('name', 'Main hall')
            ->set('name', 'Great hall')
            ->call('saveFacility')
            ->assertHasNoErrors();

        $this->assertSame('Great hall', $hall->fresh()->name);
    }

    public function test_a_clash_or_a_time_gone_by_is_said_on_the_screen(): void
    {
        $this->authorized_user(['read facility', 'book facility']);
        $hall = $this->facility();
        app(BookFacility::class)->book($hall, now()->addDay()->setTime(9, 0), now()->addDays(2)->setTime(11, 0), 'Exams');

        Livewire::test(FacilityBoard::class)
            ->call('startBooking', $hall->id)
            ->set('startsAt', now()->addDays(2)->setTime(10, 0)->format('Y-m-d\TH:i'))
            ->set('endsAt', now()->addDays(2)->setTime(12, 0)->format('Y-m-d\TH:i'))
            ->set('purpose', 'Assembly')
            ->call('book')
            ->assertHasErrors('startsAt')
            ->assertSee(' to '.now()->addDays(2)->format('j M').', 11:00 for Exams.')
            ->set('startsAt', now()->subDays(2)->setTime(9, 0)->format('Y-m-d\TH:i'))
            ->set('endsAt', now()->subDays(2)->setTime(10, 0)->format('Y-m-d\TH:i'))
            ->call('book')
            ->assertHasErrors('startsAt')
            ->assertSee('That time has passed. Choose a time still ahead.');

        $this->assertSame(1, FacilityBooking::count());
    }

    public function test_reading_the_catalogue_does_not_allow_booking(): void
    {
        $this->authorized_user(['read facility']);
        $hall = $this->facility();

        Livewire::test(FacilityBoard::class)
            ->assertDontSee('Book something')
            ->set('facilityId', (string) $hall->id)
            ->set('startsAt', now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i'))
            ->set('endsAt', now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'))
            ->set('purpose', 'Assembly')
            ->call('book')
            ->assertForbidden();

        $this->assertSame(0, FacilityBooking::count());
    }

    public function test_only_the_person_who_booked_or_the_manager_gives_a_booking_up(): void
    {
        $this->authorized_user(['read facility', 'book facility']);
        $hall = $this->facility();
        $mine = app(BookFacility::class)->book($hall, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(10, 0), 'My lesson');
        $theirs = FacilityBooking::factory()->create([
            'school_id' => $hall->school_id,
            'facility_id' => $hall->id,
            'starts_at' => now()->addDay()->setTime(11, 0),
            'ends_at' => now()->addDay()->setTime(12, 0),
        ]);

        Livewire::test(FacilityBoard::class)
            ->assertSeeHtml('wire:click="startGivingUp('.$mine->id.')"')
            ->assertDontSeeHtml('wire:click="startGivingUp('.$theirs->id.')"')
            ->call('startGivingUp', $theirs->id)
            ->assertForbidden();

        Livewire::test(FacilityBoard::class)
            ->call('startGivingUp', $mine->id)
            ->set('giveUpReason', 'The class is on a trip')
            ->call('giveUp')
            ->assertDispatched('status-message', type: 'success');

        $this->assertFalse($mine->fresh()->isRunning());
        $this->assertSame('The class is on a trip', $mine->fresh()->cancelled_reason);
        $this->assertTrue($theirs->fresh()->isRunning());
    }

    public function test_a_stale_screen_cannot_give_up_a_booking_twice_or_one_that_is_over(): void
    {
        $this->authorized_user(['read facility', 'book facility', 'manage facility']);
        $hall = $this->facility();
        $booking = app(BookFacility::class)->book($hall, now()->addHour(), now()->addHours(2), 'Rehearsal');

        $stale = Livewire::test(FacilityBoard::class)->call('startGivingUp', $booking->id);
        Livewire::test(FacilityBoard::class)->call('startGivingUp', $booking->id)->set('giveUpReason', 'First')->call('giveUp');

        $stale->set('giveUpReason', 'Second')->call('giveUp')
            ->assertDispatched('status-message', type: 'danger', message: 'This booking was already given up.');

        $this->assertSame('First', $booking->fresh()->cancelled_reason);

        $later = app(BookFacility::class)->book($hall, now()->addHours(3), now()->addHours(4), 'Choir');
        $this->travel(5)->hours();

        $this->expectExceptionMessage('This booking is over, so it stays in the record.');
        app(BookFacility::class)->cancel($later, 'Too late');
    }

    public function test_taking_something_out_of_use_gives_up_what_is_ahead_and_keeps_the_past(): void
    {
        $this->authorized_user(['read facility', 'manage facility', 'book facility']);
        $hall = $this->facility();
        $past = FacilityBooking::factory()->create([
            'school_id' => $hall->school_id,
            'facility_id' => $hall->id,
            'starts_at' => now()->subDays(3)->setTime(9, 0),
            'ends_at' => now()->subDays(3)->setTime(10, 0),
        ]);
        $ahead = app(BookFacility::class)->book($hall, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(11, 0), 'Rehearsal');

        Livewire::test(FacilityBoard::class)
            ->call('retire', $hall->id)
            ->assertDispatched('status-message', type: 'success', message: "{$hall->name} is out of use. 1 booking ahead was given up.")
            ->assertSee('out of use')
            ->assertDontSeeHtml('wire:click="startBooking('.$hall->id.')"');

        $this->assertFalse($hall->fresh()->is_active);
        $this->assertTrue($past->fresh()->isRunning());
        $this->assertFalse($ahead->fresh()->isRunning());
        $this->assertSame("{$hall->name} was taken out of use.", $ahead->fresh()->cancelled_reason);

        Livewire::test(FacilityBoard::class)->call('restore', $hall->id);

        $this->assertTrue($hall->fresh()->is_active);
    }

    public function test_a_lesson_moved_into_a_hall_blocks_a_booking_of_it(): void
    {
        $this->authorized_user([]);
        $hall = $this->facility();
        $this->publishLessonIn($hall, 'monday', '09:00', '10:00');

        $monday = now()->next('monday');

        $this->expectException(InvalidValueException::class);

        app(BookFacility::class)->book(
            $hall,
            $monday->copy()->setTime(9, 30),
            $monday->copy()->setTime(10, 30),
            'Assembly',
        );
    }

    public function test_a_booking_on_another_day_is_left_alone(): void
    {
        $this->authorized_user([]);
        $hall = $this->facility();
        $this->publishLessonIn($hall, 'monday', '09:00', '10:00');

        $tuesday = now()->next('tuesday');

        $booking = app(BookFacility::class)->book(
            $hall,
            $tuesday->copy()->setTime(9, 30),
            $tuesday->copy()->setTime(10, 30),
            'Assembly',
        );

        $this->assertTrue($booking->isRunning());
    }

    public function test_two_sections_cannot_publish_lessons_into_the_same_hall(): void
    {
        $this->authorized_user([]);
        $hall = $this->facility();
        $this->publishLessonIn($hall, 'monday', '09:00', '10:00');

        $second = $this->timetableIn($hall, 'monday', '09:30', '10:30');

        $this->expectException(TimetableConflictException::class);

        app(PublishTimetable::class)->publish($second);
    }

    public function test_a_lesson_cannot_be_published_over_a_booking_of_the_hall(): void
    {
        $this->authorized_user([]);
        $hall = $this->facility();
        $monday = now()->next('monday');
        app(BookFacility::class)->book($hall, $monday->copy()->setTime(9, 30), $monday->copy()->setTime(11, 0), 'Sports day');
        $timetable = $this->timetableIn($hall, 'monday', '09:00', '10:00');

        try {
            app(PublishTimetable::class)->publish($timetable);
            $this->fail('A lesson was published over a booking of the hall.');
        } catch (TimetableConflictException $exception) {
            $this->assertStringContainsString("$hall->name is booked for Sports day on {$monday->format('j M Y')} from 09:30 to 11:00.", $exception->getMessage());
        }

        $this->assertFalse($timetable->fresh()->isPublished());
    }

    public function test_a_booking_given_up_or_on_another_day_leaves_the_lesson_alone(): void
    {
        $this->authorized_user([]);
        $hall = $this->facility();
        $monday = now()->next('monday');
        $booking = app(BookFacility::class)->book($hall, $monday->copy()->setTime(9, 30), $monday->copy()->setTime(11, 0), 'Sports day');
        app(BookFacility::class)->cancel($booking, 'Rain');
        app(BookFacility::class)->book($hall, $monday->copy()->addDay()->setTime(9, 0), $monday->copy()->addDay()->setTime(10, 0), 'Assembly');

        $this->publishLessonIn($hall, 'monday', '09:00', '10:00');

        $this->assertTrue(Timetable::query()->published()->exists());
    }

    /**
     * Publish one lesson that happens in the given place.
     */
    private function publishLessonIn(Facility $facility, string $weekday, string $start, string $stop): Timetable
    {
        return app(PublishTimetable::class)->publish($this->timetableIn($facility, $weekday, $start, $stop));
    }

    /**
     * Build a draft holding one lesson that happens in the given place.
     */
    private function timetableIn(Facility $facility, string $weekday, string $start, string $stop): Timetable
    {
        $academicYear = AcademicYear::query()->where('school_id', $this->workingSchool()->id)->firstOrFail();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id]);
        $cycleSection = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
        ]);

        $timetable = Timetable::create([
            'name' => 'Week plan '.fake()->unique()->word(),
            'academic_cycle_section_id' => $cycleSection->id,
            'academic_period_id' => current_academic_period_id(),
        ]);

        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);

        $slot = TimetableTimeSlot::create([
            'timetable_id' => $timetable->id,
            'start_time' => $start,
            'stop_time' => $stop,
        ]);

        TimetableRecord::create([
            'timetable_time_slot_id' => $slot->id,
            'weekday_id' => Weekday::where('name', ucfirst($weekday))->firstOrFail()->id,
            'facility_id' => $facility->id,
            'timetable_time_slot_weekdayable_id' => $subject->id,
            'timetable_time_slot_weekdayable_type' => $subject->getMorphClass(),
        ]);

        return $timetable->fresh();
    }

    public function test_the_screen_says_what_each_destructive_button_really_does(): void
    {
        $actor = $this->authorized_user(['read facility', 'manage facility', 'book facility']);
        $hall = $this->facility();
        app(BookFacility::class)->book($hall, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(11, 0), 'Rehearsal');

        $actor->get(route('facilities.index'))->assertOk()
            ->assertSee('wire:confirm="Take '.e($hall->name).' out of use? Nobody will be able to book it, and 1 booking(s) ahead will be given up."', false);
    }

    /**
     * Share one hall on this campus.
     */
    private function facility(): Facility
    {
        return Facility::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'kind' => FacilityKind::Hall,
        ]);
    }
}
