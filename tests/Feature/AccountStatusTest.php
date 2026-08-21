<?php

namespace Tests\Feature;

use App\Actions\Identity\ChangeAccountStatus;
use App\Enums\AccountStatus;
use App\Events\AccountStatusChanged;
use App\Models\AccountInvitation;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountStatusTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_authorized_user_can_suspend_an_account()
    {
        $target = User::factory()->create();

        $this->authorized_user(['manage account access'])
            ->post(route('users.account-status', $target->id), [
                'account_status' => AccountStatus::Suspended->value,
            ])->assertRedirect();

        $this->assertSame(AccountStatus::Suspended, $target->fresh()->account_status);
    }

    public function test_authorized_user_can_reinstate_a_suspended_account()
    {
        $target = User::factory()->suspended()->create();

        $this->authorized_user(['manage account access'])
            ->post(route('users.account-status', $target->id), [
                'account_status' => AccountStatus::Active->value,
            ])->assertRedirect();

        $this->assertSame(AccountStatus::Active, $target->fresh()->account_status);
    }

    public function test_reinstating_an_account_with_no_password_returns_it_to_invited()
    {
        $target = User::factory()->invited()->create();
        $target->forceFill(['account_status' => AccountStatus::Suspended])->save();

        $this->authorized_user(['manage account access'])
            ->post(route('users.account-status', $target->id), [
                'account_status' => AccountStatus::Active->value,
            ])->assertRedirect();

        $this->assertSame(AccountStatus::Invited, $target->fresh()->account_status);
    }

    public function test_unauthorized_user_cannot_change_an_account_status()
    {
        $target = User::factory()->create();

        $this->unauthorized_user()
            ->post(route('users.account-status', $target->id), [
                'account_status' => AccountStatus::Suspended->value,
            ])->assertForbidden();

        $this->assertSame(AccountStatus::Active, $target->fresh()->account_status);
    }

    public function test_an_administrator_cannot_change_an_account_in_another_school()
    {
        $otherSchool = School::factory()->create();
        $target = $this->memberOf($otherSchool, User::factory()->create());
        $target->schoolMemberships()->where('school_id', '!=', $otherSchool->id)->delete();

        $this->authorized_user(['manage account access'])
            ->post(route('users.account-status', $target->id), [
                'account_status' => AccountStatus::Suspended->value,
            ])->assertForbidden();

        $this->assertSame(AccountStatus::Active, $target->fresh()->account_status);
    }

    public function test_a_user_cannot_change_their_own_account_status()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage account access');

        $this->actingAs($user)
            ->post(route('users.account-status', $user->id), [
                'account_status' => AccountStatus::Suspended->value,
            ])->assertForbidden();

        $this->assertSame(AccountStatus::Active, $user->fresh()->account_status);
    }

    public function test_the_invited_state_cannot_be_set_directly()
    {
        $target = User::factory()->create();

        $this->authorized_user(['manage account access'])
            ->post(route('users.account-status', $target->id), [
                'account_status' => AccountStatus::Invited->value,
            ])->assertSessionHasErrors('account_status');
    }

    public function test_suspending_an_account_revokes_its_pending_invitations()
    {
        $target = User::factory()->invited()->create();
        AccountInvitation::factory()->create(['user_id' => $target->id]);

        $this->authorized_user(['manage account access'])
            ->post(route('users.account-status', $target->id), [
                'account_status' => AccountStatus::Suspended->value,
            ])->assertRedirect();

        $this->assertNotNull($target->accountInvitations()->first()->revoked_at);
    }

    public function test_changing_a_status_raises_the_audit_event()
    {
        Event::fake([AccountStatusChanged::class]);

        $target = User::factory()->create();

        $this->authorized_user(['manage account access'])
            ->post(route('users.account-status', $target->id), [
                'account_status' => AccountStatus::Suspended->value,
                'reason' => 'Left the school',
            ])->assertRedirect();

        Event::assertDispatched(AccountStatusChanged::class, function (AccountStatusChanged $event) use ($target): bool {
            return $event->user->is($target)
                && $event->from === AccountStatus::Active
                && $event->to === AccountStatus::Suspended
                && $event->reason === 'Left the school';
        });
    }

    public function test_a_platform_administrator_account_cannot_be_suspended()
    {
        $superAdmin = User::factory()->platformAdmin()->create();

        $this->expectException(\RuntimeException::class);

        app(ChangeAccountStatus::class)->suspend($superAdmin);
    }

    public function test_a_suspended_account_cannot_use_the_dashboard()
    {
        $user = User::factory()->suspended()->create();

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
        $this->assertGuest();
    }

    public function test_an_archived_account_cannot_use_the_dashboard()
    {
        $user = User::factory()->archived()->create();

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
        $this->assertGuest();
    }

    public function test_an_active_account_can_use_the_dashboard()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_an_invited_account_cannot_sign_in()
    {
        $user = User::factory()->invited()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_a_suspended_account_with_a_valid_password_is_blocked_from_the_dashboard()
    {
        $user = User::factory()->suspended()->create([
            'password' => Hash::make('Str0ng-Passw0rd!'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Str0ng-Passw0rd!',
        ]);

        $this->get('/dashboard')->assertForbidden();
        $this->assertGuest();
    }
}
