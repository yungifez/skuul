<?php

namespace Tests\Feature;

use App\Actions\Staff\ManageStaffLeave;
use App\Console\Commands\EndLeaversAccess;
use App\Enums\EmploymentType;
use App\Enums\Feature;
use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Enums\StaffStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\CreateStaffProfileForm;
use App\Livewire\StaffLeaveBoard;
use App\Livewire\StaffProfileDirectory;
use App\Livewire\StaffProfileRecord;
use App\Models\School;
use App\Models\StaffAvailability;
use App\Models\StaffLeaveRequest;
use App\Models\StaffProfile;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    public function test_staff_directory_search_and_away_filter_update_without_a_page_submission(): void
    {
        $school = $this->workingSchool();
        $ada = $this->memberOf($school, User::factory()->create(['name' => 'Ada Bell']));
        $ben = $this->memberOf($school, User::factory()->create(['name' => 'Ben Cedar']));
        $adaProfile = StaffProfile::factory()->create(['school_id' => $school->id, 'user_id' => $ada->id]);
        StaffProfile::factory()->create(['school_id' => $school->id, 'user_id' => $ben->id]);
        $this->authorized_user(['read staff profile', 'read staff leave'], $school);

        $leave = app(ManageStaffLeave::class)->request($adaProfile, now(), now());
        app(ManageStaffLeave::class)->approve($leave);

        $this->get(route('staff-profiles.index', ['away' => '1']))
            ->assertOk()
            ->assertSee('Ada Bell')
            ->assertDontSee('Ben Cedar');

        Livewire::test(StaffProfileDirectory::class)
            ->set('search', 'Ada Bell')
            ->assertSee('Ada Bell')
            ->assertDontSee('Ben Cedar')
            ->set('search', '')
            ->set('awayOnly', true)
            ->assertSee('Ada Bell')
            ->assertDontSee('Ben Cedar')
            ->call('clearFilters')
            ->assertSee('Ada Bell')
            ->assertSee('Ben Cedar');
    }

    public function test_an_employment_record_is_written_from_the_screen(): void
    {
        $school = $this->workingSchool();
        $person = $this->memberOf($school, User::factory()->create(['name' => 'Ada Bell']));
        $this->authorized_user(['read staff profile', 'create staff profile'], $school);

        $this->get(route('staff-profiles.create'))->assertOk()->assertSeeLivewire(CreateStaffProfileForm::class);

        $component = Livewire::test(CreateStaffProfileForm::class)
            ->set('userId', (string) $person->id)
            ->set('staffNumber', ' STF-0001 ')
            ->set('jobTitle', 'Teacher')
            ->set('department', 'Science')
            ->call('save')
            ->assertHasNoErrors();

        $profile = StaffProfile::inSchool()->sole();

        $component->assertRedirect(route('staff-profiles.show', $profile));

        $this->assertSame('Teacher', $profile->job_title);
        $this->assertSame('STF-0001', $profile->staff_number);
        $this->assertSame($school->id, $profile->school_id);
    }

    public function test_one_person_holds_one_employment_record_per_school(): void
    {
        $school = $this->workingSchool();
        $person = $this->memberOf($school, User::factory()->create());
        StaffProfile::factory()->create(['school_id' => $school->id, 'user_id' => $person->id]);
        $this->authorized_user(['read staff profile', 'create staff profile'], $school);

        Livewire::test(CreateStaffProfileForm::class)
            ->set('userId', (string) $person->id)
            ->call('save')
            ->assertHasErrors('userId')
            ->assertSee('This person already has an employment record here.');

        $this->assertSame(1, StaffProfile::inSchool()->count());
    }

    public function test_a_learner_who_moved_away_is_not_made_staff_at_the_campus_they_left(): void
    {
        $school = $this->workingSchool();
        // The membership here stays after a move. The enrollment went with them.
        $learner = $this->memberOf($school, User::factory()->create(['name' => 'Moved Learner']));
        StudentRecord::factory()->create(['user_id' => $learner->id, 'school_id' => School::factory()->create()->id]);
        $this->authorized_user(['read staff profile', 'create staff profile'], $school);

        Livewire::test(CreateStaffProfileForm::class)
            ->assertDontSee('Moved Learner')
            ->set('userId', (string) $learner->id)
            ->call('save')
            ->assertHasErrors('userId');

        $this->assertFalse(StaffProfile::query()->where('user_id', $learner->id)->exists());
    }

    public function test_a_staff_number_is_held_by_one_person_per_school(): void
    {
        $school = $this->workingSchool();
        StaffProfile::factory()->create(['school_id' => $school->id, 'user_id' => $this->memberOf($school)->id, 'staff_number' => 'STF-7']);
        $newcomer = $this->memberOf($school);
        $outsider = $this->memberOf(School::factory()->create(), $this->nonMember());
        $this->authorized_user(['read staff profile', 'create staff profile', 'update staff profile'], $school);

        Livewire::test(CreateStaffProfileForm::class)
            ->set('userId', (string) $newcomer->id)
            ->set('staffNumber', 'stf-7')
            ->call('save')
            ->assertHasErrors('staffNumber')
            ->set('userId', (string) $outsider->id)
            ->set('staffNumber', '')
            ->call('save')
            ->assertHasErrors(['userId' => 'exists']);

        $profile = $this->profile();

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->call('startEditingJob')
            ->set('staffNumber', 'STF-7')
            ->call('saveJob')
            ->assertHasErrors('staffNumber');

        $this->assertNotSame('STF-7', $profile->fresh()->staff_number);
        $this->assertSame(2, StaffProfile::inSchool()->count());
    }

    public function test_the_job_is_changed_from_the_screen(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();

        $this->get(route('staff-profiles.show', $profile))->assertOk()->assertSeeLivewire(StaffProfileRecord::class);

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->call('startEditingJob')
            ->set('jobTitle', 'Head of year')
            ->set('employmentType', EmploymentType::PartTime->value)
            ->call('saveJob')
            ->assertHasNoErrors()
            ->assertSet('isEditingJob', false)
            ->assertSee('Head of year');

        $this->assertSame('Head of year', $profile->fresh()->job_title);
        $this->assertSame(EmploymentType::PartTime, $profile->fresh()->employment_type);
    }

    public function test_a_person_cannot_leave_before_they_joined(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();
        $profile->update(['joined_on' => now()->toDateString()]);

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->call('startEditingJob')
            ->set('status', StaffStatus::Left->value)
            ->set('leftOn', now()->subYear()->toDateString())
            ->call('saveJob')
            ->assertHasErrors('leftOn');

        $this->assertNotSame(StaffStatus::Left, $profile->fresh()->status);
    }

    public function test_a_person_who_leaves_gets_a_date_and_loses_leave_after_it(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();
        $past = app(ManageStaffLeave::class)->request($profile, now()->subWeeks(3), now()->subWeeks(2));
        app(ManageStaffLeave::class)->approve($past);
        $ahead = app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeeks(2));
        app(ManageStaffLeave::class)->approve($ahead);

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->call('startEditingJob')
            ->set('status', StaffStatus::Left->value)
            ->call('saveJob')
            ->assertHasNoErrors();

        $profile->refresh();
        $this->assertSame(now()->toDateString(), $profile->left_on?->toDateString());
        $this->assertSame(LeaveStatus::Cancelled, $ahead->fresh()->status);
        $this->assertSame(LeaveStatus::Approved, $past->fresh()->status);

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->call('startEditingJob')
            ->set('status', StaffStatus::Active->value)
            ->call('saveJob');

        $this->assertNull($profile->fresh()->left_on);
    }

    public function test_a_person_whose_last_day_passed_loses_access_to_the_campus(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();
        $profile->update(['joined_on' => now()->subYear()->toDateString()]);

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->call('startEditingJob')
            ->set('status', StaffStatus::Left->value)
            ->set('leftOn', now()->subDay()->toDateString())
            ->call('saveJob')
            ->assertHasNoErrors();

        $this->assertFalse($profile->user->refresh()->belongsToSchool($profile->school_id));
    }

    public function test_a_person_holding_more_cannot_be_recorded_as_left_by_somebody_holding_less(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();
        $profile->user->assignRole('admin');

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->call('startEditingJob')
            ->set('status', StaffStatus::Left->value)
            ->set('leftOn', now()->subDay()->toDateString())
            ->call('saveJob')
            ->assertHasErrors(['status' => 'This person holds more at this school than you do, so you cannot record that they left.']);

        $this->assertNotSame(StaffStatus::Left, $profile->fresh()->status);
        $this->assertTrue($profile->user->refresh()->belongsToSchool($profile->school_id));

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile->fresh()])
            ->call('startEditingJob')
            ->set('jobTitle', 'Principal')
            ->call('saveJob')
            ->assertHasNoErrors();

        $this->assertSame('Principal', $profile->fresh()->job_title);
    }

    public function test_a_leaver_taken_back_can_sign_in_to_the_campus_again(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();
        $profile->update(['joined_on' => now()->subYear()->toDateString()]);
        $screen = Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->call('startEditingJob')
            ->set('status', StaffStatus::Left->value)
            ->set('leftOn', now()->subDay()->toDateString())
            ->call('saveJob');

        $screen->call('startEditingJob')
            ->set('status', StaffStatus::Active->value)
            ->call('saveJob')
            ->assertHasNoErrors();

        $this->assertTrue($profile->user->refresh()->belongsToSchool($profile->school_id));
    }

    public function test_a_leaver_who_became_a_learner_cannot_be_taken_back(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();
        $profile->update(['joined_on' => now()->subYear()->toDateString()]);
        $screen = Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->call('startEditingJob')
            ->set('status', StaffStatus::Left->value)
            ->set('leftOn', now()->subDay()->toDateString())
            ->call('saveJob');
        StudentRecord::factory()->create(['user_id' => $profile->user_id, 'school_id' => $profile->school_id]);

        $screen->call('startEditingJob')
            ->set('status', StaffStatus::Active->value)
            ->call('saveJob')
            ->assertHasErrors(['status' => 'This person is now a learner. A learner cannot be made staff.']);

        $this->assertSame(StaffStatus::Left, $profile->fresh()->status);
        $this->assertFalse($profile->user->refresh()->belongsToSchool($profile->school_id));
    }

    public function test_a_person_leaving_later_keeps_access_until_the_day_after_their_last_day(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->call('startEditingJob')
            ->set('status', StaffStatus::Left->value)
            ->set('leftOn', now()->addDays(3)->toDateString())
            ->call('saveJob')
            ->assertHasNoErrors();

        $this->artisan(EndLeaversAccess::class)->assertSuccessful();
        $this->assertTrue($profile->user->refresh()->belongsToSchool($profile->school_id));

        $this->travel(3)->days();
        $this->artisan(EndLeaversAccess::class)->assertSuccessful();
        $this->assertTrue($profile->user->refresh()->belongsToSchool($profile->school_id), 'They still work on their last day.');

        $this->travel(1)->days();
        $this->artisan(EndLeaversAccess::class)->assertSuccessful();
        $this->assertFalse($profile->user->refresh()->belongsToSchool($profile->school_id));
    }

    public function test_a_qualification_and_working_hours_are_added_and_removed(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->set('credentialType', 'Licence')
            ->set('credentialName', 'Teaching licence')
            ->set('credentialIssuer', 'The board')
            ->set('credentialExpiresOn', now()->addYear()->toDateString())
            ->call('addCredential')
            ->assertHasNoErrors()
            ->set('dayOfWeek', '1')
            ->set('startsAt', '08:00')
            ->set('endsAt', '15:00')
            ->call('addHours')
            ->assertHasNoErrors()
            ->assertSee('Teaching licence')
            ->assertSee('Monday');

        $this->assertSame(1, $profile->credentials()->count());
        $this->assertSame(1, $profile->availabilities()->count());

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->call('removeCredential', $profile->credentials()->sole()->id)
            ->call('removeHours', $profile->availabilities()->sole()->id);

        $this->assertSame(0, $profile->credentials()->count());
        $this->assertSame(0, $profile->availabilities()->count());
    }

    public function test_working_hours_must_end_after_they_start_and_not_overlap(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $profile = $this->profile();

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->set('startsAt', '15:00')
            ->set('endsAt', '08:00')
            ->call('addHours')
            ->assertHasErrors(['endsAt' => 'after'])
            ->set('startsAt', '08:00')
            ->set('endsAt', '12:00')
            ->call('addHours')
            ->assertHasNoErrors()
            ->set('startsAt', '11:00')
            ->set('endsAt', '14:00')
            ->call('addHours')
            ->assertHasErrors('startsAt')
            ->set('startsAt', '12:00')
            ->set('endsAt', '14:00')
            ->call('addHours')
            ->assertHasNoErrors();

        $this->assertSame(2, $profile->availabilities()->count());
    }

    public function test_a_reader_sees_the_record_but_cannot_change_it(): void
    {
        $this->authorized_user(['read staff profile']);
        $profile = $this->profile();

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->assertDontSeeHtml('wire:click="startEditingJob"')
            ->call('startEditingJob')
            ->assertForbidden();

        Livewire::test(StaffProfileRecord::class, ['profile' => $profile])
            ->set('credentialType', 'Licence')
            ->set('credentialName', 'Forged')
            ->call('addCredential')
            ->assertForbidden();

        $this->assertSame(0, $profile->credentials()->count());
    }

    public function test_a_record_of_another_school_is_out_of_reach(): void
    {
        $this->authorized_user(['read staff profile', 'update staff profile']);
        $school = School::factory()->create();
        $theirs = StaffProfile::factory()->create(['school_id' => $school->id, 'user_id' => $this->memberOf($school)->id]);
        $mine = $this->profile();
        $theirHours = StaffAvailability::create(['staff_profile_id' => $theirs->id, 'day_of_week' => 1, 'starts_at' => '08:00', 'ends_at' => '12:00']);

        Livewire::test(StaffProfileRecord::class, ['profile' => $theirs])->assertForbidden();

        $this->assertThrows(
            fn () => Livewire::test(StaffProfileRecord::class, ['profile' => $mine])->call('removeHours', $theirHours->id),
            ModelNotFoundException::class,
        );

        $this->assertNotNull($theirHours->fresh());
    }

    public function test_leave_is_asked_for_from_the_screen(): void
    {
        $this->authorized_user(['read staff leave', 'request staff leave']);
        $profile = $this->profile();

        $this->get(route('staff-leave.index'))->assertOk()->assertSeeLivewire(StaffLeaveBoard::class);

        Livewire::test(StaffLeaveBoard::class)
            ->set('staffProfileId', (string) $profile->id)
            ->set('leaveType', LeaveType::Annual->value)
            ->set('startsOn', now()->addWeek()->toDateString())
            ->set('endsOn', now()->addWeeks(2)->toDateString())
            ->set('reason', 'A family wedding.')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(1, StaffLeaveRequest::inSchool()->count());
        $this->assertSame(LeaveStatus::Requested, StaffLeaveRequest::inSchool()->sole()->status);
    }

    public function test_the_same_days_cannot_be_asked_for_twice(): void
    {
        $this->authorized_user(['read staff leave', 'request staff leave']);
        $profile = $this->profile();
        app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeeks(2));

        $this->assertThrows(
            fn () => app(ManageStaffLeave::class)->request($profile, now()->addWeeks(2), now()->addWeeks(3)),
            InvalidValueException::class,
            'These days are already asked for.',
        );

        $this->assertSame(1, StaffLeaveRequest::inSchool()->count());
    }

    public function test_declined_days_asked_for_again_cannot_clash_with_newer_leave(): void
    {
        $this->authorized_user(['read staff leave', 'request staff leave']);
        $profile = $this->profile();
        $declined = app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeeks(2));
        app(ManageStaffLeave::class)->decline($declined);
        app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeek()->addDay());

        $this->assertThrows(
            fn () => app(ManageStaffLeave::class)->changeStatus($declined->fresh(), LeaveStatus::Requested),
            InvalidValueException::class,
            'These days are already asked for.',
        );

        $this->assertSame(LeaveStatus::Declined, $declined->fresh()->status);
    }

    public function test_leave_can_be_requested_without_leaving_the_screen(): void
    {
        $this->authorized_user(['read staff leave', 'request staff leave']);
        $profile = $this->profile();

        Livewire::test(StaffLeaveBoard::class)
            ->set('staffProfileId', (string) $profile->id)
            ->set('leaveType', LeaveType::Annual->value)
            ->set('startsOn', now()->addWeek()->toDateString())
            ->set('endsOn', now()->addWeeks(2)->toDateString())
            ->set('reason', 'Family wedding')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('The leave was asked for.');

        $this->assertSame(1, StaffLeaveRequest::inSchool()->count());
        $this->assertSame('Family wedding', StaffLeaveRequest::inSchool()->sole()->reason);
    }

    public function test_leave_form_rejects_backwards_dates_and_overlapping_days(): void
    {
        $this->authorized_user(['read staff leave', 'request staff leave']);
        $profile = $this->profile();

        Livewire::test(StaffLeaveBoard::class)
            ->set('staffProfileId', (string) $profile->id)
            ->set('startsOn', now()->addWeeks(2)->toDateString())
            ->set('endsOn', now()->addWeek()->toDateString())
            ->call('save')
            ->assertHasErrors(['endsOn' => 'after_or_equal']);

        app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeeks(2));

        Livewire::test(StaffLeaveBoard::class)
            ->set('staffProfileId', (string) $profile->id)
            ->set('startsOn', now()->addWeek()->toDateString())
            ->set('endsOn', now()->addWeeks(2)->toDateString())
            ->call('save')
            ->assertHasErrors('leave');

        $this->assertSame(1, StaffLeaveRequest::inSchool()->count());
    }

    public function test_leave_filters_update_the_results_and_can_be_cleared(): void
    {
        $this->authorized_user(['read staff leave', 'request staff leave']);
        $requestedProfile = $this->profile();
        $declinedProfile = $this->profile();
        app(ManageStaffLeave::class)->request($requestedProfile, now()->addWeek(), now()->addWeek()->addDay(), reason: 'Requested only');
        $declined = app(ManageStaffLeave::class)->request($declinedProfile, now()->addWeeks(3), now()->addWeeks(3)->addDay(), reason: 'Declined only');
        app(ManageStaffLeave::class)->decline($declined);

        Livewire::test(StaffLeaveBoard::class)
            ->set('status', LeaveStatus::Declined->value)
            ->assertSee('Declined only')
            ->assertDontSee('Requested only')
            ->call('clearFilters')
            ->assertSee('Declined only')
            ->assertSee('Requested only');
    }

    public function test_leave_request_can_be_approved_and_self_approval_is_forbidden(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user(['read staff leave', 'request staff leave', 'approve staff leave'], $school);
        $profile = $this->profile();
        $leave = app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeek()->addDay());

        Livewire::test(StaffLeaveBoard::class)
            ->call('changeStatus', $leave->id, LeaveStatus::Approved->value)
            ->assertSee('The leave is now Approved.');

        $this->assertSame(LeaveStatus::Approved, $leave->fresh()->status);

        $person = $this->memberOf($school);
        $selfProfile = StaffProfile::factory()->create(['school_id' => $school->id, 'user_id' => $person->id]);
        $selfLeave = app(ManageStaffLeave::class)->request($selfProfile, now()->addMonths(2), now()->addMonths(2)->addDay(), actor: $person);
        $person->givePermissionTo(['read staff leave', 'approve staff leave']);
        $this->actingAs($person->refresh());

        Livewire::test(StaffLeaveBoard::class)
            ->call('changeStatus', $selfLeave->id, LeaveStatus::Approved->value)
            ->assertForbidden();

        $this->assertSame(LeaveStatus::Requested, $selfLeave->fresh()->status);
    }

    public function test_leave_is_agreed_from_the_screen(): void
    {
        $this->authorized_user(['read staff leave', 'request staff leave', 'approve staff leave']);
        $profile = $this->profile();
        $leave = app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeeks(2));

        Livewire::test(StaffLeaveBoard::class)
            ->call('changeStatus', $leave->id, LeaveStatus::Approved->value)
            ->assertHasNoErrors();

        $this->assertSame(LeaveStatus::Approved, $leave->fresh()->status);
        $this->assertSame(1, $leave->statusChanges()->count());
    }

    public function test_a_second_approver_never_overturns_the_first_answer(): void
    {
        $this->authorized_user(['read staff leave', 'request staff leave', 'approve staff leave']);
        $profile = $this->profile();
        $leave = app(ManageStaffLeave::class)->request($profile, now()->addWeek(), now()->addWeeks(2));
        $asReadByTheSecond = $leave->fresh();

        app(ManageStaffLeave::class)->approve($leave);

        $this->assertThrows(
            fn () => app(ManageStaffLeave::class)->decline($asReadByTheSecond),
            InvalidValueException::class,
            'Somebody else already made this leave Approved.',
        );

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

        Livewire::test(StaffLeaveBoard::class)
            ->call('changeStatus', $leave->id, LeaveStatus::Declined->value)
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
