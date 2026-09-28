<?php

namespace Tests\Feature;

use App\Actions\Authorization\AssignCampusRole;
use App\Actions\Authorization\WriteCampusRole;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Livewire\CampusRoleRecord;
use App\Livewire\CreateCampusRoleForm;
use App\Models\AuditEvent;
use App\Models\CampusRole;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Authorization\RoleAuthority;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    public function test_changing_a_shared_role_gives_this_campus_its_own_copy(): void
    {
        $actor = $this->roleManager(['read library', 'manage library']);
        $here = $this->workingSchool();
        $librarian = CampusRole::query()->where('name', 'librarian')->whereNull('school_id')->firstOrFail();
        $sharedPermissions = $librarian->permissions->pluck('name')->sort()->values()->all();
        $ourLibrarian = $this->holderAt($here, $librarian);
        $elsewhere = School::factory()->create();
        $theirLibrarian = $this->holderAt($elsewhere, $librarian);

        $copy = app(WriteCampusRole::class)->update($librarian, $here, ['read library'], 'Runs our library', $actor);

        $this->assertNotSame($librarian->id, $copy->id);
        $this->assertSame($here->id, $copy->school_id);
        $this->assertSame(['read library'], $copy->permissions->pluck('name')->all());
        $this->assertSame('Runs our library', $copy->description);
        $this->assertTrue(app(RoleAuthority::class)->holdersAt($copy, $here)->whereKey($ourLibrarian->id)->exists());
        $this->assertFalse(app(RoleAuthority::class)->holdersAt($librarian, $here)->exists());

        $librarian = $librarian->fresh();
        $this->assertNotSame('Runs our library', $librarian->description);
        $this->assertSame($sharedPermissions, $librarian->permissions->pluck('name')->sort()->values()->all());
        $this->assertTrue(app(RoleAuthority::class)->holdersAt($librarian, $elsewhere)->whereKey($theirLibrarian->id)->exists());
    }

    public function test_retiring_a_shared_role_retires_only_this_campuss_copy(): void
    {
        $actor = $this->roleManager(['read library', 'manage library']);
        $librarian = CampusRole::query()->where('name', 'librarian')->whereNull('school_id')->firstOrFail();

        $copy = app(WriteCampusRole::class)->archive($librarian, $this->workingSchool(), $actor);

        $this->assertTrue($copy->isArchived());
        $this->assertFalse($librarian->fresh()->isArchived());
    }

    public function test_a_campus_with_its_own_copy_cannot_hand_out_the_shared_one(): void
    {
        $this->platform_admin();
        $actor = auth()->user();
        $librarian = CampusRole::query()->where('name', 'librarian')->whereNull('school_id')->firstOrFail();
        app(WriteCampusRole::class)->update($librarian, $this->workingSchool(), [], null, $actor);

        $this->expectException(InvalidValueException::class);

        app(AssignCampusRole::class)->give($this->memberOf($this->workingSchool()), $librarian, $this->workingSchool(), $actor);
    }

    public function test_the_shared_role_opens_as_this_campuss_copy(): void
    {
        $actor = $this->roleManager(['read library', 'manage library']);
        $librarian = CampusRole::query()->where('name', 'librarian')->whereNull('school_id')->firstOrFail();
        $copy = app(WriteCampusRole::class)->update($librarian, $this->workingSchool(), ['read library'], null, $actor);

        $this->actingAs($actor)->get(route('roles.edit', $librarian->id))->assertRedirect(route('roles.edit', $copy->id));
        $this->actingAs($actor)->get(route('roles.index'))->assertOk()->assertSee(route('roles.edit', $copy->id))->assertDontSee(route('roles.edit', $librarian->id));
    }

    public function test_the_role_shows_only_the_people_holding_it_here(): void
    {
        $actor = $this->roleManager(['read library', 'manage library']);
        $librarian = CampusRole::query()->where('name', 'librarian')->whereNull('school_id')->firstOrFail();
        $ours = $this->holderAt($this->workingSchool(), $librarian);
        $theirs = $this->holderAt(School::factory()->create(), $librarian);

        $this->actingAs($actor);
        $record = Livewire::test(CampusRoleRecord::class, ['role' => $librarian])
            ->assertSee($ours->email)
            ->assertDontSee($theirs->email);

        $this->assertThrows(fn () => $record->call('take', $theirs->id), ModelNotFoundException::class);
        $this->assertTrue(app(RoleAuthority::class)->holdersAt($librarian, School::query()->findOrFail($theirs->schoolMemberships()->value('school_id')))->whereKey($theirs->id)->exists());
    }

    public function test_a_campus_keeps_somebody_who_can_manage_roles(): void
    {
        $campus = School::factory()->create();
        $this->authorized_user(['read role', 'manage role'], $campus);
        $founder = auth()->user();
        $head = app(WriteCampusRole::class)->create($campus, 'Head of admin', ['read role', 'manage role'], null, $founder);
        $person = $this->memberOf($campus);
        app(AssignCampusRole::class)->give($person, $head, $campus, $founder);
        school_context()->set($campus, remember: false);
        $founder->revokePermissionTo(['read role', 'manage role']);

        try {
            app(AssignCampusRole::class)->take($person, $head, $campus, $person);
            $this->fail('The last person able to manage roles lost that.');
        } catch (InvalidValueException) {
        }

        try {
            app(WriteCampusRole::class)->update($head, $campus, ['read role'], null, $person);
            $this->fail('The only role able to manage roles lost that.');
        } catch (InvalidValueException) {
        }

        $this->assertTrue(app(RoleAuthority::class)->holdersAt($head, $campus)->whereKey($person->id)->exists());
        $this->assertContains('manage role', $head->fresh()->permissions->pluck('name')->all());
    }

    public function test_giving_a_role_twice_changes_nothing(): void
    {
        $actor = $this->roleManager(['read student']);
        $role = app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);
        $person = $this->memberOf($this->workingSchool());
        $events = fn (): int => AuditEvent::query()->forSubject($person)->count();

        app(AssignCampusRole::class)->give($person, $role, $this->workingSchool(), $actor);
        $afterGiving = $events();
        app(AssignCampusRole::class)->give($person, $role, $this->workingSchool(), $actor);
        $this->assertSame($afterGiving, $events());

        app(AssignCampusRole::class)->take($person, $role, $this->workingSchool(), $actor);
        $afterTaking = $events();
        app(AssignCampusRole::class)->take($person, $role, $this->workingSchool(), $actor);
        $this->assertSame($afterTaking, $events());
        $this->assertGreaterThan($afterGiving, $afterTaking);
    }

    public function test_a_name_in_other_capitals_or_a_shared_name_is_taken(): void
    {
        $actor = $this->roleManager(['read student']);
        app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);

        foreach (['registrar', 'LIBRARIAN', 'Admin'] as $name) {
            try {
                app(WriteCampusRole::class)->create($this->workingSchool(), $name, [], null, $actor);
                $this->fail("$name was written twice.");
            } catch (InvalidValueException) {
            }
        }

        $this->assertSame(1, CampusRole::query()->inSchool()->count());
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

    public function test_a_learner_never_holds_a_campus_role(): void
    {
        $actor = $this->roleManager(['read student']);
        $role = app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);
        $learner = $this->memberOf($this->workingSchool());
        StudentRecord::factory()->create(['user_id' => $learner->id, 'school_id' => $this->workingSchool()->id]);

        try {
            app(AssignCampusRole::class)->give($learner, $role, $this->workingSchool(), $actor);
            $this->fail('A learner was given a campus role.');
        } catch (InvalidValueException $exception) {
            $this->assertSame("$learner->name is a learner. A learner cannot hold a campus role.", $exception->getMessage());
        }

        $this->assertFalse($learner->fresh()->can('read student'));
    }

    public function test_a_learner_who_moved_away_gets_no_role_at_the_campus_they_left(): void
    {
        $actor = $this->roleManager(['read student']);
        $role = app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);
        // The membership here stays after a move. The enrollment went with them.
        $learner = $this->memberOf($this->workingSchool());
        StudentRecord::factory()->create(['user_id' => $learner->id, 'school_id' => School::factory()->create()->id]);

        Livewire::test(CampusRoleRecord::class, ['role' => $role])
            ->assertDontSee($learner->email)
            ->set('personId', (string) $learner->id)
            ->call('give')
            ->assertHasErrors('personId');

        $this->assertFalse(app(RoleAuthority::class)->holdersAt($role, $this->workingSchool())->whereKey($learner->id)->exists());
    }

    public function test_a_former_learner_may_come_back_as_staff(): void
    {
        $actor = $this->roleManager(['read student']);
        $role = app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);
        $alumnus = $this->memberOf($this->workingSchool());
        StudentRecord::factory()->create(['user_id' => $alumnus->id, 'school_id' => $this->workingSchool()->id, 'status' => EnrollmentStatus::Graduated]);

        app(AssignCampusRole::class)->give($alumnus, $role, $this->workingSchool(), $actor);

        $this->assertTrue($alumnus->fresh()->can('read student'));
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

    public function test_nobody_takes_away_a_role_holding_more_than_they_do(): void
    {
        $admin = CampusRole::query()->where('name', Role::Admin->value)->firstOrFail();
        $principal = $this->memberOf($this->workingSchool());
        school_context()->set($this->workingSchool(), remember: false);
        $principal->assignRole($admin);
        $actor = $this->roleManager(['read student']);

        try {
            app(AssignCampusRole::class)->take($principal, $admin, $this->workingSchool(), $actor);
            $this->fail('A role manager stripped a role they could not give.');
        } catch (InvalidValueException $exception) {
            $this->assertStringContainsString('holds more than you do', $exception->getMessage());
        }

        $this->assertTrue(app(RoleAuthority::class)->holdersAt($admin, $this->workingSchool())->whereKey($principal->id)->exists());
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
        $this->unauthorized_user()->get(route('roles.create'))->assertForbidden();

        Livewire::test(CreateCampusRoleForm::class)->assertForbidden();
    }

    public function test_the_form_refuses_a_permission_the_author_does_not_hold(): void
    {
        $this->roleManager(['read student']);

        Livewire::test(CreateCampusRoleForm::class)
            ->set('name', 'Registrar')
            ->set('permissions', ['delete student'])
            ->call('save')
            ->assertHasErrors('permissions.0');

        $this->assertNull(CampusRole::query()->inSchool()->where('name', 'Registrar')->first());
    }

    public function test_the_form_refuses_a_name_the_campus_uses(): void
    {
        $actor = $this->roleManager(['read student']);
        app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', [], null, $actor);

        Livewire::test(CreateCampusRoleForm::class)
            ->set('name', ' registrar ')
            ->call('save')
            ->assertHasErrors('name');
    }

    public function test_a_role_manager_writes_and_fills_a_role_through_the_screens(): void
    {
        $actor = $this->roleManager(['read student', 'update student']);
        $person = $this->memberOf($this->workingSchool());

        $this->actingAs($actor)->get(route('roles.create'))->assertOk()->assertSee('update student');

        Livewire::test(CreateCampusRoleForm::class)
            ->set('name', 'Registrar')
            ->set('permissions', ['read student'])
            ->call('save')
            ->assertHasNoErrors();

        $role = CampusRole::query()->inSchool()->where('name', 'Registrar')->firstOrFail();

        $this->actingAs($actor)->get(route('roles.edit', $role->id))->assertOk()->assertSee($person->email);

        $record = Livewire::test(CampusRoleRecord::class, ['role' => $role])
            ->set('personId', (string) $person->id)
            ->call('give')
            ->assertHasNoErrors();

        $this->assertTrue($person->fresh()->can('read student'));

        $record->call('startEditing')
            ->set('permissions', ['read student', 'update student'])
            ->set('description', 'Keeps the register')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Keeps the register', $role->fresh()->description);
        $this->assertTrue($person->fresh()->can('update student'));

        $record->call('take', $person->id);
        $this->assertFalse($person->fresh()->can('read student'));

        $record->call('archive');
        $this->assertTrue($role->fresh()->isArchived());
        $record->call('restore');
        $this->assertFalse($role->fresh()->isArchived());
    }

    public function test_the_screen_refuses_somebody_who_does_not_work_here(): void
    {
        $actor = $this->roleManager(['read student']);
        $role = app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);
        $outsider = $this->nonMember();

        Livewire::test(CampusRoleRecord::class, ['role' => $role])
            ->set('personId', (string) $outsider->id)
            ->call('give')
            ->assertHasErrors('personId');
    }

    public function test_changing_a_shared_role_on_screen_moves_to_the_campus_copy(): void
    {
        $this->roleManager(['read library', 'manage library']);
        $librarian = CampusRole::query()->where('name', 'librarian')->whereNull('school_id')->firstOrFail();

        $record = Livewire::test(CampusRoleRecord::class, ['role' => $librarian])
            ->call('startEditing')
            ->set('permissions', ['read library'])
            ->call('save')
            ->assertHasNoErrors();

        $copy = CampusRole::query()->inSchool()->where('name', 'librarian')->firstOrFail();
        $record->assertRedirect(route('roles.edit', $copy->id));
    }

    public function test_a_copy_is_made_on_screen(): void
    {
        $actor = $this->roleManager(['read student']);
        $role = app(WriteCampusRole::class)->create($this->workingSchool(), 'Registrar', ['read student'], null, $actor);

        Livewire::test(CampusRoleRecord::class, ['role' => $role])
            ->set('copyName', 'Registrar')
            ->call('duplicate')
            ->assertHasErrors('copyName')
            ->set('copyName', 'Deputy registrar')
            ->call('duplicate')
            ->assertHasNoErrors();

        $this->assertNotNull(CampusRole::query()->inSchool()->where('name', 'Deputy registrar')->first());
    }

    public function test_a_role_of_another_campus_never_opens_here(): void
    {
        $this->roleManager(['read student']);
        $theirs = CampusRole::query()->create([
            'name' => 'Their registrar',
            'guard_name' => 'web',
            'school_id' => School::factory()->create()->id,
        ]);

        $this->get(route('roles.edit', $theirs->id))->assertForbidden();
    }

    /**
     * Make somebody who holds a role at one campus.
     */
    private function holderAt(School $school, CampusRole $role): User
    {
        $person = $this->memberOf($school, $this->nonMember());
        school_context()->set($school, remember: false);
        $person->assignRole($role);
        school_context()->set($this->workingSchool(), remember: false);

        return $person;
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
