<?php

namespace Tests\Feature;

use App\Models\Section;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;

    public function test_view_all_sections_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['read section'])
            ->get('/dashboard/sections')
            ->assertSuccessful();
    }

    public function test_view_all_sections_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/sections')
            ->assertForbidden();
    }

    public function test_view_section_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['read section'])
            ->get('/dashboard/sections/1')
            ->assertSuccessful();
    }

    public function test_view_section_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/sections/1')
            ->assertForbidden();
    }

    public function test_create_section_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['create section'])
            ->get('/dashboard/sections/create')
            ->assertSuccessful();
    }

    public function test_create_section_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/sections/create')
            ->assertForbidden();
    }

    public function test_edit_section_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['update section'])
            ->get('/dashboard/sections/1/edit')
            ->assertSuccessful();
    }

    public function test_edit_section_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/sections/1/edit')
            ->assertForbidden();
    }

    public function test_user_can_create_section()
    {
        $this->authorized_user(['create section'])
            ->post('/dashboard/sections', ['name' => 'Test section', 'my_class_id' => 1]);

        $this->assertDatabaseHas('sections', [
            'name'        => 'Test section',
            'my_class_id' => 1,
        ]);
    }

    public function test_user_cannot_create_section()
    {
        $this->unauthorized_user()
            ->post('/dashboard/sections', ['name' => 'Test section', 'my_class_id' => 1])
            ->assertForbidden();
    }

    public function test_user_can_update_section()
    {
        $section = Section::factory()->create();
        $this->authorized_user(['update section'])
            ->put("/dashboard/sections/$section->id", ['name' => 'Test section', 'my_class_id' => $section->myClass->id])
            ->assertRedirect();

        $this->assertDatabaseHas('sections', [
            'id'          => $section->id,
            'name'        => 'Test section',
            'my_class_id' => $section->myClass->id,
        ]);
    }

    public function test_user_cannot_update_section()
    {
        $section = Section::factory()->create();
        $this->unauthorized_user()
            ->put("/dashboard/sections/$section->id", ['name' => 'Test section', 'my_class_id' => $section->myClass->id])
            ->assertForbidden();

        $this->assertDatabaseMissing('sections', [
            'id'          => $section->id,
            'name'        => 'Test section',
            'my_class_id' => $section->myClass->id,
        ]);
    }

    public function test_user_cannot_delete_section_with_users()
    {
        $this->authorized_user(['delete section'])
            ->delete('/dashboard/sections/1');

        $this->assertDatabaseHas('sections', [
            'id' => 1,
        ]);
    }

    public function test_unauthorized_user_cannot_delete_section()
    {
        $this->unauthorized_user()
            ->delete('/dashboard/sections/1')
            ->assertForbidden();
    }

    public function test_user_can_delete_section_with_users()
    {
        $section = Section::factory()->create();
        $this->authorized_user(['delete section'])
            ->delete("/dashboard/sections/$section->id");

        $this->assertModelMissing($section);
    }
}
