<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchoolTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_view_schools_cannot_be_rendered_to_unauthorized_user()
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

    public function test_create_schools_cannot_be_rendered_to_unauthorized_user()
    {
        $user = User::factory()->create();
     
        $this->actingAs($user);
        $response = $this->get('/dashboard/schools/create');

        $response->assertStatus(403);
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
        
        $this->assertEquals(1, $school->count());
    }

    public function test_unauthorized_user_can_not_create_school()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/dashboard/schools', ['name' => 'Test school', 'address' => 'Test address', 'initials' => 'DS']);

        $response->assertForbidden();
    }

    public function test_show_school_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['read school']
        );
        $this->actingAs($user);
        $school = School::factory()->create();
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
        $school =  School::factory()->create();
        $user->school_id = $school->id;
        $user->save();
        $response = $this->get("/dashboard/schools/$school->id");

        $response->assertStatus(200);
    }

    public function test_edit_school_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['update school']
        );
        $this->actingAs($user);
        $school = School::factory()->create();
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
        $school = School::factory()->create();
        $user->school_id = $school->id;
        $user->save();
        $response = $this->get('/dashboard/schools/settings');
        $response->assertRedirect(url("/dashboard/schools/$user->school_id/edit"));
    }

    public function test_unauthorized_user_cannot_update_school()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $school = School::factory()->create();
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
        $school = School::factory()->create();
        $user->school_id = $school->id;
        $user->save();
        $response = $this->patch("/dashboard/schools/$school->id",['name'=>'Test school 2','address' => 'something street', 'initials' => 'TS2']);

        $this->assertEquals('Test school 2',$school->fresh()->name);
    }

    public function test_that_unauthorized_user_cannot_delete_school()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $school = School::factory()->create();
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
        $school = School::factory()->create();
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

        $school = School::factory()->create();
        //user id must always not equal to school id 
        $user->school_id = $school->id++;
        $user->save();
        $response = $this->delete("/dashboard/schools/$school->id");

        $this->assertNotNull($school->fresh());
    }

    public function test_user_can_delete_School_with_no_users()
    {
        //get school and users
        $school =  School::factory()->create();;
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

    public function test_super_admin_can_set_school()
    {
        $user =  User::where('email','super@admin.com')->first();
        $this->actingAs($user);
        $school = School::factory()->create();
        $response = $this->post("/dashboard/schools/set-school",['school_id' => $school->id]);

        $this->assertEquals($school->id,$user->fresh()->school_id);
    }
}
