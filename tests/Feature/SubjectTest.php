<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;
    use WithFaker;

    public function test_unauthorized_user_cannot_see_all_subjects()
    {
        $this->unauthorized_user()
            ->get('/dashboard/subjects')
            ->assertForbidden();
    }

    public function test_authorized_user_can_see_all_subjects()
    {
        $this->authorized_user(['read subject'])
            ->get('/dashboard/subjects')
            ->assertOk();
    }

    public function test_unauthorized_user_cannot_view_create_subject()
    {
        $this->unauthorized_user()
            ->get('/dashboard/subjects/create')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_create_subject()
    {
        $this->authorized_user(['create subject'])
            ->get('/dashboard/subjects/create')
            ->assertOk();
    }

    public function test_unauthorized_user_cannot_create_subject()
    {
        $name = $this->faker()->name;
        $this->unauthorized_user()
            ->post('/dashboard/subjects', [
                'name'        => $name,
                'short_name'  => 'TS',
                'my_class_id' => 1,
                'school_id'   => 1,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('subjects', [
            'name'  => $name,
        ]);
    }

    public function test_authorized_user_can_create_subject()
    {
        $name = $this->faker()->name;

        $this->authorized_user(['create subject'])
             ->post('/dashboard/subjects', [
                 'name'        => $name,
                 'short_name'  => 'TS',
                 'my_class_id' => 1,
                 'school_id'   => 1,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('subjects', [
            'name' => $name,
        ]);
    }

    public function test_unauthorized_user_cannot_view_edit_subject()
    {
        $this->unauthorized_user()
            ->get('/dashboard/subjects/1/edit')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_edit_subject()
    {
        $this->authorized_user(['update subject'])
            ->get('/dashboard/subjects/1/edit')
            ->assertOk();
    }

    public function test_unauthorized_user_cannot_update_subject()
    {
        $subject = Subject::factory()->create();
        $name = $this->faker->name;
        $this->unauthorized_user()
            ->patch("/dashboard/subjects/$subject->id", [
                'name'        => $name,
                'short_name'  => 'TS2',
                'my_class_id' => 1,
                'school_id'   => 1,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('subjects', [
            'id'    => $subject->id,
            'name'  => $name,
        ]);
    }

    public function test_authorized_user_can_update_subject()
    {
        $subject = Subject::factory()->create();
        $name = $this->faker()->name;
        $this->authorized_user(['update subject'])
            ->patch("/dashboard/subjects/$subject->id", [
                'name'        => $name,
                'short_name'  => 'TS2',
                'my_class_id' => 1,
                'school_id'   => 1,
            ])->assertRedirect();

        $this->assertEquals($name, $subject->fresh()->name);
    }

    public function test_unauthorized_user_cannot_delete_subject()
    {
        $subject = Subject::factory()->create();
        $this->unauthorized_user()
            ->delete("/dashboard/subjects/$subject->id")
            ->assertForbidden();

        $this->assertModelExists($subject);

        $this->assertNotSoftDeleted($subject);
    }

    public function test_authorized_user_can_delete_subject()
    {
        $subject = Subject::factory()->create();
        $this->authorized_user(['delete subject'])
            ->delete("/dashboard/subjects/$subject->id")
            ->assertRedirect();

        $this->assertModelExists($subject);

        $this->assertSoftDeleted($subject);
    }

    public function test_unathorized_user_cannot_view_subject()
    {
        $this->unauthorized_user()
            ->get('/dashboard/subjects/1')
            ->assertForbidden();
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
