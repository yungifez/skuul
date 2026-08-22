<?php

namespace Tests\Feature;

use App\Actions\Staff\ManageStaffLeave;
use App\Enums\EmploymentType;
use App\Enums\Feature;
use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Enums\StaffStatus;
use App\Models\StaffLeaveRequest;
use App\Models\StaffProfile;
use App\Models\User;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The staff screens say who works here, what they are qualified for, when they
 * can take work, and who is away.
 */
class StaffScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_staff_list_starts_empty(): void
    {
        $this->authorized_user(['read staff profile', 'create staff profile']);

        $this->get(route('staff-profiles.index'))
            ->assertOk()
            ->assertSee('No employment records yet')
            ->assertSee(route('staff-profiles.create'));
    }

    public function test_an_employment_record_is_written_from_the_screen(): void
    {
        $school = $this->workingSchool();
        $person = $this->memberOf($school, User::factory()->create(['name' => 'Ada Bell']));
        $this->authorized_user(['read staff profile', 'create staff profile'], $school);

        $response = $this->post(route('staff-profiles.store'), [
            'user_id' => $person->id,
            'staff_number' => 'STF-0001',
            'job_title' => 'Teacher',
            'department' => 'Science',
            'employment_type' => EmploymentType::FullTime->value,
            'joined_on' => now()->toDateString(),
        ]);

        $profile = StaffProfile::inSchool()->sole();

        $response->assertRedirect(route('staff-profiles.show', $profile));

        $this->assertSame('Teacher', $profile->job_title);
        $this->assertSame($school->id, $profile->school_id);
    }

    public function test_one_person_holds_one_employment_record_per_school(): void
    {
        $school = $this->workingSchool();
        $person = $this->memberOf($school, User::factory()->create());
        StaffProfile::factory()->create(['school_id' => $school->id, 'user_id' => $person->id]);
        $this->authorized_user(['read staff profile', 'create staff profile'], $school);

        $this->post(route('staff-profiles.store'), [
            'user_id' => $person->id,
            'employment_type' => EmploymentType::FullTime->value,
        ])->assertSessionHasErrors('user_id');

        $this->assertSame(1, StaffProfile::inSchool()->count());
    }

    public function test_the_job_is_changed_from_the_screen(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();

        $this->from(route('staff-profiles.show', $profile))
            ->put(route('staff-profiles.update', $profile), [
                'job_title' => 'Head of year',
                'employment_type' => EmploymentType::PartTime->value,
                'status' => StaffStatus::Active->value,
            ])
            ->assertRedirect(route('staff-profiles.show', $profile));

        $this->assertSame('Head of year', $profile->fresh()->job_title);
        $this->assertSame(EmploymentType::PartTime, $profile->fresh()->employment_type);
    }

    public function test_a_person_cannot_leave_before_they_joined(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();

        $this->from(route('staff-profiles.show', $profile))
            ->put(route('staff-profiles.update', $profile), [
                'employment_type' => EmploymentType::FullTime->value,
                'status' => StaffStatus::Left->value,
                'joined_on' => now()->toDateString(),
                'left_on' => now()->subYear()->toDateString(),
            ])
            ->assertSessionHasErrors('left_on');
    }

    public function test_a_qualification_and_working_hours_are_added(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();

        $this->from(route('staff-profiles.show', $profile))
            ->post(route('staff-profiles.credentials.store', $profile), [
                'type' => 'Licence',
                'name' => 'Teaching licence',
                'issuer' => 'The board',
                'expires_on' => now()->addYear()->toDateString(),
            ])
            ->assertRedirect(route('staff-profiles.show', $profile));

        $this->from(route('staff-profiles.show', $profile))
            ->post(route('staff-profiles.availabilities.store', $profile), [
                'day_of_week' => 1,
                'starts_at' => '08:00',
                'ends_at' => '15:00',
            ])
            ->assertRedirect(route('staff-profiles.show', $profile));

        $this->get(route('staff-profiles.show', $profile))
            ->assertOk()
            ->assertSee('Teaching licence')
            ->assertSee('Monday');

        $this->assertSame(1, $profile->credentials()->count());
        $this->assertSame(1, $profile->availabilities()->count());
    }

    public function test_working_hours_must_end_after_they_start(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();

        $this->from(route('staff-profiles.show', $profile))
            ->post(route('staff-profiles.availabilities.store', $profile), [
                'day_of_week' => 1,
                'starts_at' => '15:00',
                'ends_at' => '08:00',
            ])
            ->assertSessionHasErrors('ends_at');

        $this->assertSame(0, $profile->availabilities()->count());
    }

    public function test_leave_is_asked_for_from_the_screen(): void
    {
        $this->authorized_user(['read staff leave', 'request staff leave']);
        $profile = $this->profile();

        $this->from(route('staff-leave.index'))
            ->post(route('staff-leave.store'), [
                'staff_profile_id' => $profile->id,
                'type' => LeaveType::Annual->value,
                'starts_on' => now()->addWeek()->toDateString(),
                'ends_on' => now()->addWeeks(2)->toDateString(),
                'reason' => 'A family wedding.',
            ])
            ->assertRedirect(route('staff-leave.index'));

        $this->assertSame(1, StaffLeaveRequest::inSchool()->count());
        $this->assertSame(LeaveStatus::Requested, StaffLeaveRequest::inSchool()->sole()->status);
    }

    public function test_the_same_days_cannot_be_asked_for_twice(): void
    {
        $this->authorized_user(['read staff leave', 'request staff leave']);
        $profile = $this->profile();
        app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeeks(2));

        $this->from(route('staff-leave.index'))
            ->post(route('staff-leave.store'), [
                'staff_profile_id' => $profile->id,
                'type' => LeaveType::Annual->value,
                'starts_on' => now()->addWeek()->toDateString(),
                'ends_on' => now()->addWeeks(2)->toDateString(),
            ])
            ->assertSessionHasErrors('leave');

        $this->assertSame(1, StaffLeaveRequest::inSchool()->count());
    }

    public function test_leave_is_agreed_from_the_screen(): void
    {
        $this->authorized_user(['read staff leave', 'request staff leave', 'approve staff leave']);
        $profile = $this->profile();
        $leave = app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeeks(2));

        $this->from(route('staff-leave.index'))
            ->put(route('staff-leave.status.update', $leave), ['status' => LeaveStatus::Approved->value])
            ->assertRedirect(route('staff-leave.index'));

        $this->assertSame(LeaveStatus::Approved, $leave->fresh()->status);
        $this->assertSame(1, $leave->statusChanges()->count());
    }

    public function test_a_person_never_answers_their_own_request(): void
    {
        $school = $this->workingSchool();
        $person = $this->memberOf($school);
        $profile = StaffProfile::factory()->create(['school_id' => $school->id, 'user_id' => $person->id]);
        $leave = app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeeks(2), actor: $person);

        school_context()->set($school, remember: false);
        $person->givePermissionTo(['read staff leave', 'request staff leave', 'approve staff leave']);
        $this->actingAs($person->refresh());

        $this->from(route('staff-leave.index'))
            ->put(route('staff-leave.status.update', $leave), ['status' => LeaveStatus::Approved->value])
            ->assertForbidden();

        $this->assertSame(LeaveStatus::Requested, $leave->fresh()->status);
    }

    public function test_the_leave_board_names_who_is_away_today(): void
    {
        $school = $this->workingSchool();
        $away = $this->memberOf($school, User::factory()->create(['name' => 'Ada Bell']));
        $profile = StaffProfile::factory()->create(['school_id' => $school->id, 'user_id' => $away->id]);
        $this->authorized_user(['read staff leave', 'request staff leave', 'approve staff leave'], $school);
        $leave = app(ManageStaffLeave::class)->request($profile, now(), now()->addDay());
        app(ManageStaffLeave::class)->approve($leave);

        $this->get(route('staff-leave.index'))
            ->assertOk()
            ->assertSee('Ada Bell')
            ->assertDontSee('Everybody is in today.');
    }

    public function test_the_screens_need_permission(): void
    {
        $this->unauthorized_user();

        $this->get(route('staff-profiles.index'))->assertForbidden();
        $this->get(route('staff-leave.index'))->assertForbidden();
    }

    public function test_a_school_that_turned_staff_operations_off_has_no_screens(): void
    {
        $this->authorized_user(['read staff profile', 'read staff leave']);
        app(FeatureManager::class)->disable(Feature::StaffOperations);

        $this->get(route('staff-profiles.index'))->assertNotFound();
        $this->get(route('staff-leave.index'))->assertNotFound();
    }

    /**
     * Make an employment record in the working school.
     */
    private function profile(): StaffProfile
    {
        $school = $this->workingSchool();

        return StaffProfile::factory()->create([
            'school_id' => $school->id,
            'user_id' => $this->memberOf($school)->id,
        ]);
    }
}
