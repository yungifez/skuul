<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Livewire\CreateTeacherForm;
use App\Livewire\EditTeacherForm;
use App\Livewire\ListTeachersTable;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class TeacherTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public function test_view_all_teachers_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/teachers/')->assertForbidden();
    }

    public function test_view_all_teachers_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['read teacher'])
            ->get('dashboard/teachers')
            ->assertOk()
            ->assertSee('data-slot="data-table"', false)
            ->assertSee('Search rows...')
            ->assertSee('No teachers yet');
    }

    public function test_the_teacher_table_searches_rows_on_the_server(): void
    {
        $matchingTeacher = User::factory()->create(['name' => 'Ada Lovelace']);
        $matchingTeacher->assignRole('teacher');

        $otherTeacher = User::factory()->create(['name' => 'Grace Hopper']);
        $otherTeacher->assignRole('teacher');

        $this->authorized_user(['read teacher']);

        Livewire::test(ListTeachersTable::class)
            ->call('updateTable', [
                'search' => 'Ada',
                'perPage' => 10,
                'page' => 1,
            ])
            ->assertSee('Ada Lovelace')
            ->assertDontSee('Grace Hopper');
    }

    public function test_create_teacher_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/teachers/create')->assertForbidden();
    }

    public function test_create_teacher_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['create teacher'])->get('dashboard/teachers/create')->assertOk();
    }

    public function test_unauthorised_users_cannot_create_teachers(): void
    {
        $this->unauthorized_user();

        Livewire::test(CreateTeacherForm::class)->assertForbidden();
    }

    public function test_authorized_user_can_create_teacher(): void
    {
        $email = $this->faker()->unique()->freeEmail();
        $this->authorized_user(['create teacher', 'read teacher']);

        $component = Livewire::test(CreateTeacherForm::class)
            ->set('name', 'Test teacher cody')
            ->set('email', $email)
            ->set('gender', 'Male')
            ->set('nationality', 'Nigerian')
            ->set('address', 'test address')
            ->set('addressLine2', 'Flat 3')
            ->set('postalCode', '100001')
            ->set('birthday', '2004-04-22')
            ->set('phone', '08080808080')
            ->call('save')
            ->assertHasNoErrors();

        $person = User::query()->where('email', $email)->sole();

        $component->assertRedirect(route('teachers.show', $person));
        $this->assertTrue($person->hasRole('teacher'));
        $this->assertSame('Flat 3', $person->address_line_2);
        $this->assertSame('Nigerian', $person->nationality);
        $this->assertSame('100001', $person->postal_code);
    }

    public function test_a_learner_cannot_be_added_as_a_teacher(): void
    {
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $enrollment->user->forceFill(['email' => $this->faker()->unique()->freeEmail()])->save();
        $this->authorized_user(['create teacher', 'read teacher']);

        Livewire::test(CreateTeacherForm::class)
            ->set('name', 'Test teacher cody')
            ->set('email', $enrollment->user->email)
            ->set('gender', 'Male')
            ->set('nationality', 'Nigerian')
            ->set('address', 'test address')
            ->set('birthday', '2004-04-22')
            ->call('save')
            ->assertHasErrors(['email' => "{$enrollment->user->name} is a learner. A learner cannot be made staff."]);

        $this->assertFalse($enrollment->user->refresh()->hasRole('teacher'));
    }

    public function test_edit_teacher_cannot_be_accessed_to_unauthorised_users()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->unauthorized_user()->get("dashboard/teachers/$teacher->id/edit")->assertForbidden();
    }

    public function test_edit_teacher_can_be_accessed_by_authorised_users()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->authorized_user(['update teacher'])->get("dashboard/teachers/$teacher->id/edit")->assertOk();
    }

    public function test_a_teacher_of_another_school_cannot_be_edited_here(): void
    {
        $this->workingSchool();
        $otherSchool = School::factory()->create();
        $teacher = $this->nonMember();
        $this->memberOf($otherSchool, $teacher);
        $teacher->assignRole('teacher');
        $this->authorized_user(['update teacher']);

        Livewire::test(EditTeacherForm::class, ['teacher' => $teacher])->assertForbidden();
    }

    public function test_unauthorised_users_cannot_update_teachers(): void
    {
        $person = User::factory()->create();
        $person->assignRole('teacher');
        $this->unauthorized_user();

        Livewire::test(EditTeacherForm::class, ['teacher' => $person])->assertForbidden();
    }

    public function test_authorised_users_can_update_teachers(): void
    {
        $person = User::factory()->create(['nationality' => 'Nigerian', 'postal_code' => '100001']);
        $person->assignRole('teacher');
        $email = $this->faker()->unique()->freeEmail();
        $this->authorized_user(['update teacher']);
        auth()->user()->assignRole('teacher');

        Livewire::test(EditTeacherForm::class, ['teacher' => $person])
            ->assertSet('nationality', 'Nigerian')
            ->set('name', 'Renamed teacher')
            ->set('email', $email)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('teachers.show', $person));

        $person->refresh();
        $this->assertSame('Renamed teacher', $person->name);
        $this->assertSame($email, $person->email);
        $this->assertSame('Nigerian', $person->nationality);
        $this->assertSame('100001', $person->postal_code);
    }

    public function test_an_editor_holding_less_cannot_change_a_teachers_email(): void
    {
        $person = User::factory()->create(['email' => $this->faker()->unique()->freeEmail()]);
        $person->assignRole('teacher');
        $originalEmail = $person->email;
        $this->authorized_user(['update teacher']);

        Livewire::test(EditTeacherForm::class, ['teacher' => $person])
            ->set('email', $this->faker()->unique()->freeEmail())
            ->call('save')
            ->assertHasErrors(['email' => 'This person holds more at this school than you do, so only they can change their email.']);

        $this->assertSame($originalEmail, $person->fresh()->email);

        Livewire::test(EditTeacherForm::class, ['teacher' => $person->fresh()])
            ->set('name', 'Renamed teacher')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Renamed teacher', $person->fresh()->name);
    }

    public function test_a_teacher_whose_email_domain_has_no_mail_server_can_still_be_renamed(): void
    {
        $person = User::factory()->create(['email' => 'pat.lee@staff.district.invalid']);
        $person->assignRole('teacher');
        $this->authorized_user(['update teacher']);

        Livewire::test(EditTeacherForm::class, ['teacher' => $person])
            ->set('name', 'Pat Lee')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Pat Lee', $person->fresh()->name);
    }

    public function test_unauthorised_users_cannot_delete_teachers()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->authorized_user(['read teacher']);

        Livewire::test(ListTeachersTable::class)
            ->call('deleteTeacher', $teacher->id)
            ->assertForbidden();

        $this->assertNotSoftDeleted($teacher);
    }

    public function test_authorised_users_can_delete_teachers()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->authorized_user(['read teacher', 'delete teacher']);
        auth()->user()->assignRole('teacher');

        Livewire::test(ListTeachersTable::class)
            ->assertSeeHtml('$wire.call(&quot;deleteTeacher&quot;, row.id)')
            ->call('deleteTeacher', $teacher->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertSoftDeleted($teacher);
    }

    public function test_a_remover_holding_less_cannot_delete_a_teacher(): void
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->authorized_user(['read teacher', 'delete teacher']);

        Livewire::test(ListTeachersTable::class)
            ->call('deleteTeacher', $teacher->id)
            ->assertForbidden();

        $this->assertNotSoftDeleted($teacher);
        $this->assertTrue($teacher->belongsToSchool($this->workingSchool()));
    }

    public function test_a_teacher_shared_with_a_sibling_campus_is_only_removed_from_this_one(): void
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->authorized_user(['read teacher', 'delete teacher']);
        auth()->user()->assignRole('teacher');
        $sibling = School::factory()->create(['organization_id' => $this->workingSchool()->organization_id]);
        $this->memberOf($sibling, $teacher);

        Livewire::test(ListTeachersTable::class)
            ->call('deleteTeacher', $teacher->id)
            ->assertDispatched('status-message', message: "{$teacher->name} was removed from this school.");

        $this->assertNotSoftDeleted($teacher);
        $this->assertFalse($teacher->belongsToSchool($this->workingSchool()));
        $this->assertTrue($teacher->belongsToSchool($sibling));
    }

    public function test_a_teacher_with_organization_authority_keeps_their_account(): void
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->authorized_user(['read teacher', 'delete teacher']);
        auth()->user()->assignRole('teacher');
        app(GrantOrganizationMembership::class)->grant($teacher, $this->workingSchool()->organization);

        Livewire::test(ListTeachersTable::class)
            ->call('deleteTeacher', $teacher->id)
            ->assertDispatched('status-message', message: "{$teacher->name} was removed from this school.");

        $this->assertNotSoftDeleted($teacher);
        $this->assertFalse($teacher->belongsToSchool($this->workingSchool()));
    }

    public function test_a_teacher_of_another_school_cannot_be_deleted()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $teacher->schoolMemberships()->delete();
        $this->memberOf(School::factory()->create(), $teacher->refresh());
        $this->authorized_user(['read teacher', 'delete teacher']);
        auth()->user()->assignRole('teacher');

        try {
            Livewire::test(ListTeachersTable::class)->call('deleteTeacher', $teacher->id);
            $this->fail('A teacher of another school was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertNotSoftDeleted($teacher);
    }
}
