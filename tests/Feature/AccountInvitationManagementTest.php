<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Enums\AuditAction;
use App\Livewire\ListAccountInvitations;
use App\Models\AccountInvitation;
use App\Models\AuditEvent;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Notifications\AccountInvitationNotification;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The screen administrators use to follow and manage invitation links.
 *
 * The screen never shows an invitation from a school or an organization the
 * administrator is not responsible for, and it never lets an administrator
 * act on an invitation that is already finished.
 */
class AccountInvitationManagementTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_administrator_can_open_the_screen(): void
    {
        $invitee = $this->invitedMember();
        AccountInvitation::factory()->create(['user_id' => $invitee->id]);

        $this->authorized_user(['manage account access'])
            ->get(route('users.invitations.index'))
            ->assertOk()
            ->assertSee($invitee->name)
            ->assertSee($invitee->email);
    }

    public function test_a_user_without_permission_cannot_open_the_screen(): void
    {
        $this->unauthorized_user()
            ->get(route('users.invitations.index'))
            ->assertForbidden();
    }

    public function test_the_screen_shows_the_inviter_the_dates_and_the_school_membership(): void
    {
        $school = $this->workingSchool();
        $administrator = $this->schoolAdministrator($school);
        $invitee = $this->invitedMember($school);

        AccountInvitation::factory()->create([
            'user_id'    => $invitee->id,
            'invited_by' => $administrator->id,
        ]);

        Livewire::actingAs($administrator)
            ->test(ListAccountInvitations::class)
            ->assertSee($invitee->name)
            ->assertSee($invitee->email)
            ->assertSee($administrator->name)
            ->assertSee($school->name)
            ->assertSee('Pending');
    }

    public function test_each_tab_reads_only_the_invitations_in_that_state(): void
    {
        $pending = $this->invitedMember();
        $accepted = $this->invitedMember();
        $expired = $this->invitedMember();
        $revoked = $this->invitedMember();

        AccountInvitation::factory()->create(['user_id' => $pending->id]);
        AccountInvitation::factory()->accepted()->create(['user_id' => $accepted->id]);
        AccountInvitation::factory()->expired()->create(['user_id' => $expired->id]);
        AccountInvitation::factory()->revoked()->create(['user_id' => $revoked->id]);

        $component = Livewire::actingAs($this->schoolAdministrator())
            ->test(ListAccountInvitations::class);

        foreach ([
            'pending'  => $pending,
            'accepted' => $accepted,
            'expired'  => $expired,
            'revoked'  => $revoked,
        ] as $status => $shown) {
            $component->call('selectStatus', $status)
                ->assertSee($shown->email);

            foreach ([$pending, $accepted, $expired, $revoked] as $other) {
                if ($other->id !== $shown->id) {
                    $component->assertDontSee($other->email);
                }
            }
        }
    }

    public function test_an_administrator_can_resend_a_pending_invitation(): void
    {
        Notification::fake();

        $administrator = $this->schoolAdministrator();
        $invitee = $this->invitedMember();
        $invitation = AccountInvitation::factory()->create(['user_id' => $invitee->id]);

        Livewire::actingAs($administrator)
            ->test(ListAccountInvitations::class)
            ->call('resend', $invitation->id)
            ->assertHasNoErrors();

        Notification::assertSentTo($invitee, AccountInvitationNotification::class);

        $this->assertNotNull($invitation->fresh()->revoked_at, 'The link that came before must stop working.');
        $this->assertSame(2, $invitee->accountInvitations()->count());
        $this->assertNotNull($invitee->pendingAccountInvitation());
    }

    public function test_resending_is_written_to_the_audit_log(): void
    {
        Notification::fake();

        $administrator = $this->schoolAdministrator();
        $invitee = $this->invitedMember();
        $invitation = AccountInvitation::factory()->create(['user_id' => $invitee->id]);

        Livewire::actingAs($administrator)
            ->test(ListAccountInvitations::class)
            ->call('resend', $invitation->id);

        $event = AuditEvent::ofAction(AuditAction::AccountInvitationSent)->latest('id')->firstOrFail();

        $this->assertSame($administrator->id, $event->actor_id);
        $this->assertSame($invitee->email, $event->context['email']);
    }

    public function test_an_administrator_can_revoke_a_pending_invitation(): void
    {
        $administrator = $this->schoolAdministrator();
        $invitee = $this->invitedMember();
        $invitation = AccountInvitation::factory()->create(['user_id' => $invitee->id]);

        Livewire::actingAs($administrator)
            ->test(ListAccountInvitations::class)
            ->call('revoke', $invitation->id)
            ->assertHasNoErrors();

        $this->assertNotNull($invitation->fresh()->revoked_at);
        $this->assertNull($invitee->pendingAccountInvitation());

        $event = AuditEvent::ofAction(AuditAction::AccountInvitationRevoked)->latest('id')->firstOrFail();
        $this->assertSame($administrator->id, $event->actor_id);
    }

    public function test_an_accepted_invitation_cannot_be_resent_or_revoked(): void
    {
        $administrator = $this->schoolAdministrator();
        $invitee = $this->invitedMember();
        $invitation = AccountInvitation::factory()->accepted()->create(['user_id' => $invitee->id]);

        Livewire::actingAs($administrator)
            ->test(ListAccountInvitations::class)
            ->call('resend', $invitation->id)
            ->assertForbidden();

        Livewire::actingAs($administrator)
            ->test(ListAccountInvitations::class)
            ->call('revoke', $invitation->id)
            ->assertForbidden();
    }

    public function test_the_screen_explains_why_an_accepted_invitation_has_no_actions(): void
    {
        $administrator = $this->schoolAdministrator();
        $invitee = $this->invitedMember();
        AccountInvitation::factory()->accepted()->create(['user_id' => $invitee->id]);

        Livewire::actingAs($administrator)
            ->test(ListAccountInvitations::class)
            ->call('selectStatus', 'accepted')
            ->assertSee('This person already set a password. Change access from their profile instead.');
    }

    public function test_an_expired_invitation_cannot_be_resent(): void
    {
        Notification::fake();

        $administrator = $this->schoolAdministrator();
        $invitee = $this->invitedMember();
        $invitation = AccountInvitation::factory()->expired()->create(['user_id' => $invitee->id]);

        Livewire::actingAs($administrator)
            ->test(ListAccountInvitations::class)
            ->call('selectStatus', 'expired')
            ->assertSee('This link passed its expiry time. Send a new invitation from the person')
            ->call('resend', $invitation->id)
            ->assertForbidden();

        Notification::assertNothingSent();
    }

    public function test_a_revoked_invitation_cannot_be_revoked_again(): void
    {
        $administrator = $this->schoolAdministrator();
        $invitee = $this->invitedMember();
        $invitation = AccountInvitation::factory()->revoked()->create(['user_id' => $invitee->id]);

        Livewire::actingAs($administrator)
            ->test(ListAccountInvitations::class)
            ->call('revoke', $invitation->id)
            ->assertForbidden();
    }

    public function test_an_administrator_cannot_manage_their_own_invitation(): void
    {
        $administrator = $this->schoolAdministrator();
        $invitation = AccountInvitation::factory()->create(['user_id' => $administrator->id]);

        Livewire::actingAs($administrator)
            ->test(ListAccountInvitations::class)
            ->assertSee('You cannot resend or revoke your own invitation. Ask another administrator.')
            ->call('resend', $invitation->id)
            ->assertForbidden();
    }

    public function test_an_invitation_from_another_school_is_hidden_and_cannot_be_managed(): void
    {
        $otherSchool = School::factory()->create();
        $outsider = $this->invitedMember($otherSchool);
        $outsider->schoolMemberships()->where('school_id', '!=', $otherSchool->id)->delete();

        $invitation = AccountInvitation::factory()->create(['user_id' => $outsider->id]);

        Livewire::actingAs($this->schoolAdministrator())
            ->test(ListAccountInvitations::class)
            ->assertDontSee($outsider->email)
            ->call('revoke', $invitation->id)
            ->assertForbidden();
    }

    public function test_another_school_of_the_invited_person_is_not_named(): void
    {
        $otherSchool = School::factory()->create(['name' => 'Far Away Campus']);
        $invitee = $this->invitedMember();
        $this->memberOf($otherSchool, $invitee);

        AccountInvitation::factory()->create(['user_id' => $invitee->id]);

        Livewire::actingAs($this->schoolAdministrator())
            ->test(ListAccountInvitations::class)
            ->assertSee($invitee->email)
            ->assertDontSee('Far Away Campus');
    }

    public function test_a_platform_administrator_reads_every_school(): void
    {
        $otherSchool = School::factory()->create();
        $outsider = $this->invitedMember($otherSchool);
        AccountInvitation::factory()->create(['user_id' => $outsider->id]);

        $platformAdministrator = User::factory()->platformAdmin()->create();

        Livewire::actingAs($platformAdministrator)
            ->test(ListAccountInvitations::class)
            ->assertSee($outsider->email);
    }

    public function test_an_organization_administrator_reads_the_campuses_of_that_organization(): void
    {
        $organization = Organization::factory()->create();
        $campus = School::factory()->create(['organization_id' => $organization->id]);
        $invitee = $this->invitedMember($campus);
        AccountInvitation::factory()->create(['user_id' => $invitee->id]);

        Livewire::actingAs($this->organizationAdministrator($organization))
            ->test(ListAccountInvitations::class)
            ->assertSee($invitee->email);
    }

    public function test_an_organization_administrator_cannot_reach_another_organization(): void
    {
        $organization = Organization::factory()->create();
        $ownCampus = School::factory()->create(['organization_id' => $organization->id]);
        $insider = $this->invitedMember($ownCampus);
        AccountInvitation::factory()->create(['user_id' => $insider->id]);

        $otherOrganization = Organization::factory()->create();
        $otherCampus = School::factory()->create(['organization_id' => $otherOrganization->id]);

        $outsider = $this->invitedMember($otherCampus);
        $outsider->schoolMemberships()->where('school_id', '!=', $otherCampus->id)->delete();
        $invitation = AccountInvitation::factory()->create(['user_id' => $outsider->id]);

        Livewire::actingAs($this->organizationAdministrator($organization))
            ->test(ListAccountInvitations::class)
            ->assertSee($insider->email)
            ->assertDontSee($outsider->email)
            ->call('resend', $invitation->id)
            ->assertForbidden();
    }

    /**
     * Create a person who may manage account access in the given school.
     */
    private function schoolAdministrator(?School $school = null): User
    {
        $school = $this->workingSchool($school);
        $administrator = $this->memberOf($school);

        // Permissions are school-scoped, so name the school before granting.
        school_context()->set($school, remember: false);
        $administrator->givePermissionTo('manage account access');

        return $administrator->refresh();
    }

    /**
     * Create a person who administers one organization and no school.
     */
    private function organizationAdministrator(Organization $organization): User
    {
        $administrator = $this->nonMember();

        app(GrantOrganizationMembership::class)->grant($administrator, $organization);

        return $administrator->refresh();
    }

    /**
     * Create an invited person who can work in the given school.
     */
    private function invitedMember(?School $school = null): User
    {
        return $this->memberOf(
            $this->workingSchool($school),
            User::factory()->invited()->create(),
        );
    }
}
