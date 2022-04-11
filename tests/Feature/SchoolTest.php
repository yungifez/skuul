<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchoolTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_view_schools_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['read school']
        );
        $this->actingAs($user);
        $response = $this->get('/dashboard/schools');

        $response->assertStatus(200);
    }

    public function test_view_schools_rendered_to_unauthorized_user()
    {
        $user = User::factory()->create();
     
        $this->actingAs($user);
        $response = $this->get('/dashboard/schools');

        $response->assertStatus(403);
    }

    public function test_create_schools_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['create school']
        );
        $this->actingAs($user);
        $response = $this->get('/dashboard/schools/create');

        $response->assertStatus(200);
    }

    public function test_user_can_create_school()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['create school']
        );
        $this->actingAs($user);
        $response = $this->post('/dashboard/schools', ['name' => 'Test school', 'address' => 'Test address', 'initials' => 'DS']);
        $school = School::where('name','Test school')->get();
        if ($school == null) {
            return false;
        }

        $response->assertRedirect();
    }

    public function test_unauthorized_user_can_not_create_school()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/dashboard/schools', ['name' => 'Test school', 'address' => 'Test address', 'initials' => 'DS']);

        $response->assertForbidden();
    }

    public function test_show_school_can_be_rendered_to_super_admin()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $this->actingAs($user);
        $school = School::where('name','Test school')->first();
        $response = $this->get("/dashboard/schools/$school->id");

        $response->assertStatus(200);
    }

    public function test_show_school_can_be_rendered_to_authorized_user_in_same_school()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['read school']
        );
        $this->actingAs($user);
        $school = School::where('name','Test school')->first();
        $user->school_id = $school->id;
        $user->save();
        $response = $this->get("/dashboard/schools/$school->id");

        $response->assertStatus(200);
    }

    public function test_school_is_not_rendered_to_authorized_user_in_different_school()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['read school']
        );
        $this->actingAs($user);
        $school = School::where('name','Test school')->first();
        //assign user a different school from the fetched school every time
        $user->school_id = $school->id++;
        $user->save();
        $response = $this->get("/dashboard/schools/$school->id");

        $response->assertNotFound();
    }

    public function test_edit_school_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['update school']
        );
        $this->actingAs($user);
        $school = School::where('name','Test school')->first();
        $response = $this->get("/dashboard/schools/$school->id/edit");

        $response->assertOK();
    }

    public function test_school_settings_redirects_to_edit_school()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['manage school settings']
        );
        $this->actingAs($user);
        $school = School::where('name','Test school')->first();
        $user->school_id = $school->id;
        $user->save();
        $response = $this->get('/dashboard/schools/settings');
        $response->assertRedirect(url("/dashboard/schools/$user->school_id/edit"));
    }

    public function test_unauthorized_user_cannot_update_school()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $school = School::where('name','Test school')->first();
        $response = $this->patch("/dashboard/schools/$school->id");

        $response->assertForbidden();
    }

    public function test_authorized_user_can_update_School()
    {
        $user = User::factory()->create();
        $user->givePermissionTo([
            'update school'
        ]);
        $this->actingAs($user);
        $school = School::where('name','Test school')->first();
        $user->school_id = $school->id;
        $user->save();
        $response = $this->patch("/dashboard/schools/$school->id",['name'=>'Test school 2','address' => 'something street', 'initials' => 'TS2']);

        $this->assertEquals('Test school 2',$school->fresh()->name);
    }

    public function test_that_unauthorized_user_cannot_delete_school()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $school = School::where('name','Test school 2')->first();
        $response = $this->delete("/dashboard/schools/$school->id");

        $response->assertForbidden();
    }

    public function test_that_unauthorized_user_cannot_delete_School_if_it_is_their_current_school()
    {
        $user = User::factory()->create();
        $user->givePermissionTo([
            'delete school'
        ]);
        $this->actingAs($user);
        $school = School::where('name','Test school 2')->first();
        $user->school_id = $school->id;
        $user->save();
        $response = $this->delete("/dashboard/schools/$school->id");

        $this->assertNotNull($school->fresh());
    }

    public function test_user_cannot_delete_school_with_users_in_it()
    {
        $user = User::factory()->create();
        $user->givePermissionTo([
            'delete school'
        ]);
        $this->actingAs($user);

        $school = School::where('name','Test school 2')->first();
        //user id must always not equal to school id 
        $user->school_id = $school->id++;
        $user->save();
        $response = $this->delete("/dashboard/schools/$school->id");

        $this->assertNotNull($school->fresh());
    }

    public function test_user_can_delete_School_with_no_users()
    {
        //get school and users
        $school = School::where('name','Test school 2')->first();
        $userIds = $school->users->pluck('id');
        //delete all users
        User::destroy($userIds);
        $user = User::factory()->create();
        $user->givePermissionTo([
            'delete school'
        ]);
        $this->actingAs($user);
        //user id must always not equal to school id 
        $response = $this->delete("/dashboard/schools/$school->id");
        
        $this->assertNull($school->fresh());
    }
}
