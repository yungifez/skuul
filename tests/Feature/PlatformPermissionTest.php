<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Enums\PlatformPermission;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\SystemPermissionScope;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PlatformPermissionTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_platform_authority_is_a_global_spatie_role_with_explicit_permissions(): void
    {
        $user = User::factory()->platformAdmin()->create();

        $this->assertTrue(app(SystemPermissionScope::class)->within(
            fn (): bool => $user->fresh()->hasRole(Role::PlatformAdmin)
        ));
        $this->assertTrue($user->fresh()->can(PlatformPermission::AccessAllSchools));
        $this->assertTrue($user->fresh()->can(PlatformPermission::ManagePlatform));
        $this->assertFalse(Schema::hasColumn('users', 'is_platform_admin'));
    }

    public function test_platform_permission_applies_without_changing_the_active_school(): void
    {
        $firstSchool = School::factory()->create();
        $secondSchool = School::factory()->create();
        $user = User::factory()->platformAdmin()->create();

        school_context()->set($firstSchool, remember: false);
        $this->assertTrue($user->can('read school'));

        school_context()->set($secondSchool, remember: false);
        $this->assertTrue($user->can('read school'));
    }

    public function test_platform_administrator_can_render_the_dashboard(): void
    {
        $school = School::factory()->create();
        $user = $this->memberOf($school, User::factory()->platformAdmin()->create());

        $this->actingAsMemberOf($school, $user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_dashboard_loads_school_role_memberships_once(): void
    {
        $school = School::factory()->create();
        $user = $this->memberOf($school);

        DB::flushQueryLog();
        DB::enableQueryLog();

        try {
            $this->actingAsMemberOf($school, $user)
                ->get(route('dashboard'))
                ->assertOk();

            $schoolRoleQueries = collect(DB::getQueryLog())->filter(function (array $query) use ($school): bool {
                return str_starts_with($query['query'], 'select `roles`.*, `model_has_roles`.`model_id`')
                    && in_array($school->id, $query['bindings'], true);
            });
            $schoolPermissionQueries = collect(DB::getQueryLog())->filter(function (array $query) use ($school): bool {
                return str_starts_with($query['query'], 'select `permissions`.*, `model_has_permissions`.`model_id`')
                    && in_array($school->id, $query['bindings'], true);
            });

            $this->assertCount(1, $schoolRoleQueries);
            $this->assertCount(1, $schoolPermissionQueries);
        } finally {
            DB::disableQueryLog();
        }
    }

    public function test_organization_membership_requires_its_spatie_role_for_authority(): void
    {
        $organization = Organization::factory()->create();
        $user = $this->nonMember();
        $user->organizationMemberships()->create([
            'organization_id' => $organization->id,
            'status'          => 'active',
            'joined_at'       => now(),
        ]);

        $this->assertFalse($user->fresh()->can('update', $organization));

        app(GrantOrganizationMembership::class)->grant($user, $organization);

        $this->assertTrue($user->fresh()->can('update', $organization));
    }
}
