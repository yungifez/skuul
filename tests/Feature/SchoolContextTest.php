<?php

namespace Tests\Feature;

use App\Actions\School\GrantSchoolMembership;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\User;
use App\Services\School\SchoolContext;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The school being worked in lives in the request, never on the user record.
 */
class SchoolContextTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_switching_school_writes_to_the_session_not_the_user(): void
    {
        $second = School::factory()->create();
        $user = User::where('email', 'super@example.com')->firstOrFail();

        $before = $user->schoolMemberships()->pluck('school_id')->all();

        $this->actingAs($user)
            ->post('/dashboard/schools/set-school', ['school_id' => $second->id])
            ->assertSessionHas(SchoolContext::SESSION_KEY, $second->id);

        $this->assertSame($before, $user->fresh()->schoolMemberships()->pluck('school_id')->all());
    }

    public function test_the_remembered_school_is_ignored_when_access_ended(): void
    {
        $other = School::factory()->create();
        $home = $this->workingSchool();
        $user = $this->memberOf($home);

        // Pretend the session still points at a school the person cannot use.
        $this->actingAs($user)
            ->withSession([SchoolContext::SESSION_KEY => $other->id])
            ->get('/dashboard')
            ->assertOk();

        $this->assertSame($home->id, school_context()->id());
    }

    public function test_a_platform_admin_may_open_any_school(): void
    {
        $other = School::factory()->create();
        $user = User::factory()->platformAdmin()->create();

        $this->assertSame(
            $other->id,
            app(SchoolContext::class)->schoolIfAllowed($user, $other->id)?->id
        );
    }

    public function test_a_member_may_not_open_a_school_they_left(): void
    {
        $other = School::factory()->create();
        $user = $this->nonMember();

        $this->assertNull(app(SchoolContext::class)->schoolIfAllowed($user, $other->id));
    }

    public function test_records_are_scoped_to_the_school_being_worked_in(): void
    {
        $other = School::factory()->create();

        $mine = AcademicYear::factory()->create(['school_id' => current_school_id()]);
        $theirs = AcademicYear::factory()->create(['school_id' => $other->id]);

        $this->assertTrue(AcademicYear::inSchool()->get()->contains($mine));
        $this->assertFalse(AcademicYear::inSchool()->get()->contains($theirs));
    }

    public function test_permissions_do_not_carry_across_schools(): void
    {
        $home = $this->workingSchool();
        $other = School::factory()->create();
        $user = $this->memberOf($home);

        school_context()->set($home, remember: false);
        $user->givePermissionTo('read admin');

        $this->assertTrue($user->fresh()->can('read admin'));

        // The same person in another school does not carry the permission.
        app(GrantSchoolMembership::class)->grant($user, $other);
        school_context()->set($other, remember: false);

        $this->assertFalse($user->fresh()->can('read admin'));
    }

    public function test_roles_are_held_per_school(): void
    {
        $home = $this->workingSchool();
        $other = School::factory()->create();
        $user = $this->memberOf($home);

        school_context()->set($home, remember: false);
        $user->assignRole('admin');

        app(GrantSchoolMembership::class)->grant($user, $other);
        school_context()->set($other, remember: false);
        $user->fresh()->assignRole('teacher');

        school_context()->set($home, remember: false);
        $this->assertTrue($user->fresh()->hasRole('admin'));
        $this->assertFalse($user->fresh()->hasRole('teacher'));

        school_context()->set($other, remember: false);
        $this->assertTrue($user->fresh()->hasRole('teacher'));
        $this->assertFalse($user->fresh()->hasRole('admin'));
    }

    public function test_a_person_cannot_read_another_schools_records(): void
    {
        $other = School::factory()->create();
        $theirs = AcademicYear::factory()->create(['school_id' => $other->id]);

        $this->authorized_user(['read academic year'])
            ->get("/dashboard/academic-years/$theirs->id")
            ->assertForbidden();
    }
}
