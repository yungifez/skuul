<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Semester;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SemesterTest extends TestCase
{
    //test unauthorized user can not view all semesters

    public function test_unauthorized_user_cannot_view_all_semesters()
    {
        $user  = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard/semesters')->assertForbidden();
    }

    //test authorized user can view all semesters

    public function test_authorized_user_can_view_all_semesters()
    {
        $user  = User::factory()->create();
        $user->givePermissionTo(['read semester']);
        $this->actingAs($user);

        $response = $this->get('/dashboard/semesters')->assertOk();
    }

    //test unauthorized user can not view a semester

    public function test_unauthorized_user_cannot_view_a_semester()
    {
        $user  = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard/semesters/1')->assertForbidden();
    }

    //test authorized user can view a semester

    public function test_authorized_user_can_view_a_semester()
    {
        $user  = User::factory()->create();
        $user->givePermissionTo(['read semester']);
        $this->actingAs($user);

        $response = $this->get('/dashboard/semesters/1')->assertOk();
    }

    //test unauthorized user can not create a semester

    public function test_unauthorized_user_can_not_view_create_semester()
    {
        $user  = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard/semesters')->assertForbidden();
    }

    //test authorized user can view create semester

    public function test_authorized_user_can_view_create_semester()
    {
        $user  = User::factory()->create();
        $user->givePermissionTo(['create semester']);
        $this->actingAs($user);

        $response = $this->get('/dashboard/semesters/create')->assertOk();
    }

    //test unauthorized user can not store a semester

    public function test_unauthorized_user_can_not_store_a_semester()
    {
        $user  = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/dashboard/semesters', [])->assertForbidden();
    }

    //test authorized user can store a semester

    public function test_authorized_user_can_store_a_semester()
    {
        $user  = User::factory()->create();
        $user->givePermissionTo(['create semester']);
        $this->actingAs($user);

        $response = $this->post('/dashboard/semesters', ['name' => 'test semester']);
        $semester = \App\Models\Semester::where('name', 'test semester')->first();

        $this->assertModelExists($semester);
    }

    //test unauthorized user can not update a semester

    public function test_unauthorized_user_can_not_update_a_semester()
    {
        $user  = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put('/dashboard/semesters/1', ['name' => 'Test semester'])->assertForbidden();
    }

    //test authorized user can update a semester

    public function test_authorized_user_can_update_a_semester()
    {
        $user  = User::factory()->create();
        $user->givePermissionTo(['update semester']);
        $this->actingAs($user);
        $semester = Semester::factory()->create();
        $response = $this->put("/dashboard/semesters/$semester->id", ['name' => 'Test semester']);

        $this->assertDatabaseHas('semesters', [
            'id' => $semester->id,
            'name' => 'Test semester'
        ]);
    }

    //test unauthorized user can not delete a semester

    public function test_unauthorized_user_can_not_delete_a_semester()
    {
        $user  = User::factory()->create();
        $this->actingAs($user);

        $response = $this->delete('/dashboard/semesters/1')->assertForbidden();
    }

    //test authorized user can delete a semester

    public function test_authorized_user_can_delete_a_semester()
    {
        $user  = User::factory()->create();
        $user->givePermissionTo(['delete semester']);
        $this->actingAs($user);
        $semester = Semester::factory()->create();
        $response = $this->delete("/dashboard/semesters/$semester->id");

        $this->assertDatabaseMissing('semesters', [
            'id' => $semester->id,
            'name' => $semester->name
        ]);
    }

    // test unauthorized user can not set semester

    public function test_unauthorized_user_can_not_set_semester()
    {
        $user  = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/dashboard/semesters/set',[
            'semester_id' => 1
        ])->assertForbidden();
    }

    // test authorized user can set semester

    public function test_authorized_user_can_set_semester()
    {
        $user  = User::factory()->create();
        $user->givePermissionTo(['set semester']);
        $this->actingAs($user);
        $semester = Semester::factory()->create();
        $response = $this->post('/dashboard/semesters/set',[
            'semester_id' => $semester->id
        ]);

        $this->assertDatabaseHas('schools', [
            'id' => auth()->user()->school->id,
            'semester_id' => $semester->id
        ]);
    }
}
