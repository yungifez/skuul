<?php

namespace Tests\Feature;

use App\Actions\Enrollment\MoveEnrollmentBetweenCampuses;
use App\Actions\Finance\CarryBalanceToCampus;
use App\Actions\Finance\ChargeStudent;
use App\Actions\Finance\ReceivePayment;
use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\OrganizationPermission;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\BillingGroup;
use App\Models\Organization;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Finance\ChartOfAccounts;
use App\Services\Finance\StudentLedger;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Campuses that keep one purse bill a family as one school. Campuses that keep
 * separate books keep what they are owed.
 */
class BillingGroupTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_campus_bills_on_its_own_until_it_is_put_in_a_group(): void
    {
        [$first, $second] = $this->twoCampuses();

        $this->assertFalse($first->billsWith($second));
        $this->assertTrue($first->billsWith($first));
    }

    public function test_two_campuses_of_one_group_bill_together(): void
    {
        [$first, $second, $group] = $this->twoCampuses(sharing: true);

        $this->assertNotNull($group);
        $this->assertTrue($first->billsWith($second));
        $this->assertTrue($second->billsWith($first));
    }

    public function test_a_debt_follows_a_learner_between_campuses_of_one_group(): void
    {
        [$source, $destination] = $this->twoCampuses(sharing: true);
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        app(MoveEnrollmentBetweenCampuses::class)->move($enrollment, $this->cycleSection($destination));

        $moved = $enrollment->fresh();

        $this->assertSame($destination->id, $moved->school_id);
        $this->assertSame(500.0, app(StudentLedger::class)->balance($moved));
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::BalanceCarriedToCampus)->first());
    }

    public function test_each_campus_of_the_group_keeps_its_own_books_balanced(): void
    {
        [$source, $destination] = $this->twoCampuses(sharing: true);
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        app(MoveEnrollmentBetweenCampuses::class)->move($enrollment, $this->cycleSection($destination));

        $chart = app(ChartOfAccounts::class);

        // The campus that was left is owed by the campus that took the learner.
        $this->assertSame(0.0, round($chart->account('fees_receivable', $source->id)->balance(), 2));
        $this->assertSame(500.0, round($chart->account('due_from_campus', $source->id)->balance(), 2));
        $this->assertSame(500.0, round($chart->account('fees_receivable', $destination->id)->balance(), 2));
        $this->assertSame(500.0, round($chart->account('due_to_campus', $destination->id)->balance(), 2));
    }

    public function test_money_held_for_a_learner_follows_them_too(): void
    {
        [$source, $destination] = $this->twoCampuses(sharing: true);
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        app(ReceivePayment::class)->receive($enrollment, 20000);

        app(MoveEnrollmentBetweenCampuses::class)->move($enrollment, $this->cycleSection($destination));

        $moved = $enrollment->fresh();

        $this->assertSame(200.0, app(StudentLedger::class)->unappliedCredit($moved));
        $this->assertSame(0.0, round(app(ChartOfAccounts::class)->account('unapplied_credits', $source->id)->balance(), 2));
    }

    public function test_a_debt_stays_behind_when_the_campuses_bill_separately(): void
    {
        [$source, $destination] = $this->twoCampuses();
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        app(MoveEnrollmentBetweenCampuses::class)->move($enrollment, $this->cycleSection($destination));

        $moved = $enrollment->fresh();

        $this->assertSame(0.0, app(StudentLedger::class)->balance($moved));
        $this->assertNull(AuditEvent::ofAction(AuditAction::BalanceCarriedToCampus)->first());
    }

    public function test_the_campus_that_is_owed_still_says_so(): void
    {
        [$source, $destination] = $this->twoCampuses();
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');
        app(MoveEnrollmentBetweenCampuses::class)->move($enrollment, $this->cycleSection($destination));

        $elsewhere = app(StudentLedger::class)->balancesByCampus($enrollment->fresh());

        $this->assertCount(1, $elsewhere);
        $this->assertSame($source->id, $elsewhere->first()['school']->id);
        $this->assertSame(500.0, $elsewhere->first()['balance']);
    }

    public function test_money_never_moves_between_campuses_that_bill_separately(): void
    {
        [$source, $destination] = $this->twoCampuses();
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        $this->expectException(InvalidValueException::class);

        app(CarryBalanceToCampus::class)->carry($enrollment, $source, $destination);
    }

    public function test_a_learner_who_owes_nothing_carries_nothing(): void
    {
        [$source, $destination] = $this->twoCampuses(sharing: true);
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);

        $carried = app(CarryBalanceToCampus::class)->carryIfTheyBillTogether($enrollment, $source, $destination);

        $this->assertSame([], $carried);
    }

    public function test_an_organization_manager_can_start_a_group_and_put_a_campus_in_it(): void
    {
        [$first, , $organization] = $this->twoCampusesAndOrganization();
        $manager = $this->organizationManager($organization);

        $this->actingAs($manager)
            ->post(route('organizations.billing-groups.store', $organization), ['name' => 'City campuses'])
            ->assertRedirect();

        $group = BillingGroup::firstWhere('name', 'City campuses');
        $this->assertNotNull($group);

        $this->actingAs($manager)
            ->put(route('organizations.billing-groups.update', [$organization, $first]), ['billing_group_id' => $group->id])
            ->assertRedirect();

        $this->assertSame($group->id, $first->fresh()->billing_group_id);
    }

    public function test_a_campus_of_another_organization_cannot_be_put_in_the_group(): void
    {
        [, , $organization] = $this->twoCampusesAndOrganization();
        $manager = $this->organizationManager($organization);
        $elsewhere = School::factory()->create(['organization_id' => Organization::factory()->create()->id]);

        $this->actingAs($manager)
            ->put(route('organizations.billing-groups.update', [$organization, $elsewhere]), ['billing_group_id' => null])
            ->assertNotFound();
    }

    public function test_a_group_of_another_organization_is_refused(): void
    {
        [$first, , $organization] = $this->twoCampusesAndOrganization();
        $manager = $this->organizationManager($organization);
        $elsewhere = BillingGroup::factory()->create();

        $this->actingAs($manager)
            ->put(route('organizations.billing-groups.update', [$organization, $first]), ['billing_group_id' => $elsewhere->id])
            ->assertSessionHasErrors('billing_group_id');
    }

    public function test_a_member_without_organization_management_cannot_open_the_screen(): void
    {
        [, , $organization] = $this->twoCampusesAndOrganization();
        $this->organizationManager($organization);
        $reader = $this->organizationManager($organization, [OrganizationPermission::ReadReports]);

        $this->actingAs($reader)
            ->get(route('organizations.billing-groups.index', $organization))
            ->assertForbidden();
    }

    /**
     * Make two campuses of one organization.
     *
     * @return array{0: School, 1: School, 2: BillingGroup|null}
     */
    private function twoCampuses(bool $sharing = false): array
    {
        [$first, $second, $organization] = $this->twoCampusesAndOrganization();
        $group = null;

        if ($sharing) {
            $group = BillingGroup::factory()->create(['organization_id' => $organization->id]);

            foreach ([$first, $second] as $campus) {
                $campus->billing_group_id = $group->id;
                $campus->save();
            }
        }

        return [$first->fresh(), $second->fresh(), $group];
    }

    /**
     * Make two campuses and hand back the organization that owns them.
     *
     * @return array{0: School, 1: School, 2: Organization}
     */
    private function twoCampusesAndOrganization(): array
    {
        $organization = Organization::factory()->create();
        $first = $this->workingSchool();
        $first->organization_id = $organization->id;
        $first->save();

        return [$first->fresh(), School::factory()->create(['organization_id' => $organization->id]), $organization];
    }

    /**
     * Make a home group at one campus.
     */
    private function cycleSection(School $school): AcademicCycleSection
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);

        return AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $school->id])->id,
            'status' => AcademicStructureStatus::Active,
        ]);
    }

    /**
     * Make a person with organization authority.
     *
     * @param  array<int, OrganizationPermission>|null  $permissions
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
