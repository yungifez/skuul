<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
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
}
