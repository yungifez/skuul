<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ClassGroup;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClassGroupTest extends TestCase
{
   use RefreshDatabase;
   public function test_view_class_groups_can_be_rendered_to_authorized_user()
   {
      $user = User::factory()->create();
      $user->givePermissionTo(
         ['read class group']
      );
      $this->actingAs($user);
      $response = $this->get('/dashboard/class-groups');

      $response->assertOk();
   }

   public function test_view_class_groups_cannot_be_rendered_to_unauthorized_user()
   {
      $user = User::factory()->create();

      $this->actingAs($user);
      $response = $this->get('/dashboard/class-groups');

      $response->assertForbidden();
   }

   public function test_create_class_groups_can_be_rendered_to_authorized_user()
   {
      $user = User::factory()->create();
      $user->givePermissionTo(
         ['create class group']
      );
      $this->actingAs($user);
      $response = $this->get('/dashboard/class-groups/create');

      $response->assertOk();
   }

   public function test_create_class_groups_can_not_be_rendered_to_unauthorized_user()
   {
      $user = User::factory()->create();
      $this->actingAs($user);
      $response = $this->get('/dashboard/class-groups/create');

      $response->assertForbidden();
   }

   public function test_authorized_user_can_create_class_group()
   {
      $user = User::factory()->create();
      $user->givePermissionTo(
         ['create class group']
      );
      $this->actingAs($user);
      $response = $this->post('/dashboard/class-groups', ['name' => 'Test class group', 'school_id' => '1']);
      $classGroup = \App\Models\ClassGroup::where('name','Test class group')->get();

      $this->assertEquals(1, $classGroup->count());
   }

   public function test_unauthorized_user_can_not_create_class_group()
   {
      $user = User::factory()->create();
      $this->actingAs($user);
      $response = $this->post('/dashboard/class-groups', ['name' => 'Test class group', 'school_id' => '1']);
      $classGroup = \App\Models\ClassGroup::where('name','Test class group')->get();

      $response->assertForbidden();
   }

   public function test_edit_class_groups_can_be_rendered_to_authorized_user()
   {
      $user = User::factory()->create();
      $user->givePermissionTo(
         ['update class group']
      );
      $this->actingAs($user);
      $response = $this->get('/dashboard/class-groups/1/edit');

      $response->assertOk();
   }

   public function test_edit_class_groups_can_not_be_rendered_to_unauthorized_user()
   {
      $user = User::factory()->create();
     
      $this->actingAs($user);
      $response = $this->get('/dashboard/class-groups/1/edit');

      $response->assertForbidden();
   }

   public function test_authorized_user_can_edit_class_group()
   {
      $user = User::factory()->create();
      $user->givePermissionTo(
         ['update class group']
      );
      $this->actingAs($user);
      $response = $this->put('/dashboard/class-groups/1', ['name' => 'Test class group', 'school_id' => '1']);
      //get class group with updatesd name
      $classGroup = \App\Models\ClassGroup::where('name','Test class group')->get();

      //check if model exists
      $this->assertEquals(1, $classGroup->count());
   }

   public function test_unauthorized_user_can_not_edit_class_group()
   {
      $user = User::factory()->create();
      $this->actingAs($user);
      $response = $this->put('/dashboard/class-groups/1', ['name' => 'Test class group would fail', 'school_id' => '1']);

      $response->assertForbidden();
   }

   public function test_authorized_user_can_delete_class_group()
   {
      $user = User::factory()->create();
      $user->givePermissionTo(
         ['delete class group']
      );
      $this->actingAs($user);
      $classGroup = ClassGroup::factory()->create();
      $response = $this->delete("/dashboard/class-groups/$classGroup->id");
      $this->assertEquals(0,$classGroup->fresh());
   }

   public function test_unauthorized_user_can_not_delete_class_group()
   {
      $user = User::factory()->create();
      $this->actingAs($user);
      $response = $this->delete('/dashboard/class-groups/1');
      $classGroup = \App\Models\ClassGroup::where('id','1')->get();

      $response->assertForbidden();
   }

   public function test_user_can_view_class_group()
   {
      $user = User::factory()->create();
      $user->givePermissionTo(
         ['read class group']
      );
      $this->actingAs($user);
      $response = $this->get('/dashboard/class-groups/1');

      $response->assertOk();
   }
}
