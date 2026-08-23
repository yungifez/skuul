<?php

namespace Tests\Feature;

use App\Actions\Organization\AddSchoolDomain;
use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Actions\Organization\VerifySchoolDomain;
use App\Actions\School\GrantSchoolMembership;
use App\Enums\AuditAction;
use App\Enums\OrganizationPermission;
use App\Exceptions\InvalidValueException;
use App\Models\AuditEvent;
use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolDomain;
use App\Models\User;
use App\Services\School\DnsTextRecords;
use App\Services\School\DomainContext;
use App\Services\School\SchoolContext;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * An address says which campus a visitor meant. It never says who they are.
 */
class SchoolDomainTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_claimed_address_does_nothing_until_it_is_proved(): void
    {
        $domain = SchoolDomain::factory()->create(['host' => 'lagos.example.school']);

        $this->assertNull(SchoolDomain::forHost('lagos.example.school'));

        $domain->verified_at = now();
        $domain->save();

        $this->assertNotNull(SchoolDomain::forHost('lagos.example.school'));
    }

    public function test_an_address_is_read_the_same_however_it_is_typed(): void
    {
        SchoolDomain::factory()->verified()->create(['host' => 'lagos.example.school']);

        $this->assertNotNull(SchoolDomain::forHost('LAGOS.Example.School'));
        $this->assertNotNull(SchoolDomain::forHost('lagos.example.school:8080'));
    }

    public function test_the_address_opens_its_campus_for_a_member(): void
    {
        [$organization, $campus, $other] = $this->twoCampuses();
        $user = $this->personOf($campus, $other);
        SchoolDomain::factory()->verified()->create([
            'organization_id' => $organization->id,
            'school_id' => $campus->id,
            'host' => 'lagos.example.school',
        ]);

        $this->onHost('lagos.example.school');

        $this->assertSame($campus->id, app(SchoolContext::class)->resolveFor($user)?->id);
    }

    public function test_the_address_is_ignored_for_somebody_with_no_membership_there(): void
    {
        [$organization, $campus, $other] = $this->twoCampuses();
        $user = $this->personOf($other);
        SchoolDomain::factory()->verified()->create([
            'organization_id' => $organization->id,
            'school_id' => $campus->id,
            'host' => 'lagos.example.school',
        ]);

        $this->onHost('lagos.example.school');

        $this->assertSame($other->id, app(SchoolContext::class)->resolveFor($user)?->id);
    }

    public function test_an_address_that_names_no_campus_leaves_the_choice_alone(): void
    {
        [$organization, $campus, $other] = $this->twoCampuses();
        $user = $this->personOf($other, $campus);
        SchoolDomain::factory()->verified()->create([
            'organization_id' => $organization->id,
            'school_id' => null,
            'host' => 'example.school',
        ]);

        $this->onHost('example.school');

        $this->assertSame($other->id, app(SchoolContext::class)->resolveFor($user)?->id);
    }

    public function test_the_address_wins_over_the_remembered_campus(): void
    {
        [$organization, $campus, $other] = $this->twoCampuses();
        $user = $this->personOf($other, $campus);
        SchoolDomain::factory()->verified()->create([
            'organization_id' => $organization->id,
            'school_id' => $campus->id,
            'host' => 'lagos.example.school',
        ]);

        $this->onHost('lagos.example.school');
        $request = Request::create('/dashboard');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put(SchoolContext::SESSION_KEY, $other->id);

        $this->assertSame($campus->id, app(SchoolContext::class)->resolveFor($user, $request)?->id);
    }

    public function test_an_unknown_address_changes_nothing(): void
    {
        [, $campus] = $this->twoCampuses();
        $user = $this->personOf($campus);

        $this->onHost('somebody-elses.example');

        $this->assertSame($campus->id, app(SchoolContext::class)->resolveFor($user)?->id);
    }

    public function test_a_claimed_address_is_written_down_with_a_record_to_prove_it(): void
    {
        $organization = Organization::factory()->create();

        $domain = app(AddSchoolDomain::class)->add($organization, 'Lagos.Example.School ');

        $this->assertSame('lagos.example.school', $domain->host);
        $this->assertFalse($domain->isVerified());
        $this->assertSame('_skuul-verification.lagos.example.school', $domain->verificationRecord());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::SchoolDomainAdded)->forSubject($domain)->first());
    }

    public function test_the_same_address_cannot_be_claimed_twice(): void
    {
        $organization = Organization::factory()->create();
        app(AddSchoolDomain::class)->add($organization, 'lagos.example.school');

        $this->expectException(InvalidValueException::class);

        app(AddSchoolDomain::class)->add(Organization::factory()->create(), 'lagos.example.school');
    }

    public function test_something_that_is_not_an_address_is_refused(): void
    {
        $this->expectException(InvalidValueException::class);

        app(AddSchoolDomain::class)->add(Organization::factory()->create(), 'not an address');
    }

    public function test_an_address_cannot_name_another_organization_campus(): void
    {
        $organization = Organization::factory()->create();
        $elsewhere = School::factory()->create(['organization_id' => Organization::factory()->create()->id]);

        $this->expectException(InvalidValueException::class);

        app(AddSchoolDomain::class)->add($organization, 'lagos.example.school', $elsewhere);
    }

    public function test_an_address_is_proved_by_the_record_only_its_owner_can_write(): void
    {
        $domain = SchoolDomain::factory()->create(['host' => 'lagos.example.school']);
        $this->dnsAnswers([$domain->verificationRecord() => [$domain->verification_token]]);

        app(VerifySchoolDomain::class)->verify($domain);

        $this->assertTrue($domain->fresh()->isVerified());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::SchoolDomainVerified)->forSubject($domain)->first());
    }

    public function test_an_address_without_the_record_stays_unproved(): void
    {
        $domain = SchoolDomain::factory()->create(['host' => 'lagos.example.school']);
        $this->dnsAnswers([]);

        try {
            app(VerifySchoolDomain::class)->verify($domain);
            $this->fail('An address with no record was accepted.');
        } catch (InvalidValueException) {
            // The organization has not proved anything yet.
        }

        $this->assertFalse($domain->fresh()->isVerified());
    }

    public function test_somebody_elses_value_does_not_prove_the_address(): void
    {
        $domain = SchoolDomain::factory()->create(['host' => 'lagos.example.school']);
        $this->dnsAnswers([$domain->verificationRecord() => ['a value from another application']]);

        $this->expectException(InvalidValueException::class);

        app(VerifySchoolDomain::class)->verify($domain);
    }

    public function test_an_organization_manager_can_claim_and_prove_an_address(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->organizationManager($organization);

        $this->actingAs($manager)
            ->post(route('organizations.domains.store', $organization), ['host' => 'lagos.example.school'])
            ->assertRedirect();

        $domain = SchoolDomain::firstWhere('host', 'lagos.example.school');
        $this->assertNotNull($domain);

        $this->dnsAnswers([$domain->verificationRecord() => [$domain->verification_token]]);

        $this->actingAs($manager)
            ->post(route('organizations.domains.verify', [$organization, $domain]))
            ->assertRedirect();

        $this->assertTrue($domain->fresh()->isVerified());
    }

    public function test_a_member_without_organization_management_cannot_claim_an_address(): void
    {
        $organization = Organization::factory()->create();
        // The organization keeps a full administrator, so the reader can be narrowed.
        $this->organizationManager($organization);
        $reader = $this->organizationManager($organization, [OrganizationPermission::ReadReports]);

        $this->actingAs($reader)
            ->get(route('organizations.domains.index', $organization))
            ->assertForbidden();
    }

    public function test_an_administrator_of_another_organization_cannot_claim_an_address(): void
    {
        $organization = Organization::factory()->create();
        $outsider = $this->organizationManager(Organization::factory()->create());

        $this->actingAs($outsider)
            ->post(route('organizations.domains.store', $organization), ['host' => 'lagos.example.school'])
            ->assertForbidden();
    }

    public function test_an_address_of_another_organization_cannot_be_given_up(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->organizationManager($organization);
        $elsewhere = SchoolDomain::factory()->create();

        $this->actingAs($manager)
            ->delete(route('organizations.domains.destroy', [$organization, $elsewhere]))
            ->assertNotFound();

        $this->assertNotNull($elsewhere->fresh());
    }

    public function test_giving_up_an_address_stops_it_being_answered(): void
    {
        $organization = Organization::factory()->create();
        $manager = $this->organizationManager($organization);
        $domain = SchoolDomain::factory()->verified()->create([
            'organization_id' => $organization->id,
            'host' => 'lagos.example.school',
        ]);

        $this->actingAs($manager)
            ->delete(route('organizations.domains.destroy', [$organization, $domain]))
            ->assertRedirect();

        $this->assertNull(SchoolDomain::forHost('lagos.example.school'));
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::SchoolDomainRemoved)->first());
    }

    /**
     * Answer domain name lookups with what the test says.
     *
     * @param  array<string, array<int, string>>  $answers
     */
    private function dnsAnswers(array $answers): void
    {
        $this->app->instance(DnsTextRecords::class, new class($answers) extends DnsTextRecords
        {
            /** @param array<string, array<int, string>> $answers */
            public function __construct(private array $answers) {}

            /** @return array<int, string> */
            public function lookup(string $name): array
            {
                return $this->answers[$name] ?? [];
            }
        });
    }

    /**
     * Make believe the request came in on one address.
     */
    private function onHost(string $host): void
    {
        app(DomainContext::class)->resolveFor(Request::create("https://$host/dashboard"));
    }

    /**
     * Make an organization with two campuses.
     *
     * @return array{0: Organization, 1: School, 2: School}
     */
    private function twoCampuses(): array
    {
        $organization = Organization::factory()->create();

        return [
            $organization,
            School::factory()->create(['organization_id' => $organization->id]),
            School::factory()->create(['organization_id' => $organization->id]),
        ];
    }

    /**
     * Make a person who works in the given campuses, the first one primary.
     */
    private function personOf(School ...$schools): User
    {
        $user = $this->nonMember();

        foreach ($schools as $index => $school) {
            app(GrantSchoolMembership::class)->grant($user, $school, primary: $index === 0);
        }

        return $user->refresh();
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
