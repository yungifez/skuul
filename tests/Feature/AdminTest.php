<?php

namespace Tests\Feature;

use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;
    use WithFaker;

    public function test_view_all_admins_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/admins/')->assertForbidden();
    }

    public function test_view_all_admins_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['read admin'])->get('dashboard/admins')->assertOk();
    }

    public function test_create_admin_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/admins/create')->assertForbidden();
    }

    public function test_create_admin_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['create admin'])->get('dashboard/admins/create')->assertOk();
    }

    public function test_unauthorised_users_cannot_create_admins()
    {
        $email = $this->faker()->freeEmail();
        $this->unauthorized_user()->post('dashboard/admins', [
            'first_name'            => 'Test',
            'last_name'             => 'admin',
            'other_name'            => 'cody',
            'email'                 => $email,
            'password'              => 'password',
            'password_confirmation' => 'password',
            'gender'                => 'male',
            'nationality'           => 'nigeria',
            'state'                 => 'lagos',
            'city'                  => 'lagos',
            'blood_group'           => 'a+',
            'address'               => 'test address',
            'birthday'              => '2004/04/22',
            'phone'                 => '08080808080',
            'my_class_id'           => 1,
            'section_id'            => 1,
            'admission_date'        => '2004/04/22',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }

    public function test_authorized_user_can_create_admin()
    {
        $email = $this->faker()->freeEmail();

        $this->authorized_user(['create admin'])->post('dashboard/admins', [
            'first_name'            => 'Test',
            'last_name'             => 'admin',
            'other_name'            => 'cody',
            'email'                 => $email,
            'password'              => 'password',
            'password_confirmation' => 'password',
            'gender'                => 'male',
            'nationality'           => 'nigeria',
            'state'                 => 'lagos',
            'city'                  => 'lagos',
            'blood_group'           => 'a+',
            'address'               => 'test address',
            'birthday'              => '2004/04/22',
            'phone'                 => '08080808080',
            'my_class_id'           => 1,
            'section_id'            => 1,
            'admission_date'        => '2004/04/22',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email'    => $email,
            'address'  => 'test address',
            'birthday' => '2004/04/22',
            'phone'    => '08080808080',
        ]);
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

    public function test_unauthorised_users_cannot_update_admins()
    {
        $email = $this->faker()->freeEmail();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->unauthorized_user()->put('dashboard/admins/'.$admin->id, [
            'first_name'            => 'Test',
            'last_name'             => 'admin 2',
            'email'                 => $email,
            'password'              => 'password',
            'password_confirmation' => 'password',
            'gender'                => 'male',
            'nationality'           => 'nigeria',
            'state'                 => 'lagos',
            'city'                  => 'lagos',
            'blood_group'           => 'a+',
            'address'               => 'test address',
            'birthday'              => '2004/04/22',
            'phone'                 => '08080808080',
            'my_class_id'           => 1,
            'section_id'            => 1,
            'admission_date'        => '2004/04/22',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }

    public function test_authorised_users_can_update_admins()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $email = $this->faker()->freeEmail();

        $this->authorized_user(['update admin'])->put('dashboard/admins/'.$admin->id, [
            'first_name'            => 'Test 2',
            'other_names'           => 'admin 2',
            'last_name'             => 'admin',
            'email'                 => $email,
            'password'              => 'password',
            'password_confirmation' => 'password',
            'gender'                => 'male',
            'nationality'           => 'nigeria',
            'state'                 => 'lagos',
            'city'                  => 'lagos',
            'blood_group'           => 'a+',
            'address'               => 'test address',
            'birthday'              => '2004/04/22',
            'phone'                 => '08080808080',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    public function test_unauthorised_users_cannot_delete_admins()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->unauthorized_user()
            ->delete('dashboard/admins/'.$admin->id)
            ->assertForbidden();

        $this->assertModelExists($admin) && $this->assertNotSoftDeleted($admin);
    }

    public function test_authorised_users_can_delete_admins()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->authorized_user(['delete admin'])
            ->delete('dashboard/admins/'.$admin->id)
            ->assertRedirect();

        $this->assertModelExists($admin) && $this->assertSoftDeleted($admin);
    }
}
