<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Section;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectionTest extends TestCase
{
    use RefreshDatabase;
    public function test_view_all_sections_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['read section']
        );
        $this->actingAs($user);
        $response = $this->get('/dashboard/sections');

        $response->assertOk();
    }

    public function test_view_all_sections_cannot_be_rendered_to_unauthorized_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/dashboard/sections');

        $response->assertForbidden();
    }

    public function test_view_section_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['read section']
        );
        $this->actingAs($user);
        $response = $this->get('/dashboard/sections/1');

        $response->assertOk();
    }

    public function test_view_section_cannot_be_rendered_to_unauthorized_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/dashboard/sections/1');

        $response->assertForbidden();
    }

    public function test_create_section_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['create section']
        );
        $this->actingAs($user);
        $response = $this->get('/dashboard/sections/create');

        $response->assertOk();
    }

    public function test_create_section_cannot_be_rendered_to_unauthorized_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/dashboard/sections/create');

        $response->assertForbidden();
    }

    public function test_edit_section_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['update section']
        );
        $this->actingAs($user);
        $response = $this->get('/dashboard/sections/1/edit');

        $response->assertOk();
    }

    public function test_edit_section_cannot_be_rendered_to_unauthorized_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/dashboard/sections/1/edit');

        $response->assertForbidden();
    }

    public function test_user_can_create_section()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['create section']
        );
        $this->actingAs($user);
        $response = $this->post('/dashboard/sections', ['name' => 'Test section', 'my_class_id' => '1']);
        $section = Section::where('name','Test section')->first();
        
        $this->assertModelExists($section);
    }

    public function test_user_cannot_create_section()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/dashboard/sections', ['name' => 'Test section 2', 'my_class_id' => '1']);
        $section = Section::where('name','Test section 2')->get();

        $this->assertEquals(0, $section->count());
    }

    public function test_user_can_update_section()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['update section']
        );
        $this->actingAs($user);
        $response = $this->put('/dashboard/sections/1', ['name' => 'Test section', 'initials' => 'TS']);
        $section = Section::where('name','Test section')->get();

        $this->assertEquals(1, $section->count());
    }

    public function test_user_cannot_update_section()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->put('/dashboard/sections/1', ['name' => 'Test section 2', 'initials' => 'TS']);
        $section = Section::where('name','Test section 2')->get();

        $this->assertEquals(0, $section->count());
    }

    public function test_user_cannot_delete_section_with_users()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['delete section']
        );
        $this->actingAs($user);
        $response = $this->delete('/dashboard/sections/1');
        $section = Section::where('id',1)->get();

        $this->assertEquals(1, $section->count());
    }

    public function test_user_cannot_delete_section()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->delete('/dashboard/sections/1');
        $section = Section::where('id',1)->get();

        $this->assertEquals(1, $section->count());
    }

    public function test_user_can_delete_section_with_users()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['delete section']
        );
        $this->actingAs($user);
        $section = Section::factory()->create();
        $response = $this->delete("/dashboard/sections/$section->id");
  
        $this->assertModelMissing($section);
    }
}
