<?php

namespace Tests\Feature;

use App\Actions\Organization\AssignSchoolToOrganization;
use App\Actions\Organization\GrantOrganizationMembership;
use App\Models\BillingGroup;
use App\Models\BoardingResidence;
use App\Models\CalendarTemplate;
use App\Models\Dormitory;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationHierarchyTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_organization_owns_multiple_campuses(): void
    {
        $organization = Organization::factory()->create();
        $firstSchool = School::factory()->create(['organization_id' => $organization->id]);
        $secondSchool = School::factory()->create(['organization_id' => $organization->id]);

        $this->assertTrue($organization->schools->contains($firstSchool));
        $this->assertTrue($organization->schools->contains($secondSchool));
    }

    public function test_organization_administration_does_not_grant_school_access(): void
    {
        $organization = Organization::factory()->create();
        $school = School::factory()->create(['organization_id' => $organization->id]);
        $user = $this->nonMember();

        app(GrantOrganizationMembership::class)->grant($user, $organization);

        $this->assertTrue($user->fresh()->administersOrganization($organization));
        $this->assertFalse($user->fresh()->belongsToSchool($school));
    }

    public function test_an_organization_administrator_can_only_manage_their_organization(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $user = $this->nonMember();

        app(GrantOrganizationMembership::class)->grant($user, $organization);

        $this->assertTrue($user->fresh()->can('view', $organization));
        $this->assertFalse($user->fresh()->can('view', $otherOrganization));
    }

    public function test_an_organization_administrator_can_open_the_organization_screen_without_school_access(): void
    {
        $organization = Organization::factory()->create();
        $user = $this->nonMember();
        app(GrantOrganizationMembership::class)->grant($user, $organization);

        $this->actingAs($user)
            ->get(route('organizations.show', $organization))
            ->assertOk()
            ->assertSee($organization->name);
    }

    public function test_assigning_a_campus_to_an_organization_preserves_school_memberships(): void
    {
        $organization = Organization::factory()->create();
        $school = School::factory()->create();
        $user = User::factory()->create();
        $this->memberOf($school, $user);

        app(AssignSchoolToOrganization::class)->assign($school, $organization, $user);

        $this->assertSame($organization->id, $school->fresh()->organization_id);
        $this->assertTrue($user->fresh()->belongsToSchool($school));
    }

    public function test_a_campus_that_changes_organization_leaves_the_old_purse_calendar_and_residence(): void
    {
        $previous = Organization::factory()->create();
        $group = BillingGroup::factory()->create(['organization_id' => $previous->id]);
        $template = CalendarTemplate::factory()->create(['organization_id' => $previous->id]);
        $school = School::factory()->create([
            'organization_id' => $previous->id,
            'billing_group_id' => $group->id,
            'calendar_template_id' => $template->id,
        ]);
        $sister = School::factory()->create(['organization_id' => $previous->id, 'billing_group_id' => $group->id]);
        $residence = BoardingResidence::factory()->create(['organization_id' => $previous->id]);
        $residence->schools()->attach($school->id);
        $house = Dormitory::factory()->create(['school_id' => $school->id, 'boarding_residence_id' => $residence->id]);

        app(AssignSchoolToOrganization::class)->assign($school, Organization::factory()->create());

        $school->refresh();
        $this->assertNull($school->billing_group_id);
        $this->assertNull($school->calendar_template_id);
        $this->assertFalse($school->billsWith($sister->fresh()));
        $this->assertFalse($group->schools()->whereKey($school->id)->exists());
        $this->assertFalse($residence->schools()->whereKey($school->id)->exists());
        $this->assertNull($house->fresh()->boarding_residence_id);
    }

    public function test_a_platform_administrator_can_manage_any_organization(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->platformAdmin()->create();

        $this->assertTrue($user->can('update', $organization));
    }
}
