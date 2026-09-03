<?php

namespace Tests\Feature;

use App\Actions\Boarding\AttachDormitoryToBoardingResidence;
use App\Actions\Boarding\LinkSchoolToBoardingResidence;
use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Enums\OrganizationPermission;
use App\Exceptions\InvalidValueException;
use App\Models\BoardingResidence;
use App\Models\Dormitory;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardingResidenceTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_organization_manager_can_configure_a_residence_for_two_campuses(): void
    {
        $organization = Organization::factory()->create();
        $firstCampus = School::factory()->create(['organization_id' => $organization->id]);
        $secondCampus = School::factory()->create(['organization_id' => $organization->id]);
        $firstHouse = Dormitory::factory()->create(['school_id' => $firstCampus->id]);
        $secondHouse = Dormitory::factory()->create(['school_id' => $secondCampus->id]);
        $manager = $this->organizationManager($organization);

        $this->actingAs($manager)
            ->post(route('organizations.boarding-residences.store', $organization), [
                'name' => 'Central residence',
                'notes' => 'Shared by both campuses',
            ])
            ->assertRedirect();

        $residence = BoardingResidence::firstWhere('name', 'Central residence');
        $this->assertNotNull($residence);

        $this->actingAs($manager)
            ->post(route('organizations.boarding-residences.schools.store', [$organization, $residence]), [
                'school_id' => $firstCampus->id,
            ])
            ->assertRedirect();
        $this->actingAs($manager)
            ->post(route('organizations.boarding-residences.schools.store', [$organization, $residence]), [
                'school_id' => $secondCampus->id,
            ])
            ->assertRedirect();

        $this->actingAs($manager)
            ->post(route('organizations.boarding-residences.houses.store', [$organization, $residence]), [
                'dormitory_id' => $firstHouse->id,
            ])
            ->assertRedirect();
        $this->actingAs($manager)
            ->post(route('organizations.boarding-residences.houses.store', [$organization, $residence]), [
                'dormitory_id' => $secondHouse->id,
            ])
            ->assertRedirect();

        $this->assertTrue($residence->fresh()->schools()->whereKey($firstCampus)->exists());
        $this->assertTrue($residence->fresh()->schools()->whereKey($secondCampus)->exists());
        $this->assertSame($residence->id, $firstHouse->fresh()->boarding_residence_id);
        $this->assertSame($residence->id, $secondHouse->fresh()->boarding_residence_id);

        $this->actingAs($manager)
            ->get(route('organizations.boarding-residences.index', $organization))
            ->assertOk()
            ->assertSee('Central residence')
            ->assertSee($firstCampus->name)
            ->assertSee($secondHouse->name);
    }

    public function test_a_campus_from_another_organization_cannot_be_linked(): void
    {
        $organization = Organization::factory()->create();
        $residence = BoardingResidence::factory()->create(['organization_id' => $organization->id]);
        $elsewhere = School::factory()->create();
        $manager = $this->organizationManager($organization);

        $this->actingAs($manager)
            ->post(route('organizations.boarding-residences.schools.store', [$organization, $residence]), [
                'school_id' => $elsewhere->id,
            ])
            ->assertSessionHasErrors('school_id');

        $this->assertFalse($residence->schools()->whereKey($elsewhere)->exists());
    }

    public function test_a_house_cannot_be_attached_until_its_campus_is_linked(): void
    {
        $organization = Organization::factory()->create();
        $campus = School::factory()->create(['organization_id' => $organization->id]);
        $residence = BoardingResidence::factory()->create(['organization_id' => $organization->id]);
        $house = Dormitory::factory()->create(['school_id' => $campus->id]);

        $this->expectException(InvalidValueException::class);

        app(AttachDormitoryToBoardingResidence::class)->attach($residence, $house);
    }

    public function test_a_campus_cannot_be_unlinked_while_one_of_its_houses_is_attached(): void
    {
        $organization = Organization::factory()->create();
        $campus = School::factory()->create(['organization_id' => $organization->id]);
        $residence = BoardingResidence::factory()->create(['organization_id' => $organization->id]);
        $house = Dormitory::factory()->create([
            'school_id' => $campus->id,
            'boarding_residence_id' => $residence->id,
        ]);
        $residence->schools()->attach($campus->id);

        $this->expectException(InvalidValueException::class);

        app(LinkSchoolToBoardingResidence::class)->unlink($residence, $campus);
    }

    public function test_shared_residence_configuration_requires_organization_campus_permission(): void
    {
        $organization = Organization::factory()->create();
        $this->organizationManager($organization);
        $reader = $this->organizationManager($organization, [OrganizationPermission::ReadReports]);

        $this->actingAs($reader)
            ->get(route('organizations.boarding-residences.index', $organization))
            ->assertForbidden();
    }

    /**
     * Give a person organization scope, delegated to the named permissions.
     *
     * @param  list<OrganizationPermission>|null  $permissions  null gives every permission
     */
    private function organizationManager(Organization $organization, ?array $permissions = null): User
    {
        $user = $this->nonMember();

        app(GrantOrganizationMembership::class)->grant($user, $organization);

        if ($permissions !== null) {
            app(SetOrganizationMemberPermissions::class)->set($user, $organization, $permissions);
        }

        return $user->refresh();
    }
}
