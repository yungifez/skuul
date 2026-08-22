<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\RevokeOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Enums\OrganizationMembershipStatus;
use App\Enums\OrganizationPermission;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\SystemPermissionScope;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationAccessTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_revoking_organization_scope_keeps_school_access(): void
    {
        $organization = Organization::factory()->create();
        $school = School::factory()->create(['organization_id' => $organization->id]);
        $user = $this->memberOf($school);
        $manager = $this->grantedMember($organization);

        app(GrantOrganizationMembership::class)->grant($user, $organization);
        app(RevokeOrganizationMembership::class)->revoke($user, $organization, $manager);

        $this->assertFalse($user->fresh()->administersOrganization($organization));
        $this->assertTrue($user->fresh()->belongsToSchool($school));
    }

    public function test_revoking_one_scope_keeps_organization_admin_for_another_organization(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $user = $this->nonMember();
        $manager = $this->grantedMember($organization);

        app(GrantOrganizationMembership::class)->grant($user, $organization);
        app(GrantOrganizationMembership::class)->grant($user, $otherOrganization);

        app(RevokeOrganizationMembership::class)->revoke($user, $organization, $manager);

        $this->assertFalse($user->fresh()->administersOrganization($organization));
        $this->assertTrue($user->fresh()->administersOrganization($otherOrganization));
        $this->assertTrue($this->hasOrganizationAdminRole($user->fresh()));
        $this->assertTrue($user->fresh()->can('view', $otherOrganization));
    }

    public function test_revoking_the_last_scope_removes_the_organization_admin_role(): void
    {
        $organization = Organization::factory()->create();
        $user = $this->nonMember();
        $this->grantedMember($organization);

        app(GrantOrganizationMembership::class)->grant($user, $organization);
        app(RevokeOrganizationMembership::class)->revoke($user, $organization);

        $this->assertFalse($this->hasOrganizationAdminRole($user->fresh()));
        $this->assertFalse($user->fresh()->can('view', $organization));
    }

    public function test_revoking_records_an_ended_membership_rather_than_deleting_it(): void
    {
        $organization = Organization::factory()->create();
        $user = $this->nonMember();
        $this->grantedMember($organization);

        app(GrantOrganizationMembership::class)->grant($user, $organization);
        $membership = app(RevokeOrganizationMembership::class)->revoke($user, $organization);

        $this->assertSame(OrganizationMembershipStatus::Ended, $membership->status);
        $this->assertNotNull($membership->ended_at);
        $this->assertDatabaseHas('organization_memberships', [
            'organization_id' => $organization->id,
            'user_id'         => $user->id,
            'status'          => OrganizationMembershipStatus::Ended->value,
        ]);
    }

    public function test_the_last_member_who_manages_members_cannot_be_revoked(): void
    {
        $organization = Organization::factory()->create();
        $onlyManager = $this->grantedMember($organization);

        $this->expectException(InvalidValueException::class);

        app(RevokeOrganizationMembership::class)->revoke($onlyManager, $organization);
    }

    public function test_a_replacement_lets_the_last_manager_step_down(): void
    {
        $organization = Organization::factory()->create();
        $first = $this->grantedMember($organization);
        $second = $this->grantedMember($organization);

        app(RevokeOrganizationMembership::class)->revoke($first, $organization, $second);

        $this->assertFalse($first->fresh()->administersOrganization($organization));
        $this->assertTrue($second->fresh()->administersOrganization($organization));
    }

    public function test_a_delegated_member_only_holds_the_permissions_given(): void
    {
        $organization = Organization::factory()->create();
        $this->grantedMember($organization);
        $reader = $this->grantedMember($organization, [OrganizationPermission::ReadReports]);

        $reader = $reader->fresh();

        $this->assertTrue($reader->can('view', $organization));
        $this->assertTrue($reader->can('viewReports', $organization));
        $this->assertFalse($reader->can('update', $organization));
        $this->assertFalse($reader->can('manageMembers', $organization));
        $this->assertFalse($reader->can('manageCampuses', $organization));
    }

    public function test_delegation_never_reaches_another_organization(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $this->grantedMember($organization);
        $member = $this->grantedMember($organization, [OrganizationPermission::Manage]);

        $member = $member->fresh();

        $this->assertTrue($member->can('update', $organization));
        $this->assertFalse($member->can('view', $otherOrganization));
        $this->assertFalse($member->can('update', $otherOrganization));
    }

    public function test_the_last_manager_cannot_delegate_member_management_away(): void
    {
        $organization = Organization::factory()->create();
        $onlyManager = $this->grantedMember($organization);

        $this->expectException(InvalidValueException::class);

        app(SetOrganizationMemberPermissions::class)->set(
            $onlyManager,
            $organization,
            [OrganizationPermission::ReadReports],
        );
    }

    public function test_a_reactivated_membership_keeps_its_delegated_permissions(): void
    {
        $organization = Organization::factory()->create();
        $this->grantedMember($organization);
        $member = $this->grantedMember($organization, [OrganizationPermission::ReadReports]);

        app(RevokeOrganizationMembership::class)->revoke($member, $organization);
        app(GrantOrganizationMembership::class)->grant($member, $organization);

        $membership = $member->fresh()->organizationMemberships()->active()->first();

        $this->assertFalse($membership->hasFullAuthority());
        $this->assertFalse($member->fresh()->can('update', $organization));
        $this->assertTrue($member->fresh()->can('viewReports', $organization));
    }

    public function test_an_ended_membership_grants_nothing(): void
    {
        $organization = Organization::factory()->create();
        $this->grantedMember($organization);
        $member = $this->grantedMember($organization);

        app(RevokeOrganizationMembership::class)->revoke($member, $organization);

        $this->assertFalse($member->fresh()->can('view', $organization));
        $this->assertFalse($member->fresh()->can('manageMembers', $organization));
    }

    public function test_a_platform_administrator_holds_every_organization_permission(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->platformAdmin()->create();

        $this->assertTrue($user->can('manageMembers', $organization));
        $this->assertTrue($user->can('manageCampuses', $organization));
        $this->assertTrue($user->can('viewReports', $organization));
    }

    /**
     * Give a person organization scope, delegated to the named permissions.
     *
     * @param list<OrganizationPermission>|null $permissions null gives every permission
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

    private function hasOrganizationAdminRole(User $user): bool
    {
        return app(SystemPermissionScope::class)->withinUserScope(
            $user,
            fn (): bool => $user->hasRole(Role::OrganizationAdmin),
        );
    }
}
