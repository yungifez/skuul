<?php

namespace Tests\Feature;

use App\Models\MyClass;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;

    public function test_view_all_classes_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['read class'])
            ->get('/dashboard/classes')
            ->assertSuccessful();
    }

    public function test_view_all_classes_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/classes')
            ->assertForbidden();
    }

    public function test_view_class_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['read class'])
            ->get('/dashboard/classes/1')
            ->assertSuccessful();
    }

    public function test_view_class_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/classes/1')
            ->assertForbidden();
    }

    public function test_create_class_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['create class'])
            ->get('/dashboard/classes/create')
            ->assertSuccessful();
    }

    public function test_create_class_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/classes/create')
            ->assertForbidden();
    }

    public function test_authorized_user_can_create_class()
    {
        $this->authorized_user(['create class'])
            ->post('/dashboard/classes', ['name' => 'Test class', 'class_group_id' => '1']);

        $this->assertDatabaseHas('my_classes', [
            'name'           => 'Test class',
            'class_group_id' => '1',
        ]);
    }

    public function test_unauthorized_user_can_not_create_class()
    {
        $this->unauthorized_user()
            ->post('/dashboard/classes', ['name' => 'Test class', 'class_group_id' => '1'])
            ->assertForbidden();
    }

    public function test_authorized_user_can_edit_class()
    {
        $class = MyClass::factory()->create();
        $this->authorized_user(['update class'])
            ->put("/dashboard/classes/$class->id", ['name' => 'Test class', 'class_group_id' => '1']);

        $this->assertDatabaseHas('my_classes', [
            'id'             => $class->id,
            'name'           => 'Test class',
            'class_group_id' => '1',
        ]);
    }

    public function test_unauthorized_user_can_not_edit_class()
    {
        $class = MyClass::factory()->create();
        $this->unauthorized_user()
            ->put("/dashboard/classes/$class->id", ['name' => 'Test class', 'class_group_id' => '1'])
            ->assertForbidden();
    }

    public function test_authorized_user_can_delete_class()
    {
        $class = MyClass::factory()->create();
        $this->authorized_user(['delete class'])
            ->delete("/dashboard/classes/$class->id");

        $this->assertModelExists($class);

        $this->assertSoftDeleted($class);
    }

    public function test_unauthorized_user_can_not_delete_class()
    {
        $class = MyClass::factory()->create();
        $this->unauthorized_user()
            ->delete("/dashboard/classes/$class->id")
            ->assertForbidden();

        $this->assertModelExists($class) && $this->assertNotSoftDeleted($class);
    }
}
