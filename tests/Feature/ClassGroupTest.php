<?php

namespace Tests\Feature;

use App\Models\ClassGroup;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassGroupTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;

    public function test_view_class_groups_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['read class group'])
            ->get('/dashboard/class-groups')
            ->assertOk();
    }

    public function test_view_class_groups_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/class-groups')
            ->assertForbidden();
    }

    public function test_create_class_groups_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['create class group'])
            ->get('/dashboard/class-groups/create')
            ->assertOk();
    }

    public function test_create_class_groups_can_not_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
      ->get('/dashboard/class-groups/create')
      ->assertForbidden();
    }

    public function test_authorized_user_can_create_class_group()
    {
        $this->authorized_user(['create class group'])
            ->post('/dashboard/class-groups', ['name' => 'test class group']);

        $this->assertDatabaseHas('class_groups', [
            'name'      => 'test class group',
            'school_id' => 1,
        ]);
    }

    public function test_unauthorized_user_can_not_create_class_group()
    {
        $this->unauthorized_user()
         ->post('/dashboard/class-groups', ['name' => 'test class group'])
         ->assertForbidden();
    }

    public function test_edit_class_groups_can_be_rendered_to_authorized_user()
    {
        $classGroup = ClassGroup::factory()->create();
        $this->authorized_user(['read class group'])
         ->get("/dashboard/class-groups/$classGroup->id")
         ->assertOk();
    }

    public function test_edit_class_groups_can_not_be_rendered_to_unauthorized_user()
    {
        $classGroup = ClassGroup::factory()->create();
        $this->unauthorized_user()
         ->get("/dashboard/class-groups/$classGroup->id")
         ->assertForbidden();
    }

    public function test_authorized_user_can_edit_class_group()
    {
        $classGroup = ClassGroup::factory()->create();
        $this->authorized_user(['update class group'])
            ->put("/dashboard/class-groups/$classGroup->id", ['name' => 'test class group']);

        $this->assertDatabaseHas('class_groups', [
            'id'        => $classGroup->id,
            'name'      => 'test class group',
            'school_id' => 1,
        ]);
    }

    public function test_unauthorized_user_can_not_edit_class_group()
    {
        $classGroup = ClassGroup::factory()->create();
        $this->unauthorized_user()
            ->put("/dashboard/class-groups/$classGroup->id", ['name' => 'test class group'])
            ->assertForbidden();
    }

    public function test_authorized_user_can_delete_class_group()
    {
        $classGroup = ClassGroup::factory()->create();
        $this->authorized_user(['delete class group'])
            ->delete("/dashboard/class-groups/$classGroup->id");

        $this->assertModelMissing($classGroup);
    }

    public function test_unauthorized_user_can_not_delete_class_group()
    {
        $classGroup = ClassGroup::factory()->create();
        $this->unauthorized_user()
            ->delete("/dashboard/class-groups/$classGroup->id");

        $this->assertModelExists($classGroup);
    }

    //test authorized user can view class group
    public function test_authorized_user_can_view_class_group()
    {
        $classGroup = ClassGroup::factory()->create();
        $this->authorized_user(['read class group'])
            ->get("/dashboard/class-groups/$classGroup->id")
            ->assertOk();
    }

    //test unauthorized user cant view class group
    public function test_authorized_user_cant_view_class_group()
    {
        $classGroup = ClassGroup::factory()->create();
        $this->unauthorized_user()
            ->get("/dashboard/class-groups/$classGroup->id")
            ->assertForbidden();
    }
}
