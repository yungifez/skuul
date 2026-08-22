<?php

namespace Tests\Feature;

use App\Actions\Identity\ProvisionAccount;
use App\Actions\School\EndSchoolMembership;
use App\Actions\School\GrantSchoolMembership;
use App\Enums\SchoolMembershipStatus;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * School access lives in membership records, not on the user row.
 */
class SchoolMembershipTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public function test_the_users_table_no_longer_carries_a_school(): void
    {
        $this->assertFalse(
            Schema::hasColumn('users', 'school_id'),
            'School access must live in school_memberships, not on the user record.'
        );
    }

    public function test_granting_membership_creates_one_active_record(): void
    {
        $school = School::factory()->create();
        $user = $this->nonMember();

        $membership = app(GrantSchoolMembership::class)->grant($user, $school);

        $this->assertSame(SchoolMembershipStatus::Active, $membership->status);
        $this->assertTrue($membership->is_primary);
        $this->assertTrue($user->fresh()->belongsToSchool($school));
    }

    public function test_granting_the_same_membership_twice_does_not_duplicate_it(): void
    {
        $school = School::factory()->create();
        $user = $this->nonMember();

        app(GrantSchoolMembership::class)->grant($user, $school);
        app(GrantSchoolMembership::class)->grant($user, $school);

        $this->assertSame(1, $user->schoolMemberships()->count());
    }

    public function test_one_person_can_work_in_several_schools(): void
    {
        $first = School::factory()->create();
        $second = School::factory()->create();
        $user = $this->nonMember();

        app(GrantSchoolMembership::class)->grant($user, $first, primary: true);
        app(GrantSchoolMembership::class)->grant($user, $second);

        $user->refresh();

        $this->assertTrue($user->belongsToSchool($first));
        $this->assertTrue($user->belongsToSchool($second));
        $this->assertSame($first->id, $user->primarySchool()->id);
        $this->assertSame(1, $user->schoolMemberships()->primary()->count());
    }

    public function test_ending_a_membership_keeps_the_record_and_removes_access(): void
    {
        $school = School::factory()->create();
        $user = $this->nonMember();

        app(GrantSchoolMembership::class)->grant($user, $school);
        app(EndSchoolMembership::class)->end($user, $school);

        $user->refresh();

        $this->assertFalse($user->belongsToSchool($school));
        $this->assertSame(1, $user->schoolMemberships()->count());
        $this->assertSame(SchoolMembershipStatus::Ended, $user->schoolMemberships()->first()->status);
        $this->assertNotNull($user->schoolMemberships()->first()->ended_at);
    }

    public function test_ending_the_primary_membership_promotes_another_one(): void
    {
        $first = School::factory()->create();
        $second = School::factory()->create();
        $user = $this->nonMember();

        app(GrantSchoolMembership::class)->grant($user, $first, primary: true);
        app(GrantSchoolMembership::class)->grant($user, $second);
        app(EndSchoolMembership::class)->end($user, $first);

        $this->assertSame($second->id, $user->fresh()->primarySchool()->id);
    }

    public function test_provisioning_a_person_into_a_second_school_reuses_one_login(): void
    {
        $second = School::factory()->create();

        // Provisioning validates the address against DNS, so use a real domain.
        $existing = User::factory()->create(['email' => $this->faker()->unique()->freeEmail()]);

        app(ProvisionAccount::class)->provision([
            'name'        => $existing->name,
            'email'       => $existing->email,
            'school_id'   => $second->id,
            'birthday'    => '2000-01-01',
            'address'     => 'a road',
            'nationality' => 'nigeria',
            'state'       => 'lagos',
            'city'        => 'lagos',
            'gender'      => 'male',
        ]);

        $this->assertSame(1, User::where('email', $existing->email)->count());
        $this->assertTrue($existing->fresh()->belongsToSchool($second));
    }

    public function test_a_person_with_no_membership_cannot_reach_the_dashboard(): void
    {
        $user = $this->nonMember();

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
    }
}
