<?php

namespace Tests\Feature;

use App\Actions\Authorization\AssignCampusRole;
use App\Actions\Authorization\WriteCampusRole;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\AuditEvent;
use App\Models\CampusRole;
use App\Models\School;
use App\Models\User;
use App\Services\Authorization\RoleAuthority;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A campus writes its own roles, and nobody hands out authority they do not
 * hold themselves.
 */
class CampusRoleTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_campus_writes_a_role_of_its_own(): void
    {
        $actor = $this->roleManager(['read student', 'update student']);

        $role = app(WriteCampusRole::class)->create(
            $this->workingSchool(),
            'Registrar',
            ['read student', 'update student'],
            'Keeps the register',
            $actor,
        );

        $this->assertSame($this->workingSchool()->id, $role->school_id);
        $this->assertSame(['read student', 'update student'], $role->permissions->pluck('name')->sort()->values()->all());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::RoleCreated)->forSubject($role)->first());
    }

    public function test_nobody_writes_a_role_holding_more_than_they_do(): void
    {
        $actor = $this->roleManager(['read student']);

        $this->expectException(InvalidValueException::class);

        app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student', 'delete student'], null, $actor);
    }

    public function test_a_platform_permission_is_never_on_the_list(): void
    {
        $actor = $this->roleManager(['read student']);

        $grantable = app(RoleAuthority::class)->grantableBy($actor, $this->workingSchool());

        $this->assertFalse($grantable->contains('access all schools'));
        $this->assertFalse($grantable->contains('manage organization'));
    }

    public function test_the_same_name_cannot_be_used_twice_at_one_campus(): void
    {
        $actor = $this->roleManager(['read student']);
        app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);

        $this->expectException(InvalidValueException::class);

        app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', [], null, $actor);
    }

    public function test_a_built_in_role_cannot_be_rewritten(): void
    {
        $actor = $this->roleManager(['read student']);
        $admin = CampusRole::query()->where('name', Role::Admin->value)->firstOrFail();

        $this->expectException(InvalidValueException::class);

        app(WriteCampusRole::class)->update($admin, $this->workingSchool(), [], null, $actor);
    }

    public function test_a_role_of_another_campus_cannot_be_changed(): void
    {
        $actor = $this->roleManager(['read student']);
        $elsewhere = School::factory()->create();
        $theirs = CampusRole::query()->create([
            'name' => 'Their registrar',
            'guard_name' => 'web',
            'school_id' => $elsewhere->id,
        ]);

        $this->expectException(InvalidValueException::class);

        app(WriteCampusRole::class)->update($theirs, $this->workingSchool(), [], null, $actor);
    }

    public function test_a_copy_holds_only_what_the_person_copying_it_holds(): void
    {
        $author = $this->roleManager(['read student', 'update student', 'delete student']);
        $rich = app(WriteCampusRole::class)->create(
            $this->workingSchool(),
            'Registrar',
            ['read student', 'update student', 'delete student'],
            null,
            $author,
        );

        $lesser = $this->roleManager(['read student']);
        $copy = app(WriteCampusRole::class)->duplicate($rich, $this->workingSchool(), 'Registrar assistant', $lesser);

        $this->assertSame(['read student'], $copy->permissions->pluck('name')->all());
    }

    public function test_a_retired_role_keeps_its_holders_and_is_never_offered_again(): void
    {
        $actor = $this->roleManager(['read student']);
        $role = app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);
        $person = $this->memberOf($this->workingSchool());
        app(AssignCampusRole::class)->give($person, $role, $this->workingSchool(), $actor);

        app(WriteCampusRole::class)->archive($role, $this->workingSchool(), $actor);

        $this->assertTrue($role->fresh()->isArchived());
        $this->assertSame(1, $role->fresh()->users()->count());
        $this->assertFalse(CampusRole::query()->inSchool()->inUse()->where('id', $role->id)->exists());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::RoleArchived)->forSubject($role)->first());
    }

    public function test_a_retired_role_cannot_be_given_to_anybody_new(): void
    {
        $actor = $this->roleManager(['read student']);
        $role = app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);
        app(WriteCampusRole::class)->archive($role, $this->workingSchool(), $actor);

        $this->expectException(InvalidValueException::class);

        app(AssignCampusRole::class)->give($this->memberOf($this->workingSchool()), $role, $this->workingSchool(), $actor);
    }

    public function test_a_role_reaches_only_somebody_who_works_here(): void
    {
        $actor = $this->roleManager(['read student']);
        $role = app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);

        $this->expectException(InvalidValueException::class);

        app(AssignCampusRole::class)->give($this->nonMember(), $role, $this->workingSchool(), $actor);
    }

    public function test_the_role_gives_its_holder_what_it_holds(): void
    {
        $actor = $this->roleManager(['read student']);
        $role = app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);
        $person = $this->memberOf($this->workingSchool());

        app(AssignCampusRole::class)->give($person, $role, $this->workingSchool(), $actor);

        $this->assertTrue($person->fresh()->can('read student'));
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::RoleAttached)->first());
    }

    public function test_taking_the_role_away_takes_away_what_it_held(): void
    {
        $actor = $this->roleManager(['read student']);
        $role = app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);
        $person = $this->memberOf($this->workingSchool());
        app(AssignCampusRole::class)->give($person, $role, $this->workingSchool(), $actor);

        app(AssignCampusRole::class)->take($person, $role, $this->workingSchool(), $actor);

        $this->assertFalse($person->fresh()->can('read student'));
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::RoleDetached)->first());
    }

    public function test_a_built_in_role_can_still_be_given_out_here(): void
    {
        $actor = $this->platform_admin();
        $teacher = CampusRole::query()->where('name', Role::Teacher->value)->firstOrFail();
        $person = $this->memberOf($this->workingSchool());

        app(AssignCampusRole::class)->give($person, $teacher, $this->workingSchool(), auth()->user());

        $this->assertTrue($person->fresh()->hasRole(Role::Teacher->value));
    }

    public function test_nobody_gives_out_a_role_holding_more_than_they_do(): void
    {
        $actor = $this->roleManager(['read student']);
        $admin = CampusRole::query()->where('name', Role::Admin->value)->firstOrFail();

        $this->expectException(InvalidValueException::class);

        app(AssignCampusRole::class)->give($this->memberOf($this->workingSchool()), $admin, $this->workingSchool(), $actor);
    }

    public function test_the_screen_lists_the_roles_of_this_campus_only(): void
    {
        $actor = $this->roleManager(['read student']);
        app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);
        CampusRole::query()->create([
            'name' => 'Somebody elses role',
            'guard_name' => 'web',
            'school_id' => School::factory()->create()->id,
        ]);

        $this->actingAs($actor)
            ->get(route('roles.index'))
            ->assertOk()
            ->assertSee('Registrar')
            ->assertDontSee('Somebody elses role');
    }

    public function test_a_person_without_role_management_cannot_write_one(): void
    {
        $this->unauthorized_user()
            ->post(route('roles.store'), ['name' => 'Registrar'])
            ->assertForbidden();
    }

    public function test_the_form_refuses_a_permission_the_author_does_not_hold(): void
    {
        $actor = $this->roleManager(['read student']);

        $this->actingAs($actor)
            ->post(route('roles.store'), ['name' => 'Registrar', 'permissions' => ['delete student']])
            ->assertRedirect();

        $this->assertNull(CampusRole::query()->inSchool()->where('name', 'Registrar')->first());
    }

    public function test_a_role_manager_writes_and_fills_a_role_through_the_screens(): void
    {
        $actor = $this->roleManager(['read student', 'update student']);
        $person = $this->memberOf($this->workingSchool());

        $this->actingAs($actor)
            ->post(route('roles.store'), ['name' => 'Registrar', 'permissions' => ['read student']])
            ->assertRedirect();

        $role = CampusRole::query()->inSchool()->where('name', 'Registrar')->firstOrFail();

        $this->actingAs($actor)
            ->post(route('roles.members.store', $role->id), ['user_id' => $person->id])
            ->assertRedirect();

        $this->assertTrue($person->fresh()->can('read student'));
    }

    /**
     * Make a person who may write roles and holds the given permissions here.
     *
     * @param  array<int, string>  $permissions
     */
    private function roleManager(array $permissions): User
    {
        $this->authorized_user(['read role', 'manage role', ...$permissions]);

        /** @var User $actor */
        $actor = auth()->user();

        return $actor;
    }
}
