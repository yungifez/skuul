<?php

namespace Tests\Feature;

use App\Actions\Boarding\AttachDormitoryToBoardingResidence;
use App\Actions\Boarding\LinkSchoolToBoardingResidence;
use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Enums\OrganizationPermission;
use App\Exceptions\InvalidValueException;
use App\Livewire\OrganizationBoardingResidences;
use App\Models\BoardingResidence;
use App\Models\Dormitory;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

        $component = Livewire::actingAs($manager)
            ->test(OrganizationBoardingResidences::class, ['organization' => $organization])
            ->set('name', ' Central residence ')
            ->set('notes', 'Shared by both campuses')
            ->call('createResidence')
            ->assertHasNoErrors()
            ->assertSet('name', '');

        $residence = BoardingResidence::firstWhere('name', 'Central residence');
        $this->assertNotNull($residence);

        $component->set('name', 'central RESIDENCE')
            ->call('createResidence')
            ->assertHasErrors('name')
            ->assertSee('This organization already has a residence with that name.');

        $component->call('linkCampus', $residence->id, (string) $firstCampus->id)
            ->call('linkCampus', $residence->id, (string) $secondCampus->id)
            ->call('attachHouse', $residence->id, (string) $firstHouse->id)
            ->call('attachHouse', $residence->id, (string) $secondHouse->id);
        $component->assertDispatched('status-message', type: 'success');

        $this->assertTrue($residence->fresh()->schools()->whereKey($firstCampus)->exists());
        $this->assertTrue($residence->fresh()->schools()->whereKey($secondCampus)->exists());
        $this->assertSame($residence->id, $firstHouse->fresh()->boarding_residence_id);
        $this->assertSame($residence->id, $secondHouse->fresh()->boarding_residence_id);

        $component->call('detachHouse', $residence->id, $firstHouse->id)
            ->call('unlinkCampus', $residence->id, $firstCampus->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertNull($firstHouse->fresh()->boarding_residence_id);
        $this->assertFalse($residence->fresh()->schools()->whereKey($firstCampus)->exists());

        $this->actingAs($manager)
            ->get(route('organizations.boarding-residences.index', $organization))
            ->assertOk()
            ->assertSeeLivewire(OrganizationBoardingResidences::class)
            ->assertSee('Central residence')
            ->assertSee($secondCampus->name)
            ->assertSee($secondHouse->name);
    }

    public function test_a_campus_from_another_organization_cannot_be_linked(): void
    {
        $organization = Organization::factory()->create();
        $residence = BoardingResidence::factory()->create(['organization_id' => $organization->id]);
        $elsewhere = School::factory()->create();
        $theirHouse = Dormitory::factory()->create(['school_id' => $elsewhere->id]);
        $theirResidence = BoardingResidence::factory()->create();
        $manager = $this->organizationManager($organization);

        $component = Livewire::actingAs($manager)->test(OrganizationBoardingResidences::class, ['organization' => $organization])
            ->assertDontSee($theirResidence->name)
            ->assertDontSee($theirHouse->name);

        $this->assertThrows(fn () => $component->call('linkCampus', $residence->id, (string) $elsewhere->id), ModelNotFoundException::class);
        $this->assertThrows(fn () => $component->call('attachHouse', $residence->id, (string) $theirHouse->id), ModelNotFoundException::class);
        $this->assertThrows(fn () => $component->call('unlinkCampus', $theirResidence->id, $elsewhere->id), ModelNotFoundException::class);

        $this->assertFalse($residence->schools()->whereKey($elsewhere)->exists());
        $this->assertNull($theirHouse->fresh()->boarding_residence_id);
    }

    public function test_a_house_of_a_campus_another_manager_just_unlinked_is_refused(): void
    {
        $organization = Organization::factory()->create();
        $campus = School::factory()->create(['organization_id' => $organization->id]);
        $house = Dormitory::factory()->create(['school_id' => $campus->id]);
        $residence = BoardingResidence::factory()->create(['organization_id' => $organization->id]);
        $residence->schools()->attach($campus->id);
        $manager = $this->organizationManager($organization);

        $stale = Livewire::actingAs($manager)->test(OrganizationBoardingResidences::class, ['organization' => $organization]);

        app(LinkSchoolToBoardingResidence::class)->unlink($residence, $campus);

        $stale->call('attachHouse', $residence->id, (string) $house->id)
            ->assertDispatched('status-message', type: 'danger', message: 'Link the campus to this residence before adding its house.');

        $this->assertNull($house->fresh()->boarding_residence_id);
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

        Livewire::actingAs($reader)->test(OrganizationBoardingResidences::class, ['organization' => $organization])->assertForbidden();
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
