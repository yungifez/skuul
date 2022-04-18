<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_user_cannot_see_all_subjects()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/dashboard/subjects');

        $response->assertForbidden();
    }

    public function test_authorized_user_can_see_all_subjects()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('read subject');
        $this->actingAs($user);
        $response = $this->get('/dashboard/subjects');

        $response->assertOk();
    }

    public function test_unauthorized_user_cannot_view_create_subject()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/dashboard/subjects/create');

        $response->assertForbidden();
    }

    public function test_authorized_user_can_view_create_subject()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['create subject']);
        $this->actingAs($user);
        $response = $this->get('/dashboard/subjects/create');

        $response->assertOk();
    }

    public function test_unauthorized_user_cannot_create_subject()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/dashboard/subjects', [
            'name' => 'Test Subject',
            'short_name' => 'TS',
            'my_class_id' => 1,
            'school_id' => 1,
        ]);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_create_subject()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['create subject']);
        $this->actingAs($user);
        $response = $this->post('/dashboard/subjects', [
            'name' => 'Test Subject',
            'short_name' => 'TS',
            'my_class_id' => 1,
            'school_id' => 1,
        ]);
        $subject = \App\Models\Subject::where('name', 'Test Subject')->first();

        $this->assertModelExists($subject);
    }

    public function test_unauthorized_user_cannot_view_edit_subject()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/dashboard/subjects/1/edit');

        $response->assertForbidden();
    }

    public function test_authorized_user_can_view_edit_subject()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['update subject']);
        $this->actingAs($user);
        $response = $this->get('/dashboard/subjects/1/edit');

        $response->assertOk();
    }

    public function test_unauthorized_user_cannot_update_subject()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->patch('/dashboard/subjects/1', [
            'name' => 'Test Subject 2',
            'short_name' => 'TS2',
            'my_class_id' => 1,
            'school_id' => 1,
        ]);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_update_subject()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['update subject']);
        $this->actingAs($user);
        $subject = \App\Models\Subject::factory()->create();
        $response = $this->patch("/dashboard/subjects/$subject->id", [
            'name' => 'Test Subject 2',
            'short_name' => 'TS2',
            'my_class_id' => 1,
            'school_id' => 1,
        ]);

        $this->assertEquals('Test Subject 2',$subject->fresh()->name);
    }

    public function test_unauthorized_user_cannot_delete_subject()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->delete('/dashboard/subjects/1');

        $response->assertForbidden();
    }

    public function test_authorized_user_can_delete_subject()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['delete subject']);
        $this->actingAs($user);
        $subject = \App\Models\Subject::factory()->create();
        $response = $this->delete("/dashboard/subjects/{$subject->id}");

        $this->assertModelMissing($subject);
    }


    public function test_unathorized_user_cannot_view_subject()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/dashboard/subjects/1');

        $response->assertForbidden();
    }

    // public function test_authorized_user_can_view_subject()
    // {
    //     $user = User::factory()->create();
    //     $user->givePermissionTo(['read subject']);
    //     $this->actingAs($user);
    //     $response = $this->get('/dashboard/subjects/1');

    //     $response->assertOk();
    // }
}
