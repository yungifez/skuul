<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MyClass;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClassTest extends TestCase
{
    public function test_view_all_classes_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['read class']
        );
        $this->actingAs($user);
        $response = $this->get('/dashboard/classes');

        $response->assertOk();
    }

    public function test_view_all_classes_cannot_be_rendered_to_unauthorized_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/dashboard/classes');

        $response->assertForbidden();
    }

    public function test_view_class_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['read class']
        );
        $this->actingAs($user);
        $response = $this->get('/dashboard/classes/1');

        $response->assertOk();
    }

    public function test_view_class_cannot_be_rendered_to_unauthorized_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/dashboard/classes/1');

        $response->assertForbidden();
    }

    public function test_create_class_can_be_rendered_to_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['create class']
        );
        $this->actingAs($user);
        $response = $this->get('/dashboard/classes/create');

        $response->assertOk();
    }

    public function test_create_class_cannot_be_rendered_to_unauthorized_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/dashboard/classes/create');

        $response->assertForbidden();
    }

    public function test_authorized_user_can_create_class()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['create class']
        );
        $this->actingAs($user);
        $response = $this->post('/dashboard/classes', ['name' => 'Test class','class_group_id' => '1']);
        $class = MyClass::where('name','Test class')->first();
        
        $this->assertModelExists($class);
    }

    public function test_unauthorized_user_can_not_create_class()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/dashboard/classes', ['name' => 'Test class', 'school_id' => '1']);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_edit_class()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['update class']
        );
        $this->actingAs($user);
        $class = MyClass::factory()->create();
        $response = $this->put("/dashboard/classes/$class->id", ['name' => 'Test class','class_group_id' => '2']);
        
        $this->assertEquals('Test class', $class->fresh()->name) && $this->assertEquals(2, $class->fresh()->class_group_id);
    }

    public function test_unauthorized_user_can_not_edit_class()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $class = MyClass::factory()->create();
        $response = $this->put("/dashboard/classes/$class->id", ['name' => 'Test class','class_group_id' => '2']);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_delete_class()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            ['delete class']
        );
        $this->actingAs($user);
        $class = MyClass::factory()->create();
        $response = $this->delete("/dashboard/classes/$class->id");

        $this->assertEquals(0, $class->fresh());
    }

    public function test_unauthorized_user_can_not_delete_class()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $class = MyClass::factory()->create();
        $response = $this->delete("/dashboard/classes/$class->id");

        $response->assertForbidden();
    }
}
