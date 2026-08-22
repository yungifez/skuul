<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Enums\EnrollmentStatus;
use App\Enums\OrganizationPermission;
use App\Livewire\OrganizationDashboard;
use App\Models\Organization;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Livewire\Livewire;
use Tests\TestCase;

class OrganizationDashboardTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_overview_counts_campuses_enrollments_and_campus_access(): void
    {
        $organization = Organization::factory()->create();
        $firstCampus = School::factory()->create(['organization_id' => $organization->id]);
        $secondCampus = School::factory()->create(['organization_id' => $organization->id]);

        $this->enroll($firstCampus, 2);
        $this->enroll($secondCampus, 1);
        $this->memberOf($firstCampus);

        $manager = $this->grantedMember($organization);

        Livewire::actingAs($manager)
            ->test(OrganizationDashboard::class, ['organization' => $organization])
            ->call('loadDashboard')
            ->assertSet('campusCount', 2)
            ->assertSet('activeStudents', 3)
            ->assertSet('loaded', true);
    }

    public function test_the_overview_ignores_campuses_of_another_organization(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $campus = School::factory()->create(['organization_id' => $organization->id]);
        $foreignCampus = School::factory()->create(['organization_id' => $otherOrganization->id]);

        $this->enroll($campus, 1);
        $this->enroll($foreignCampus, 5);

        $manager = $this->grantedMember($organization);

        Livewire::actingAs($manager)
            ->test(OrganizationDashboard::class, ['organization' => $organization])
            ->call('loadDashboard')
            ->assertSet('campusCount', 1)
            ->assertSet('activeStudents', 1);
    }

    public function test_a_closed_enrollment_is_not_counted_as_attending(): void
    {
        $organization = Organization::factory()->create();
        $campus = School::factory()->create(['organization_id' => $organization->id]);

        $this->enroll($campus, 1);
        $this->enroll($campus, 1, EnrollmentStatus::Graduated);

        $manager = $this->grantedMember($organization);

        Livewire::actingAs($manager)
            ->test(OrganizationDashboard::class, ['organization' => $organization])
            ->call('loadDashboard')
            ->assertSet('activeStudents', 1);
    }

    public function test_a_member_delegated_reports_can_open_the_overview(): void
    {
        $organization = Organization::factory()->create();
        $this->grantedMember($organization);
        $reader = $this->grantedMember($organization, [OrganizationPermission::ReadReports]);

        $this->actingAs($reader)
            ->get(route('organizations.dashboard', $organization))
            ->assertOk();
    }

    public function test_a_member_without_the_reports_permission_cannot_open_the_overview(): void
    {
        $organization = Organization::factory()->create();
        $this->grantedMember($organization);
        $member = $this->grantedMember($organization, [OrganizationPermission::Manage]);

        $this->actingAs($member)
            ->get(route('organizations.dashboard', $organization))
            ->assertForbidden();
    }

    public function test_an_administrator_of_another_organization_cannot_open_the_overview(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $outsider = $this->grantedMember($otherOrganization);

        $this->actingAs($outsider)
            ->get(route('organizations.dashboard', $organization))
            ->assertForbidden();
    }

    public function test_the_overview_shows_totals_without_naming_a_student(): void
    {
        $organization = Organization::factory()->create();
        $campus = School::factory()->create(['organization_id' => $organization->id]);
        $student = $this->enroll($campus, 1)->first();

        $manager = $this->grantedMember($organization);

        $this->actingAs($manager)
            ->get(route('organizations.dashboard', $organization))
            ->assertOk()
            ->assertDontSee($student->name);
    }

    /**
     * Enroll people in a campus and return them.
     *
     * @return Collection<int, User>
     */
    private function enroll(School $school, int $count, EnrollmentStatus $status = EnrollmentStatus::Active)
    {
        return collect(range(1, $count))->map(function () use ($school, $status): User {
            $user = User::factory()->create();

            StudentRecord::factory()->create([
                'user_id'   => $user->id,
                'school_id' => $school->id,
                'status'    => $status,
            ]);

            return $user;
        });
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
}
