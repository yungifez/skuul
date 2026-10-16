<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Enums\AuditAction;
use App\Enums\OrganizationPermission;
use App\Livewire\OrganizationForm;
use App\Models\AuditEvent;
use App\Models\Organization;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Starting an organization, and changing what it is called.
 */
class OrganizationSettingsTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_platform_administrator_starts_an_organization_with_a_made_up_code(): void
    {
        $admin = User::factory()->platformAdmin()->create();

        $this->actingAs($admin)->get(route('organizations.create'))->assertOk()->assertSeeLivewire(OrganizationForm::class);

        Livewire::actingAs($admin)
            ->test(OrganizationForm::class)
            ->set('name', '  Lagos Schools Trust ')
            ->set('email', ' ')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('organizations.show', Organization::query()->firstWhere('name', 'Lagos Schools Trust')));

        $organization = Organization::query()->firstWhere('name', 'Lagos Schools Trust');
        $this->assertNotEmpty($organization->code);
        $this->assertNull($organization->email);
    }

    public function test_a_code_another_organization_uses_is_refused(): void
    {
        $admin = User::factory()->platformAdmin()->create();
        Organization::factory()->create(['code' => 'LST']);

        Livewire::actingAs($admin)
            ->test(OrganizationForm::class)
            ->set('name', 'Lagos Schools Trust')
            ->set('code', 'LST')
            ->call('save')
            ->assertHasErrors(['code' => 'unique'])
            ->set('code', 'has spaces')
            ->call('save')
            ->assertHasErrors(['code' => 'alpha_dash']);

        $this->assertFalse(Organization::query()->where('name', 'Lagos Schools Trust')->exists());
    }

    public function test_an_organization_manager_changes_the_settings_and_the_log_names_only_what_changed(): void
    {
        $organization = Organization::factory()->create(['name' => 'Old name', 'code' => 'OLD']);
        $manager = $this->organizationManager($organization);

        $this->actingAs($manager)->get(route('organizations.edit', $organization))->assertOk()->assertSeeLivewire(OrganizationForm::class);

        Livewire::actingAs($manager)
            ->test(OrganizationForm::class, ['organization' => $organization])
            ->assertSet('code', 'OLD')
            ->set('name', 'New name')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('status-message', type: 'success')
            ->set('code', '')
            ->call('save')
            ->assertHasErrors(['code' => 'required']);

        $this->assertSame('New name', $organization->fresh()->name);
        $this->assertSame(['name'], AuditEvent::ofAction(AuditAction::OrganizationUpdated)->sole()->context['changed']);
    }

    public function test_a_manager_of_another_organization_or_a_reader_cannot_change_it(): void
    {
        $organization = Organization::factory()->create(['name' => 'Kept']);
        $this->organizationManager($organization);
        $reader = $this->organizationManager($organization, [OrganizationPermission::ReadReports]);
        $outsider = $this->organizationManager(Organization::factory()->create());

        Livewire::actingAs($reader)->test(OrganizationForm::class, ['organization' => $organization])->assertForbidden();
        Livewire::actingAs($outsider)->test(OrganizationForm::class, ['organization' => $organization])->assertForbidden();
        Livewire::actingAs($outsider)->test(OrganizationForm::class)->assertForbidden();

        $this->assertSame('Kept', $organization->fresh()->name);
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
