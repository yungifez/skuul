<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Enums\OrganizationMembershipStatus;
use App\Enums\OrganizationPermission;
use App\Livewire\OrganizationMembers;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrganizationMembersScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_member_manager_can_open_the_members_screen(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->grantedMember($organization);
        $member = $this->grantedMember($organization);

        $this->actingAs($manager)
            ->get(route('organizations.members.index', $organization))
            ->assertOk()
            ->assertSee($member->name)
            ->assertSee($member->email);
    }

    public function test_a_member_without_member_management_cannot_open_the_screen(): void
    {
        $organization = Organization::factory()->create();
        $this->grantedMember($organization);
        $reader = $this->grantedMember($organization, [OrganizationPermission::ReadReports]);

        $this->actingAs($reader)
            ->get(route('organizations.members.index', $organization))
            ->assertForbidden();
    }

    public function test_an_administrator_of_another_organization_cannot_open_the_screen(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $outsider = $this->grantedMember($otherOrganization);

        $this->actingAs($outsider)
            ->get(route('organizations.members.index', $organization))
            ->assertForbidden();
    }

    public function test_granting_scope_by_email_adds_the_member(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->grantedMember($organization);
        $newcomer = $this->campusMemberOf($organization);

        Livewire::actingAs($manager)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->set('email', $newcomer->email)
            ->call('grant')
            ->assertHasNoErrors();

        $this->assertTrue($newcomer->fresh()->administersOrganization($organization));
    }

    public function test_granting_an_unknown_email_reports_an_error(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->grantedMember($organization);

        Livewire::actingAs($manager)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->set('email', 'nobody@gmail.com')
            ->call('grant')
            ->assertHasErrors('email');
    }

    public function test_granting_the_email_of_another_organizations_person_reads_as_unknown(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->grantedMember($organization);
        $stranger = $this->campusMemberOf(Organization::factory()->create());

        Livewire::actingAs($manager)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->set('email', $stranger->email)
            ->call('grant')
            ->assertHasErrors(['email' => 'Nobody at this organization uses that email address.'])
            ->assertDontSee($stranger->name);

        $this->assertFalse($stranger->fresh()->isKnownToOrganization($organization));
    }

    public function test_revoking_from_the_screen_keeps_campus_access(): void
    {
        $organization = Organization::factory()->create();
        $school = School::factory()->create(['organization_id' => $organization->id]);
        $manager = $this->grantedMember($organization);
        $member = $this->grantedMember($organization);
        $this->memberOf($school, $member);

        Livewire::actingAs($manager)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->call('revoke', $member->id)
            ->assertHasNoErrors();

        $this->assertFalse($member->fresh()->administersOrganization($organization));
        $this->assertTrue($member->fresh()->belongsToSchool($school));
    }

    public function test_the_screen_refuses_to_remove_the_last_member_manager(): void
    {
        $organization = Organization::factory()->create();
        $onlyManager = $this->grantedMember($organization);

        Livewire::actingAs($onlyManager)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->call('revoke', $onlyManager->id)
            ->assertHasErrors('members');

        $this->assertTrue($onlyManager->fresh()->administersOrganization($organization));
    }

    public function test_an_administrator_can_delegate_a_smaller_set_of_permissions(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->grantedMember($organization);
        $member = $this->grantedMember($organization);

        Livewire::actingAs($manager)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->call('edit', $member->id)
            ->assertSet('fullAuthority', true)
            ->set('fullAuthority', false)
            ->set('draftPermissions', [OrganizationPermission::ReadReports->value])
            ->call('savePermissions')
            ->assertHasNoErrors();

        $member = $member->fresh();

        $this->assertTrue($member->can('viewReports', $organization));
        $this->assertFalse($member->can('update', $organization));
        $this->assertFalse($member->can('manageMembers', $organization));
    }

    public function test_an_administrator_can_restore_full_authority(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->grantedMember($organization);
        $member = $this->grantedMember($organization, [OrganizationPermission::ReadReports]);

        Livewire::actingAs($manager)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->call('edit', $member->id)
            ->assertSet('fullAuthority', false)
            ->assertSet('draftPermissions', [OrganizationPermission::ReadReports->value])
            ->set('fullAuthority', true)
            ->call('savePermissions')
            ->assertHasNoErrors();

        $this->assertTrue($member->fresh()->can('update', $organization));
    }

    public function test_the_screen_shows_past_administrators(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->grantedMember($organization);
        $member = $this->grantedMember($organization);

        Livewire::actingAs($manager)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->call('revoke', $member->id)
            ->assertSee($member->name);

        $this->assertDatabaseHas('organization_memberships', [
            'organization_id' => $organization->id,
            'user_id' => $member->id,
            'status' => OrganizationMembershipStatus::Ended->value,
        ]);
    }

    public function test_the_screen_does_not_act_on_or_name_a_stranger(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->grantedMember($organization);
        $otherOrganization = Organization::factory()->create();
        $stranger = $this->grantedMember($otherOrganization);

        $screen = Livewire::actingAs($manager)
            ->test(OrganizationMembers::class, ['organization' => $organization]);

        $this->assertThrows(fn () => $screen->call('revoke', $stranger->id), ModelNotFoundException::class);
        $this->assertThrows(
            fn () => $screen->set('editingUserId', $stranger->id)->call('savePermissions'),
            ModelNotFoundException::class,
        );

        $this->assertTrue($stranger->fresh()->administersOrganization($otherOrganization));
    }

    public function test_a_past_administrator_cannot_be_edited_again(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->grantedMember($organization);
        $member = $this->grantedMember($organization);

        $screen = Livewire::actingAs($manager)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->call('edit', $member->id)
            ->call('revoke', $member->id);

        $this->assertThrows(
            fn () => $screen->set('editingUserId', $member->id)->call('savePermissions'),
            ModelNotFoundException::class,
        );
        $this->assertFalse($member->fresh()->administersOrganization($organization));
    }

    public function test_a_members_only_administrator_cannot_give_themself_more(): void
    {
        $organization = Organization::factory()->create();
        $this->grantedMember($organization);
        $membersOnly = $this->grantedMember($organization, [OrganizationPermission::ManageMembers]);

        Livewire::actingAs($membersOnly)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->call('edit', $membersOnly->id)
            ->set('fullAuthority', true)
            ->call('savePermissions')
            ->assertHasErrors('draftPermissions')
            ->set('fullAuthority', false)
            ->set('draftPermissions', [OrganizationPermission::ManageMembers->value, OrganizationPermission::MoveStudents->value])
            ->call('savePermissions')
            ->assertHasErrors('draftPermissions');

        $this->assertFalse($membersOnly->fresh()->can('update', $organization));
        $this->assertFalse($membersOnly->fresh()->can('manageCampuses', $organization));
    }

    public function test_an_administrator_cannot_take_away_a_permission_they_lack(): void
    {
        $organization = Organization::factory()->create();
        $membersOnly = $this->grantedMember($organization, [OrganizationPermission::ManageMembers]);
        $member = $this->grantedMember($organization, [OrganizationPermission::ManageCampuses]);

        Livewire::actingAs($membersOnly)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->call('edit', $member->id)
            ->set('draftPermissions', [])
            ->call('savePermissions')
            ->assertHasErrors('draftPermissions');

        $this->assertTrue($member->fresh()->can('manageCampuses', $organization));
    }

    public function test_a_members_only_administrator_cannot_remove_somebody_holding_more(): void
    {
        $organization = Organization::factory()->create();
        $owner = $this->grantedMember($organization);
        $membersOnly = $this->grantedMember($organization, [OrganizationPermission::ManageMembers]);
        $peer = $this->grantedMember($organization, [OrganizationPermission::ManageMembers]);

        $screen = Livewire::actingAs($membersOnly)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->call('revoke', $owner->id)
            ->assertHasErrors(['members' => 'This person holds more of the organization than you do, so only somebody holding as much can remove them.']);

        $this->assertTrue($owner->fresh()->administersOrganization($organization));

        $screen->call('revoke', $peer->id)->assertHasNoErrors(['members']);

        $this->assertFalse($peer->fresh()->administersOrganization($organization));
    }

    public function test_an_administrator_can_change_the_permissions_they_hold(): void
    {
        $organization = Organization::factory()->create();
        $delegator = $this->grantedMember($organization, [OrganizationPermission::ManageMembers, OrganizationPermission::ReadReports]);
        $member = $this->grantedMember($organization, [OrganizationPermission::ManageCampuses]);

        Livewire::actingAs($delegator)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->call('edit', $member->id)
            ->assertSeeHtml('value="'.OrganizationPermission::ReadReports->value.'"')
            ->set('draftPermissions', [OrganizationPermission::ManageCampuses->value, OrganizationPermission::ReadReports->value])
            ->call('savePermissions')
            ->assertHasNoErrors();

        $this->assertTrue($member->fresh()->can('viewReports', $organization));
        $this->assertTrue($member->fresh()->can('manageCampuses', $organization));
    }

    public function test_a_new_member_gets_only_what_the_granting_administrator_holds(): void
    {
        $organization = Organization::factory()->create();
        $this->grantedMember($organization);
        $membersOnly = $this->grantedMember($organization, [OrganizationPermission::ManageMembers]);
        $newcomer = $this->campusMemberOf($organization);

        Livewire::actingAs($membersOnly)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->set('email', $newcomer->email)
            ->call('grant')
            ->assertHasNoErrors();

        $newcomer = $newcomer->fresh();

        $this->assertTrue($newcomer->can('manageMembers', $organization));
        $this->assertFalse($newcomer->can('update', $organization));
        $this->assertFalse($newcomer->can('manageCampuses', $organization));
    }

    public function test_a_full_administrator_still_grants_full_authority(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->grantedMember($organization);
        $newcomer = $this->campusMemberOf($organization);

        Livewire::actingAs($manager)
            ->test(OrganizationMembers::class, ['organization' => $organization])
            ->set('email', $newcomer->email)
            ->call('grant');

        $this->assertTrue($newcomer->organizationMemberships()->firstOrFail()->hasFullAuthority());
    }

    /**
     * Create a person who works at a campus of the organization.
     */
    private function campusMemberOf(Organization $organization): User
    {
        return $this->memberOf(School::factory()->create(['organization_id' => $organization->id]));
    }

    /**
     * Give a person organization scope, delegated to the named permissions.
     *
     * @param  list<OrganizationPermission>|null  $permissions  null gives every permission
     */
    private function grantedMember(Organization $organization, ?array $permissions = null): User
    {
        $user = $this->nonMember();

        app(GrantOrganizationMembership::class)->grant($user, $organization);

        if ($permissions !== null) {
            app(SetOrganizationMemberPermissions::class)->set($user, $organization, $permissions);
        }

        return $user->refresh();
    }
}
