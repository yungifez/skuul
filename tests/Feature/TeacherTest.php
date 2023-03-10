<?php

namespace Tests\Feature;

use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TeacherTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;
    use WithFaker;

    public function test_view_all_teachers_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/teachers/')->assertForbidden();
    }

    public function test_view_all_teachers_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['read teacher'])->get('dashboard/teachers')->assertOk();
    }

    public function test_create_teacher_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/teachers/create')->assertForbidden();
    }

    public function test_create_teacher_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['create teacher'])->get('dashboard/teachers/create')->assertOk();
    }

    public function test_unauthorised_users_cannot_create_teachers()
    {
        $email = $this->faker()->freeEmail();
        $this->unauthorized_user()->post('dashboard/teachers', [
            'first_name'            => 'Test',
            'last_name'             => 'teacher',
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

    public function test_authorized_user_can_create_teacher()
    {
        $email = $this->faker()->freeEmail();

        $this->authorized_user(['create teacher'])->post('dashboard/teachers', [
            'first_name'            => 'Test',
            'last_name'             => 'teacher',
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

    public function test_unauthorised_users_cannot_update_teachers()
    {
        $email = $this->faker()->freeEmail();

        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $this->unauthorized_user()->put('dashboard/teachers/'.$teacher->id, [
            'first_name'            => 'Test',
            'last_name'             => 'teacher 2',
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

    public function test_authorised_users_can_update_teachers()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $email = $this->faker()->freeEmail();

        $this->authorized_user(['update teacher'])->put('dashboard/teachers/'.$teacher->id, [
            'first_name'            => 'Test 2',
            'other_names'           => 'teacher 2',
            'last_name'             => 'teacher',
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

    public function test_unauthorised_users_cannot_delete_teachers()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->unauthorized_user()
            ->delete('dashboard/teachers/'.$teacher->id)
            ->assertForbidden();

        $this->assertModelExists($teacher) && $this->assertNotSoftDeleted($teacher);
    }

    public function test_authorised_users_can_delete_teachers()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->authorized_user(['delete teacher'])
            ->delete('dashboard/teachers/'.$teacher->id)
            ->assertRedirect();

        $this->assertModelExists($teacher) && $this->assertSoftDeleted($teacher);
    }
}
