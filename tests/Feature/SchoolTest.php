<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Services\School\SchoolContext;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SchoolTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_view_schools_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['read school'])
            ->get('/dashboard/schools')
            ->assertSuccessful();
    }

    public function test_view_schools_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/schools')
            ->assertForbidden();
    }

    public function test_create_schools_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['create school'])
            ->get('/dashboard/schools/create')
            ->assertSuccessful();
    }

    public function test_create_schools_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/schools/create')
            ->assertForbidden();
    }

    public function test_user_can_create_school()
    {
        $this->authorized_user(['create school'])
            ->post('/dashboard/schools', ['name' => 'Test school', 'address' => 'Test address', 'phone' => '+123 456789', 'email' => 'test@email.com', 'initials' => 'TS']);

        $this->assertDatabaseHas('schools', [
            'name' => 'Test school',
            'address' => 'Test address',
            'phone' => '+123 456789',
            'email' => 'test@email.com',
            'initials' => 'TS',
        ]);
    }

    public function test_unauthorized_user_can_not_create_school()
    {
        $this->unauthorized_user()
            ->post('/dashboard/schools', ['name' => 'Test school', 'address' => 'Test address', 'phone' => 'Test phone', 'email' => 'test@email.com', 'initials' => 'TS'])
            ->assertForbidden();
    }

    public function test_show_school_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['read school'])
            ->get('/dashboard/schools/1')
            ->assertSuccessful();
    }

    public function test_show_school_can_be_rendered_to_authorized_user_in_same_school()
    {
        $this->authorized_user(['read school'])
            ->get('/dashboard/schools/1')
            ->assertSuccessful();
    }

    public function test_edit_school_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['update school'])
            ->get('/dashboard/schools/1/edit')
            ->assertSuccessful();
    }

    public function test_school_settings_redirects_to_edit_school()
    {
        $this->authorized_user(['update school'])
            ->get('/dashboard/schools/settings')
            ->assertRedirect('/dashboard/schools/1/edit');
    }

    public function test_unauthorized_user_cannot_update_school()
    {
        $this->unauthorized_user()
            ->put('/dashboard/schools/1', ['name' => 'Test school', 'address' => 'Test address', 'phone' => 'Test phone', 'email' => 'test@email.com', 'initials' => 'TS'])
            ->assertForbidden();
    }

    public function test_authorized_user_can_update_school()
    {
        $school = $this->workingSchool();
        $this->authorized_user(['update school'], $school)
            ->patch("/dashboard/schools/$school->id", ['name' => 'Test school 2', 'address' => 'something street', 'initials' => 'TS2', 'phone' => '123456789', 'email' => 'school@test.com']);

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'name' => 'Test school 2',
            'address' => 'something street',
            'initials' => 'TS2',
            'phone' => '123456789',
            'email' => 'school@test.com',
        ]);
    }

    public function test_a_school_member_cannot_update_another_school()
    {
        $school = School::factory()->create(['name' => 'Other school']);

        $this->authorized_user(['update school'])
            ->patch("/dashboard/schools/$school->id", [
                'name' => 'Changed school',
                'address' => 'changed address',
                'initials' => 'CS',
                'phone' => '123456789',
                'email' => 'changed@example.com',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'name' => 'Other school',
        ]);
    }

    public function test_that_unauthorized_user_cannot_delete_school()
    {
        $school = School::factory()->create();
        $this->unauthorized_user()
            ->delete("/dashboard/schools/$school->id")
            ->assertForbidden();
    }

    public function test_that_unauthorized_user_cannot_delete_school_if_it_is_their_current_school()
    {
        $this->authorized_user(['delete school'])
            ->delete('/dashboard/schools/1');

        $this->assertDatabaseHas('schools', [
            'id' => 1,
        ]);
    }

    public function test_user_cannot_delete_school_with_users_in_it()
    {
        $school = School::factory()->create();
        $this->memberOf($school, User::factory()->create());

        $this->authorized_user(['delete school'], $school)
            ->delete("/dashboard/schools/$school->id");

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
        ]);
    }

    public function test_user_can_delete_school_with_no_users()
    {
        // A school with no users has nobody who could hold a permission in it,
        // so only a platform administrator can empty one out.
        $school = School::factory()->create();

        $this->platform_admin()
            ->delete("/dashboard/schools/$school->id");

        $this->assertModelMissing($school);
    }

    public function test_a_school_member_cannot_delete_another_school()
    {
        $school = School::factory()->create();

        $this->authorized_user(['delete school'])
            ->delete("/dashboard/schools/$school->id")
            ->assertForbidden();

        $this->assertModelExists($school);
    }

    public function test_platform_admin_can_set_the_working_school()
    {
        $user = User::where('email', 'super@admin.com')->first();
        // since factory produces random password, it had to be changed
        $user->password = Hash::make('random-password-lolololololol');
        $user->save();

        $this->actingAs($user);
        $school = School::factory()->create();

        $this->post('/dashboard/schools/set-school', ['school_id' => $school->id])
            ->assertSessionHas(SchoolContext::SESSION_KEY, $school->id);

        // Switching school must never write to the person's record.
        $this->assertSame(1, $user->fresh()->schoolMemberships()->count());
    }

    public function test_a_person_cannot_set_a_school_they_do_not_belong_to()
    {
        $otherSchool = School::factory()->create();

        $this->authorized_user(['read school'])
            ->post('/dashboard/schools/set-school', ['school_id' => $otherSchool->id])
            ->assertForbidden();
    }
}
