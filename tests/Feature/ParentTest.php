<?php

namespace Tests\Feature;

use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParentTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;
    use WithFaker;

    public function test_view_all_parents_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/parents/')->assertForbidden();
    }

    public function test_view_all_parents_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['read parent'])->get('dashboard/parents')->assertOk();
    }

    public function test_create_parent_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/parents/create')->assertForbidden();
    }

    public function test_create_parent_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['create parent'])->get('dashboard/parents/create')->assertOk();
    }

    public function test_unauthorised_users_cannot_create_parents()
    {
        $email = $this->faker()->freeEmail();
        $this->unauthorized_user()->post('dashboard/parents', [
            'first_name'            => 'Test',
            'last_name'             => 'parent',
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

    public function test_authorized_user_can_create_parent()
    {
        $email = $this->faker()->freeEmail();

        $this->authorized_user(['create parent'])->post('dashboard/parents', [
            'first_name'            => 'Test',
            'last_name'             => 'parent',
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

    public function test_edit_parent_cannot_be_accessed_to_unauthorised_users()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $this->unauthorized_user()->get("dashboard/parents/$parent->id/edit")->assertForbidden();
    }

    public function test_edit_parent_can_be_accessed_by_authorised_users()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $this->authorized_user(['update parent'])->get("dashboard/parents/$parent->id/edit")->assertOk();
    }

    public function test_unauthorised_users_cannot_update_parents()
    {
        $email = $this->faker()->freeEmail();

        $parent = User::factory()->create();
        $parent->assignRole('parent');

        $this->unauthorized_user()->put('dashboard/parents/'.$parent->id, [
            'first_name'            => 'Test',
            'last_name'             => 'parent 2',
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

    public function test_authorised_users_can_update_parents()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $email = $this->faker()->freeEmail();

        $this->authorized_user(['update parent'])->put('dashboard/parents/'.$parent->id, [
            'first_name'            => 'Test 2',
            'other_names'           => 'parent 2',
            'last_name'             => 'parent',
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

    public function test_unauthorised_users_cannot_delete_parents()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $this->unauthorized_user()
            ->delete('dashboard/parents/'.$parent->id)
            ->assertForbidden();

        $this->assertModelExists($parent) && $this->assertNotSoftDeleted($parent);
    }

    public function test_authorised_users_can_delete_parents()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $this->authorized_user(['delete parent'])
            ->delete('dashboard/parents/'.$parent->id)
            ->assertRedirect();

        $this->assertModelExists($parent) && $this->assertSoftDeleted($parent);
    }

    public function test_unauthorised_users_cannot_assign_student_to_parent()
    {
        $student = StudentRecord::factory()->create();

        $parent = User::factory()->create();
        $parent->parentRecord()->create(['user_id' => $parent->id]);
        $parent->assignRole('parent');

        $this->unauthorized_user()
            ->post("dashboard/parents/$parent->id/assign-student-to-parent", [
                'student_id' => $student->user->id,
                'assign'     => true,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('parent_record_user', [
            'parent_record_id' => $parent->parentRecord->id,
            'user_id'          => $student->user->id,
        ]);
    }

    public function test_authorised_users_can_assign_student_to_parent()
    {
        $student = StudentRecord::factory()->create();

        $parent = User::factory()->create();
        $parent->parentRecord()->create(['user_id' => $parent->id]);
        $parent->assignRole('parent');

        $this->authorized_user(['update parent'])
            ->post("dashboard/parents/$parent->id/assign-student-to-parent", [
                'student_id' => $student->user->id,
                'assign'     => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('parent_record_user', [
            'parent_record_id' => $parent->parentRecord->id,
            'user_id'          => $student->user->id,
        ]);
    }
}
