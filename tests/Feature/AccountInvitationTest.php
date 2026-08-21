<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\AccountInvitation;
use App\Models\School;
use App\Models\User;
use App\Notifications\AccountInvitationNotification;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class AccountInvitationTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    /**
     * Build an invited account with a known plain token.
     *
     * @return array{0: User, 1: string}
     */
    private function invitedUserWithToken(array $invitationAttributes = []): array
    {
        $token = Str::random(64);

        $user = User::factory()->invited()->create();

        AccountInvitation::factory()->create(array_merge([
            'user_id'    => $user->id,
            'token_hash' => AccountInvitation::hashToken($token),
        ], $invitationAttributes));

        return [$user, $token];
    }

    public function test_public_registration_route_no_longer_exists()
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
    }

    public function test_invitation_screen_can_be_rendered_with_a_pending_token()
    {
        [$user, $token] = $this->invitedUserWithToken();

        $this->get("/invitations/$token")
            ->assertOk()
            ->assertSee($user->email);
    }

    public function test_invitation_screen_rejects_an_unknown_token()
    {
        $this->get('/invitations/'.Str::random(64))->assertNotFound();
    }

    public function test_invitation_screen_rejects_an_expired_token()
    {
        [, $token] = $this->invitedUserWithToken(['expires_at' => now()->subHour()]);

        $this->get("/invitations/$token")->assertNotFound();
    }

    public function test_invitation_screen_rejects_a_revoked_token()
    {
        [, $token] = $this->invitedUserWithToken(['revoked_at' => now()]);

        $this->get("/invitations/$token")->assertNotFound();
    }

    public function test_invited_person_sets_a_password_and_becomes_active()
    {
        [$user, $token] = $this->invitedUserWithToken();

        $this->post("/invitations/$token", [
            'password'              => 'Str0ng-Passw0rd!',
            'password_confirmation' => 'Str0ng-Passw0rd!',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user->fresh());

        $user->refresh();

        $this->assertSame(AccountStatus::Active, $user->account_status);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotNull($user->accountInvitations()->first()->accepted_at);
    }

    public function test_an_invitation_can_only_be_used_once()
    {
        [, $token] = $this->invitedUserWithToken();

        $this->post("/invitations/$token", [
            'password'              => 'Str0ng-Passw0rd!',
            'password_confirmation' => 'Str0ng-Passw0rd!',
        ])->assertRedirect(route('dashboard'));

        $this->post('/logout');

        $this->post("/invitations/$token", [
            'password'              => 'An0ther-Passw0rd!',
            'password_confirmation' => 'An0ther-Passw0rd!',
        ])->assertSessionHasErrors('token');
    }

    public function test_an_expired_invitation_cannot_set_a_password()
    {
        [$user, $token] = $this->invitedUserWithToken(['expires_at' => now()->subHour()]);

        $this->post("/invitations/$token", [
            'password'              => 'Str0ng-Passw0rd!',
            'password_confirmation' => 'Str0ng-Passw0rd!',
        ])->assertSessionHasErrors('token');

        $this->assertGuest();
        $this->assertSame(AccountStatus::Invited, $user->fresh()->account_status);
    }

    public function test_a_weak_password_is_rejected()
    {
        [$user, $token] = $this->invitedUserWithToken();

        $this->post("/invitations/$token", [
            'password'              => 'abc',
            'password_confirmation' => 'abc',
        ])->assertSessionHasErrors('password');

        $this->assertNull($user->fresh()->password);
    }

    public function test_authorized_user_can_send_an_invitation()
    {
        Notification::fake();

        $target = User::factory()->invited()->create();

        $this->authorized_user(['manage account access'])
            ->post(route('users.invitation.send', $target->id))
            ->assertRedirect();

        Notification::assertSentTo($target, AccountInvitationNotification::class);
        $this->assertDatabaseCount('account_invitations', 1);
    }

    public function test_unauthorized_user_cannot_send_an_invitation()
    {
        Notification::fake();

        $target = User::factory()->invited()->create();

        $this->unauthorized_user()
            ->post(route('users.invitation.send', $target->id))
            ->assertForbidden();

        Notification::assertNothingSent();
    }

    public function test_an_administrator_cannot_manage_an_account_in_another_school()
    {
        Notification::fake();

        $otherSchool = School::factory()->create();
        $target = $this->memberOf($otherSchool, User::factory()->invited()->create());
        $target->schoolMemberships()->where('school_id', '!=', $otherSchool->id)->delete();

        $this->authorized_user(['manage account access'])
            ->post(route('users.invitation.send', $target->id))
            ->assertForbidden();

        Notification::assertNothingSent();
    }

    public function test_sending_a_new_invitation_revokes_the_previous_one()
    {
        Notification::fake();

        [$target, $oldToken] = $this->invitedUserWithToken();

        $this->authorized_user(['manage account access'])
            ->post(route('users.invitation.send', $target->id))
            ->assertRedirect();

        $this->post('/logout');

        $this->get("/invitations/$oldToken")->assertNotFound();
        $this->assertDatabaseCount('account_invitations', 2);
    }

    public function test_authorized_user_can_revoke_an_invitation()
    {
        [$target, $token] = $this->invitedUserWithToken();

        $this->authorized_user(['manage account access'])
            ->delete(route('users.invitation.revoke', $target->id))
            ->assertRedirect();

        $this->post('/logout');

        $this->get("/invitations/$token")->assertNotFound();
    }
}
