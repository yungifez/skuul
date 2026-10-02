<?php

namespace Tests\Feature;

use App\Actions\Enrollment\ChangeEnrollmentStatus;
use App\Actions\Finance\ReceivePayment;
use App\Enums\EnrollmentStatus;
use App\Enums\LibraryReservationStatus;
use App\Enums\NoticeStatus;
use App\Livewire\CreateStudentForm;
use App\Livewire\EditSchoolForm;
use App\Models\DataSharingRequest;
use App\Models\LibraryReservation;
use App\Models\Notice;
use App\Models\School;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A school's "today" is the date on its own clocks, not the server's.
 */
class SchoolTimeZoneTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_edit_form_saves_the_time_zone(): void
    {
        $school = $this->workingSchool();
        $school->update(['address' => '12 Wharf Road', 'country' => 'Nigeria', 'state' => 'Lagos', 'city' => 'Lagos', 'postal_code' => '100001']);
        $this->authorized_user(['update school'], $school);

        Livewire::test(EditSchoolForm::class, ['school' => $school])
            ->set('timezone', 'Mars/Olympus')
            ->call('save')
            ->assertHasErrors(['timezone' => 'timezone']);

        Livewire::test(EditSchoolForm::class, ['school' => $school])
            ->set('timezone', 'Africa/Lagos')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Africa/Lagos', $school->fresh()->timezone);
    }

    public function test_a_school_without_a_zone_keeps_the_server_date(): void
    {
        $school = $this->workingSchool();
        $this->travelTo(now('UTC')->setDate(2026, 10, 1)->setTime(23, 30));

        $this->assertSame('2026-10-01', school_today($school)->toDateString());
        $this->assertSame('UTC', school_timezone($school));
    }

    public function test_today_follows_the_school_clock_past_midnight(): void
    {
        $school = $this->workingSchool();
        $this->inLagos($school);
        $this->travelTo(now('UTC')->setDate(2026, 10, 1)->setTime(23, 30));

        $this->assertSame('2026-10-02', school_today($school)->toDateString());
        $this->assertSame('2026-10-02 00:00:00', school_today($school)->format('Y-m-d H:i:s'));
        $this->assertSame('00:30', school_time(now(), $school)?->format('H:i'));
    }

    public function test_a_payment_taken_after_local_midnight_carries_the_school_date(): void
    {
        $school = $this->workingSchool();
        $this->inLagos($school);
        $this->travelTo(now('UTC')->setDate(2026, 10, 1)->setTime(23, 30));
        $enrollment = StudentRecord::factory()->create(['school_id' => $school->id]);

        $payment = app(ReceivePayment::class)->receive($enrollment, 5_000);

        $this->assertSame('2026-10-02', $payment->received_on->toDateString());
        $this->assertSame('2026-10-02', $payment->ledgerTransaction->transaction_date->toDateString());
    }

    public function test_the_header_shows_the_school_date(): void
    {
        $school = $this->workingSchool();
        $this->inLagos($school);
        $this->travelTo(now('UTC')->setDate(2026, 10, 1)->setTime(23, 30));
        $this->authorized_user([], $school);

        $this->get(route('dashboard'))->assertOk()->assertSee('Fri, Oct 2, 2026')->assertDontSee('Thu, Oct 1, 2026');
    }

    public function test_a_form_takes_the_school_date_after_local_midnight(): void
    {
        $school = $this->workingSchool();
        $this->inLagos($school);
        $this->travelTo(now('UTC')->setDate(2026, 10, 1)->setTime(23, 30));
        $this->authorized_user(['create student'], $school);

        Livewire::test(CreateStudentForm::class)
            ->assertSet('admissionDate', '2026-10-02')
            ->call('save')
            ->assertHasNoErrors('admissionDate')
            ->set('admissionDate', '2026-10-03')
            ->call('save')
            ->assertHasErrors('admissionDate');
    }

    /**
     * Put the school on Lagos time, where the date turns an hour before UTC.
     */
    public function test_a_notice_runs_to_the_end_of_its_last_day_at_each_school(): void
    {
        $lagos = $this->workingSchool();
        $this->inLagos($lagos);
        $losAngeles = School::factory()->create(['timezone' => 'America/Los_Angeles']);
        $this->travelTo(now('UTC')->setDate(2026, 10, 2)->setTime(3, 0));
        $endsInLagos = $this->noticeEnding($lagos, '2026-10-01');
        $endsInLosAngeles = $this->noticeEnding($losAngeles, '2026-10-01');

        $this->artisan('skuul:process-notices')->assertSuccessful();

        $this->assertNotSame(NoticeStatus::Published, $endsInLagos->fresh()->status);
        $this->assertSame(NoticeStatus::Published, $endsInLosAngeles->fresh()->status);
    }

    public function test_a_notice_ends_once_local_midnight_passes_before_the_server_date(): void
    {
        $school = $this->workingSchool();
        $this->inLagos($school);
        $this->travelTo(now('UTC')->setDate(2026, 10, 1)->setTime(23, 30));
        $notice = $this->noticeEnding($school, '2026-10-01');

        $this->artisan('skuul:process-notices')->assertSuccessful();

        $this->assertNotSame(NoticeStatus::Published, $notice->fresh()->status);
    }

    public function test_a_library_hold_lasts_to_the_end_of_the_school_day(): void
    {
        $losAngeles = School::factory()->create(['timezone' => 'America/Los_Angeles']);
        $this->travelTo(now('UTC')->setDate(2026, 10, 2)->setTime(3, 0));
        $hold = new LibraryReservation([
            'school_id' => $losAngeles->id,
            'status' => LibraryReservationStatus::Ready,
            'holds_until' => '2026-10-01',
        ]);

        $this->assertFalse($hold->holdHasRunOut());

        $this->travelTo(now('UTC')->setDate(2026, 10, 2)->setTime(8, 0));

        $this->assertTrue($hold->holdHasRunOut());
    }

    public function test_a_sharing_request_runs_out_on_the_asking_school_clock(): void
    {
        $school = $this->workingSchool();
        $this->inLagos($school);
        $request = new DataSharingRequest(['requesting_school_id' => $school->id, 'expires_on' => '2026-10-01']);

        $this->travelTo(now('UTC')->setDate(2026, 10, 1)->setTime(22, 30));
        $this->assertFalse($request->hasExpired());

        $this->travelTo(now('UTC')->setDate(2026, 10, 1)->setTime(23, 30));
        $this->assertTrue($request->hasExpired());
    }

    public function test_an_evening_change_at_a_school_behind_the_server_keeps_the_school_date(): void
    {
        $losAngeles = School::factory()->create(['timezone' => 'America/Los_Angeles']);
        $enrollment = StudentRecord::factory()->create(['school_id' => $losAngeles->id]);
        $this->travelTo(now('UTC')->setDate(2026, 10, 2)->setTime(3, 0));

        app(ChangeEnrollmentStatus::class)->change($enrollment, EnrollmentStatus::Withdrawn);

        $this->assertSame('2026-10-01', $enrollment->statusChanges()->sole()->effective_on->toDateString());
    }

    private function noticeEnding(School $school, string $lastDay): Notice
    {
        return Notice::factory()->create([
            'school_id' => $school->id,
            'status' => NoticeStatus::Published,
            'active' => true,
            'start_date' => '2026-09-01',
            'stop_date' => $lastDay,
        ]);
    }

    private function inLagos(School $school): void
    {
        $school->forceFill(['timezone' => 'Africa/Lagos'])->save();
        school_context()->set($school, remember: false);
    }
}
