<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Livewire\CreateAdminForm;
use App\Livewire\EditAdminForm;
use App\Livewire\ListAdminsTable;
use App\Livewire\ManageAccountAccess;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public function test_view_all_admins_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/admins/')->assertForbidden();
    }

    public function test_view_all_admins_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['read admin'])->get('dashboard/admins')->assertOk();
    }

    public function test_authorised_users_can_view_an_admin_profile()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->authorized_user(['read admin', 'manage account access'])
            ->get('dashboard/admins/'.$admin->id)
            ->assertOk()
            ->assertSeeLivewire(ManageAccountAccess::class)
            ->assertSee('id="account-status"', false)
            ->assertSee('Admin')
            ->assertDontSee('data-slot="card"', false);
    }

    public function test_create_admin_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/admins/create')->assertForbidden();
    }

    public function test_create_admin_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['create admin'])->get('dashboard/admins/create')->assertOk();
    }

    public function test_unauthorised_users_cannot_create_admins(): void
    {
        $this->unauthorized_user();

        Livewire::test(CreateAdminForm::class)->assertForbidden();
    }

    public function test_authorized_user_can_create_admin(): void
    {
        $email = $this->faker()->unique()->freeEmail();
        $this->authorized_user(['create admin', 'read admin']);

        $component = Livewire::test(CreateAdminForm::class)
            ->set('name', 'Test admin cody')
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

        $component->assertRedirect(route('admins.show', $person));
        $this->assertTrue($person->hasRole('admin'));
        $this->assertSame('Flat 3', $person->address_line_2);
        $this->assertSame('Nigerian', $person->nationality);
        $this->assertSame('100001', $person->postal_code);
    }

    public function test_edit_admin_cannot_be_accessed_to_unauthorised_users()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->unauthorized_user()->get("dashboard/admins/$admin->id/edit")->assertForbidden();
    }

    public function test_edit_admin_can_be_accessed_by_authorised_users()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->authorized_user(['update admin'])->get("dashboard/admins/$admin->id/edit")->assertOk();
    }

    public function test_unauthorised_users_cannot_update_admins(): void
    {
        $person = User::factory()->create();
        $person->assignRole('admin');
        $this->unauthorized_user();

        Livewire::test(EditAdminForm::class, ['admin' => $person])->assertForbidden();
    }

    public function test_authorised_users_can_update_admins(): void
    {
        $person = User::factory()->create(['nationality' => 'Nigerian', 'postal_code' => '100001']);
        $person->assignRole('admin');
        $email = $this->faker()->unique()->freeEmail();
        $this->authorized_user(['update admin']);

        Livewire::test(EditAdminForm::class, ['admin' => $person])
            ->assertSet('nationality', 'Nigerian')
            ->set('name', 'Renamed admin')
            ->set('email', $email)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admins.show', $person));

        $person->refresh();
        $this->assertSame('Renamed admin', $person->name);
        $this->assertSame($email, $person->email);
        $this->assertSame('Nigerian', $person->nationality);
        $this->assertSame('100001', $person->postal_code);
    }

    public function test_one_school_cannot_change_the_email_of_a_person_another_school_shares(): void
    {
        $person = $this->memberOf(School::factory()->create());
        $person->forceFill(['email' => $this->faker()->unique()->freeEmail()])->save();
        $this->authorized_user(['update admin']);
        $this->memberOf($this->workingSchool(), $person);
        $person->assignRole('admin');
        $originalEmail = $person->email;

        Livewire::test(EditAdminForm::class, ['admin' => $person])
            ->set('email', $this->faker()->unique()->freeEmail())
            ->call('save')
            ->assertHasErrors(['email' => 'This person also belongs to another school, so only they can change their email.']);

        $this->assertSame($originalEmail, $person->fresh()->email);
    }

    public function test_one_school_cannot_change_the_email_of_an_organization_administrator(): void
    {
        $this->authorized_user(['update admin']);
        $person = $this->memberOf($this->workingSchool());
        $person->forceFill(['email' => $this->faker()->unique()->freeEmail()])->save();
        $person->assignRole('admin');
        app(GrantOrganizationMembership::class)->grant($person, $this->workingSchool()->organization);
        $originalEmail = $person->email;

        Livewire::test(EditAdminForm::class, ['admin' => $person->refresh()])
            ->set('email', $this->faker()->unique()->freeEmail())
            ->call('save')
            ->assertHasErrors(['email' => 'This person has authority beyond this school, so only they can change their email.']);

        $this->assertSame($originalEmail, $person->fresh()->email);
    }

    public function test_a_profile_picture_must_be_an_image(): void
    {
        Storage::fake('public');
        $person = User::factory()->create(['email' => $this->faker()->unique()->freeEmail()]);
        $person->assignRole('admin');
        $this->authorized_user(['update admin']);

        Livewire::test(EditAdminForm::class, ['admin' => $person])
            ->set('profilePhoto', UploadedFile::fake()->createWithContent('photo.html', '<script>alert(1)</script>'))
            ->call('save')
            ->assertHasErrors('profilePhoto');

        $this->assertNull($person->fresh()->profile_photo_path);
    }

    public function test_the_same_person_cannot_be_added_as_an_administrator_twice(): void
    {
        $this->authorized_user(['create admin']);
        $existing = User::factory()->create(['email' => $this->faker()->unique()->freeEmail()]);
        $this->memberOf($this->workingSchool(), $existing);
        $existing->assignRole('admin');

        Livewire::test(CreateAdminForm::class)
            ->set('name', 'Someone Else')
            ->set('email', $existing->email)
            ->call('save')
            ->assertHasErrors(['email' => "{$existing->name} already holds the administrator role at this school."]);
    }

    public function test_unauthorised_users_cannot_delete_admins()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->authorized_user(['read admin']);

        Livewire::test(ListAdminsTable::class)
            ->call('deleteAdmin', $admin->id)
            ->assertForbidden();

        $this->assertNotSoftDeleted($admin);
    }

    public function test_authorised_users_can_delete_admins()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->authorized_user(['read admin', 'delete admin']);

        Livewire::test(ListAdminsTable::class)
            ->assertSeeHtml('$wire.call(&quot;deleteAdmin&quot;, row.id)')
            ->call('deleteAdmin', $admin->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertSoftDeleted($admin);
    }

    public function test_a_admin_of_another_school_cannot_be_deleted()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $admin->schoolMemberships()->delete();
        $this->memberOf(School::factory()->create(), $admin->refresh());
        $this->authorized_user(['read admin', 'delete admin']);

        try {
            Livewire::test(ListAdminsTable::class)->call('deleteAdmin', $admin->id);
            $this->fail('A admin of another school was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertNotSoftDeleted($admin);
    }

    public function test_an_administrator_cannot_delete_themself(): void
    {
        $this->authorized_user(['read admin', 'delete admin']);
        $self = auth()->user();
        $self->assignRole('admin');

        Livewire::test(ListAdminsTable::class)
            ->call('deleteAdmin', $self->id)
            ->assertDispatched('status-message', type: 'danger');

        $this->assertNotSoftDeleted($self);
    }
}
