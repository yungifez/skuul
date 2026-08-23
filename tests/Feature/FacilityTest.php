<?php

namespace Tests\Feature;

use App\Actions\Facility\BookFacility;
use App\Actions\Timetable\PublishTimetable;
use App\Enums\AuditAction;
use App\Enums\FacilityKind;
use App\Exceptions\InvalidValueException;
use App\Exceptions\TimetableConflictException;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $actor = $this->authorized_user(['read facility', 'book facility']);
        $elsewhere = Facility::factory()->create(['school_id' => School::factory()->create()->id]);

        $actor->post(route('facilities.book'), [
            'facility_id' => $elsewhere->id,
            'starts_at' => now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i'),
            'ends_at' => now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'),
            'purpose' => 'Assembly',
        ])->assertSessionHasErrors('facility_id');

        $this->assertSame(0, FacilityBooking::count());
    }

    public function test_an_unauthorized_user_cannot_see_what_the_campus_shares(): void
    {
        $this->unauthorized_user()->get(route('facilities.index'))->assertForbidden();
    }

    public function test_staff_can_share_something_and_book_it_from_the_screen(): void
    {
        $actor = $this->authorized_user(['read facility', 'manage facility', 'book facility']);

        $actor->get(route('facilities.index'))->assertOk()->assertSee('What the campus shares');

        $actor->post(route('facilities.store'), [
            'name' => 'Main hall',
            'kind' => FacilityKind::Hall->value,
            'capacity' => 300,
        ])->assertRedirect();

        $hall = Facility::where('name', 'Main hall')->sole();

        $actor->post(route('facilities.book'), [
            'facility_id' => $hall->id,
            'starts_at' => now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i'),
            'ends_at' => now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'),
            'purpose' => 'Assembly',
        ])->assertRedirect();

        $this->assertSame(1, FacilityBooking::where('facility_id', $hall->id)->count());
    }

    public function test_reading_the_catalogue_does_not_allow_booking(): void
    {
        $actor = $this->authorized_user(['read facility']);
        $hall = $this->facility();

        $actor->post(route('facilities.book'), [
            'facility_id' => $hall->id,
            'starts_at' => now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i'),
            'ends_at' => now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'),
            'purpose' => 'Assembly',
        ])->assertForbidden();

        $this->assertSame(0, FacilityBooking::count());
    }

    public function test_taking_something_out_of_use_keeps_its_bookings(): void
    {
        $actor = $this->authorized_user(['read facility', 'manage facility', 'book facility']);
        $hall = $this->facility();
        app(BookFacility::class)->book($hall, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(11, 0), 'Rehearsal');

        $actor->delete(route('facilities.destroy', $hall->id))->assertRedirect();

        $this->assertFalse($hall->fresh()->is_active);
        $this->assertSame(1, FacilityBooking::where('facility_id', $hall->id)->count());
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
