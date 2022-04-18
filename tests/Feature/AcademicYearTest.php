<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcademicYearTest extends TestCase
{
    // test ubauthorized user cannot see academic years

    function test_unauthorized_user_cannot_see_academic_years()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get('/dashboard/academic-years')->assertForbidden();
    }

    // test authorized user can see academic years

    function test_authorized_user_can_see_academic_years()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('read academic year');
        $this->actingAs($user);
        $this->get('/dashboard/academic-years')->assertOk();
    }

    //test unauthorized user cannot view create academic year

    function test_unauthorized_user_cannot_create_academic_year()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->post('/dashboard/academic-years')->assertForbidden();
    }

    // test authorized user can view create academic years

    function test_authorized_user_can_create_academic_years()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create academic year');
        $this->actingAs($user);
        $this->get('/dashboard/academic-years/create')->assertOk();
    }

    //test unauthorized user cannot create academic year

    function test_unauthorized_user_cannot_store_academic_year()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->post('/dashboard/academic-years')->assertForbidden();
    }

    //test authorized user can create academic year

    function test_user_can_create_academic_year()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create academic year');
        $this->actingAs($user);
        $this->post('/dashboard/academic-years', [
            'start_year' => '3030',
            'stop_year' => '4040',
        ]);

        $this->assertDatabaseHas('academic_years', [
            'start_year' => '3030',
            'stop_year' => '4040',
        ]);
    }

    //test unauthorized user cannot view edit academic year

    function test_unauthorized_user_cannot_edit_academic_year()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get('/dashboard/academic-years/1/edit')->assertForbidden();
    }

    //test authorized user can view edit academic year

    function test_authorized_user_can_edit_academic_year()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('update academic year');
        $this->actingAs($user);
        $this->get('/dashboard/academic-years/1/edit')->assertOk();
    }

    //test unauthorized user cannot update academic year

    function test_unauthorized_user_cannot_update_academic_year()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->patch('/dashboard/academic-years/1')->assertForbidden();
    }

    //test authorized user can update academic year

    function test_authorized_user_can_update_academic_year()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('update academic year');
        $this->actingAs($user);
        $this->patch('/dashboard/academic-years/1', [
            'start_year' => '3030',
            'stop_year' => '4040',
        ]);

        $this->assertDatabaseHas('academic_years', [
            'id' => '1',
            'start_year' => '3030',
            'stop_year' => '4040',
        ]);
    }

    //test unauthorized user cannot delete academic year

    function test_unauthorized_user_cannot_delete_academic_year()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->delete('/dashboard/academic-years/1')->assertForbidden();
    }

    //test authorized user can delete academic year

    function test_authorized_user_can_delete_academic_year()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete academic year');
        $this->actingAs($user);
        $academicYear = \App\Models\AcademicYear::factory()->create();
        $this->delete("/dashboard/academic-years/$academicYear->id");

        $this->assertDatabaseMissing('academic_years', [
            'id' => $academicYear->id,
        ]);
    }
}