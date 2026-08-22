<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Enums\AcademicPeriodStatus;
use App\Enums\EnrollmentStatus;
use App\Livewire\OrganizationDashboard;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class OrganizationDashboardVerticalSliceTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_platform_administrator_can_open_any_organization_dashboard(): void
    {
        $organization = Organization::factory()->create();
        $administrator = User::factory()->platformAdmin()->create();

        $this->actingAs($administrator)
            ->get(route('organizations.dashboard', $organization))
            ->assertOk();
    }

    public function test_an_organization_administrator_can_only_open_their_organization_dashboard(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $administrator = $this->organizationAdministrator($organization);

        $this->actingAs($administrator)
            ->get(route('organizations.dashboard', $organization))
            ->assertOk();

        $this->actingAs($administrator)
            ->get(route('organizations.dashboard', $otherOrganization))
            ->assertForbidden();
    }

    public function test_opening_the_dashboard_never_grants_a_school_membership(): void
    {
        $organization = Organization::factory()->create();
        $campus = School::factory()->create(['organization_id' => $organization->id]);
        $administrator = $this->organizationAdministrator($organization);

        $this->assertFalse($administrator->fresh()->belongsToSchool($campus));

        $this->actingAs($administrator)
            ->get(route('organizations.dashboard', $organization))
            ->assertOk();

        $this->assertFalse($administrator->fresh()->belongsToSchool($campus));
    }

    public function test_the_dashboard_returns_correct_aggregate_values_and_academic_period_statuses(): void
    {
        $organization = Organization::factory()->create();
        $readyCampus = School::factory()->create(['organization_id' => $organization->id]);
        $academicPeriodMissingCampus = School::factory()->create(['organization_id' => $organization->id]);
        School::factory()->create(['organization_id' => $organization->id]);
        $administrator = $this->organizationAdministrator($organization);

        $academicYear = AcademicYear::factory()->create([
            'school_id' => $readyCampus->id,
            'status'    => AcademicPeriodStatus::Open,
        ]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id'        => $readyCampus->id,
            'academic_year_id' => $academicYear->id,
            'status'           => AcademicPeriodStatus::Closed,
        ]);
        $readyCampus->forceFill([
            'academic_year_id'   => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
        ])->save();

        $academicPeriodMissingYear = AcademicYear::factory()->create([
            'school_id' => $academicPeriodMissingCampus->id,
            'status'    => AcademicPeriodStatus::Draft,
        ]);
        $academicPeriodMissingCampus->forceFill(['academic_year_id' => $academicPeriodMissingYear->id])->save();

        $this->createEnrollment($readyCampus, EnrollmentStatus::Active);
        $this->createEnrollment($readyCampus, EnrollmentStatus::Active);
        $this->createEnrollment($readyCampus, EnrollmentStatus::Graduated);
        $this->createEnrollment($academicPeriodMissingCampus, EnrollmentStatus::Active);
        $this->createCampusAccess($readyCampus);
        $this->createCampusAccess($academicPeriodMissingCampus);

        Livewire::actingAs($administrator)
            ->test(OrganizationDashboard::class, ['organization' => $organization])
            ->call('loadDashboard')
            ->assertSet('campusCount', 3)
            ->assertSet('activeStudents', 3)
            ->assertSet('campusAccessHolders', 2)
            ->assertSet('campusesMissingAcademicSetup', 2)
            ->assertSee($readyCampus->name)
            ->assertSee('Open')
            ->assertSee('Closed')
            ->assertSee('Setup needed');
    }

    public function test_the_dashboard_loads_campus_health_without_an_n_plus_one_query_regression(): void
    {
        $organization = Organization::factory()->create();
        $administrator = $this->organizationAdministrator($organization);

        foreach (range(1, 10) as $index) {
            $campus = School::factory()->create(['organization_id' => $organization->id]);
            $academicYear = AcademicYear::factory()->create(['school_id' => $campus->id]);
            $academicPeriod = AcademicPeriod::factory()->create([
                'school_id'        => $campus->id,
                'academic_year_id' => $academicYear->id,
            ]);
            $campus->forceFill([
                'academic_year_id'   => $academicYear->id,
                'academic_period_id' => $academicPeriod->id,
            ])->save();
        }

        $component = Livewire::actingAs($administrator)
            ->test(OrganizationDashboard::class, ['organization' => $organization]);

        DB::enableQueryLog();
        DB::flushQueryLog();

        $component->call('loadDashboard');

        $selectQueryCount = collect(DB::getQueryLog())
            ->filter(fn (array $query): bool => str_starts_with(strtolower($query['query']), 'select'))
            ->count();

        $this->assertLessThanOrEqual(7, $selectQueryCount);
    }

    private function organizationAdministrator(Organization $organization): User
    {
        $administrator = $this->nonMember();

        app(GrantOrganizationMembership::class)->grant($administrator, $organization);

        return $administrator->fresh();
    }

    private function createEnrollment(School $campus, EnrollmentStatus $status): StudentRecord
    {
        return StudentRecord::query()->create([
            'user_id'          => $this->nonMember()->id,
            'school_id'        => $campus->id,
            'admission_date'   => now(),
            'status'           => $status,
            'admission_number' => Str::uuid()->toString(),
        ]);
    }

    private function createCampusAccess(School $campus): SchoolMembership
    {
        return SchoolMembership::query()->create([
            'user_id'    => $this->nonMember()->id,
            'school_id'  => $campus->id,
            'status'     => 'active',
            'is_primary' => true,
            'joined_at'  => now(),
        ]);
    }
}
