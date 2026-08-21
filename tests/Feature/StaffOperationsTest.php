<?php

namespace Tests\Feature;

use App\Actions\Staff\ManageStaffLeave;
use App\Enums\AuditAction;
use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Enums\StaffStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AuditEvent;
use App\Models\School;
use App\Models\StaffAvailability as StaffAvailabilityRecord;
use App\Models\StaffCredential;
use App\Models\StaffProfile;
use App\Services\Staff\StaffAvailability;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use RuntimeException;
use Tests\TestCase;

/**
 * Employment records, qualifications, working hours, and leave.
 */
class StaffOperationsTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_employment_record_belongs_to_one_school(): void
    {
        $this->authorized_user(['read staff profile']);

        $profile = StaffProfile::factory()->create();

        $this->assertSame($this->workingSchool()->id, $profile->school_id);
        $this->assertSame(StaffStatus::Active, $profile->status);
        $this->assertTrue($profile->status->canBeGivenWork());
    }

    public function test_a_person_reads_their_own_record(): void
    {
        $this->unauthorized_user();
        $profile = StaffProfile::factory()->create(['user_id' => auth()->id()]);

        $this->assertTrue(Gate::forUser(auth()->user())->allows('view', $profile));
    }

    public function test_another_school_never_reads_the_record(): void
    {
        $this->authorized_user(['read staff profile']);
        $profile = StaffProfile::factory()->create();

        $this->authorized_user(['read staff profile'], School::factory()->create());

        $this->assertFalse(Gate::forUser(auth()->user())->allows('view', $profile));
    }

    public function test_a_credential_says_when_it_runs_out(): void
    {
        $this->authorized_user(['read staff profile']);
        $profile = StaffProfile::factory()->create();

        $current = StaffCredential::create([
            'staff_profile_id' => $profile->id,
            'type' => 'certificate',
            'name' => 'First aid',
            'expires_on' => now()->addYear(),
        ]);
        $old = StaffCredential::create([
            'staff_profile_id' => $profile->id,
            'type' => 'certificate',
            'name' => 'Fire safety',
            'expires_on' => now()->subDay(),
        ]);

        $this->assertFalse($current->hasExpired());
        $this->assertTrue($old->hasExpired());
        $this->assertFalse($old->isVerified());
        $this->assertSame(
            [$old->id],
            StaffCredential::expiringBefore(now())->pluck('id')->all()
        );
    }

    public function test_leave_is_asked_for_and_agreed(): void
    {
        $this->authorized_user(['approve staff leave']);
        $profile = StaffProfile::factory()->create();
        $action = app(ManageStaffLeave::class);

        $request = $action->request($profile, now()->addWeek(), now()->addWeek()->addDays(2), LeaveType::Annual, 'A wedding');

        $this->assertSame(LeaveStatus::Requested, $request->status);
        $this->assertSame(3, $request->days());

        $action->approve($request, note: 'Cover arranged');

        $this->assertSame(LeaveStatus::Approved, $request->fresh()->status);
        $this->assertSame(auth()->id(), $request->fresh()->decided_by);
        $this->assertNotNull($request->fresh()->decided_at);
    }

    public function test_leave_cannot_end_before_it_starts(): void
    {
        $this->authorized_user(['request staff leave']);
        $profile = StaffProfile::factory()->create();

        $this->expectException(InvalidValueException::class);

        app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addDay());
    }

    public function test_the_same_days_cannot_be_asked_for_twice(): void
    {
        $this->authorized_user(['request staff leave']);
        $profile = StaffProfile::factory()->create();
        $action = app(ManageStaffLeave::class);
        $action->request($profile, now()->addWeek(), now()->addWeek()->addDays(3));

        $this->expectException(InvalidValueException::class);

        $action->request($profile, now()->addWeek()->addDays(2), now()->addWeek()->addDays(5));
    }

    public function test_days_free_again_after_a_request_is_cancelled(): void
    {
        $this->authorized_user(['request staff leave']);
        $profile = StaffProfile::factory()->create();
        $action = app(ManageStaffLeave::class);
        $first = $action->request($profile, now()->addWeek(), now()->addWeek()->addDays(3));
        $action->cancel($first);

        $second = $action->request($profile, now()->addWeek(), now()->addWeek()->addDays(3));

        $this->assertSame(LeaveStatus::Requested, $second->status);
    }

    public function test_a_person_who_left_asks_for_nothing(): void
    {
        $this->authorized_user(['request staff leave']);
        $profile = StaffProfile::factory()->left()->create();

        $this->expectException(InvalidValueException::class);

        app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeek());
    }

    public function test_declined_leave_cannot_become_approved(): void
    {
        $this->authorized_user(['approve staff leave']);
        $profile = StaffProfile::factory()->create();
        $action = app(ManageStaffLeave::class);
        $request = $action->request($profile, now()->addWeek(), now()->addWeek());
        $action->decline($request, note: 'Too many people away');

        $this->expectException(InvalidValueException::class);

        $action->approve($request);
    }

    public function test_leave_history_cannot_be_changed(): void
    {
        $this->authorized_user(['approve staff leave']);
        $profile = StaffProfile::factory()->create();
        $action = app(ManageStaffLeave::class);
        $request = $action->request($profile, now()->addWeek(), now()->addWeek());
        $action->approve($request);

        $this->expectException(RuntimeException::class);

        $request->statusChanges()->firstOrFail()->update(['reason' => 'Something else']);
    }

    public function test_nobody_agrees_to_their_own_days_away(): void
    {
        $this->authorized_user(['approve staff leave', 'request staff leave']);
        $own = StaffProfile::factory()->create(['user_id' => auth()->id()]);
        $other = StaffProfile::factory()->create();
        $action = app(ManageStaffLeave::class);

        $mine = $action->request($own, now()->addWeek(), now()->addWeek());
        $theirs = $action->request($other, now()->addWeek(), now()->addWeek());

        $this->assertFalse(Gate::forUser(auth()->user())->allows('decide', $mine));
        $this->assertTrue(Gate::forUser(auth()->user())->allows('decide', $theirs));
    }

    public function test_a_person_on_leave_is_not_free(): void
    {
        $this->authorized_user(['approve staff leave']);
        $profile = StaffProfile::factory()->create();
        $action = app(ManageStaffLeave::class);
        $request = $action->request($profile, now()->addWeek(), now()->addWeek()->addDays(2));
        $action->approve($request);

        $availability = app(StaffAvailability::class);

        $this->assertTrue($availability->isAway($profile, now()->addWeek()->addDay()));
        $this->assertFalse($availability->isFree($profile, now()->addWeek()->addDay()));
        $this->assertTrue($availability->isFree($profile, now()->addMonth()));
        $this->assertSame([$profile->id], $availability->awayOn(now()->addWeek())->pluck('id')->all());
    }

    public function test_working_hours_limit_when_a_person_can_be_given_work(): void
    {
        $this->authorized_user(['read staff profile']);
        $profile = StaffProfile::factory()->create();
        $monday = now()->next('Monday');
        StaffAvailabilityRecord::create([
            'staff_profile_id' => $profile->id,
            'day_of_week' => 1,
            'starts_at' => '08:00:00',
            'ends_at' => '12:00:00',
        ]);

        $availability = app(StaffAvailability::class);

        $this->assertTrue($availability->isFree($profile, $monday, '09:00:00', '10:00:00'));
        $this->assertFalse($availability->isFree($profile, $monday, '11:00:00', '13:00:00'));
        $this->assertFalse($availability->isFree($profile, $monday->copy()->addDay(), '09:00:00', '10:00:00'));
    }

    public function test_a_person_with_no_hours_is_treated_as_free(): void
    {
        $this->authorized_user(['read staff profile']);
        $profile = StaffProfile::factory()->create();

        $this->assertTrue(app(StaffAvailability::class)->isFree($profile, now()->next('Monday'), '09:00:00', '10:00:00'));
    }

    public function test_the_free_list_leaves_out_people_who_left(): void
    {
        $this->authorized_user(['read staff profile']);
        $working = StaffProfile::factory()->create();
        StaffProfile::factory()->left()->create();

        $free = app(StaffAvailability::class)->freeOn(now()->next('Monday'));

        $this->assertSame([$working->id], $free->pluck('id')->all());
    }

    public function test_asking_for_leave_is_written_to_the_audit_log(): void
    {
        $this->authorized_user(['request staff leave']);
        $profile = StaffProfile::factory()->create();

        $request = app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeek());

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::StaffLeaveRequested)->forSubject($request)->first());
    }
}
