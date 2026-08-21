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
        $newcomer = User::factory()->create();

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
