<?php

namespace Tests\Feature;

use App\Models\Semester;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemesterTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;
    //test unauthorized user can not view all semesters

    public function test_unauthorized_user_cannot_view_all_semesters()
    {
        $this->unauthorized_user()
            ->get('/dashboard/semesters')
            ->assertForbidden();
    }

    //test authorized user can view all semesters

    public function test_authorized_user_can_view_all_semesters()
    {
        $this->authorized_user(['read semester'])
            ->get('/dashboard/semesters')
            ->assertSuccessful();
    }

    //test unauthorized user can not view a semester

    public function test_unauthorized_user_cannot_view_a_semester()
    {
        $this->unauthorized_user()
            ->get('/dashboard/semesters/1')
            ->assertForbidden();
    }

    //test authorized user can view a semester

    public function test_authorized_user_can_view_a_semester()
    {
        $this->authorized_user(['read semester'])
            ->get('/dashboard/semesters/1')
            ->status(404);
    }

    //test unauthorized user can not create a semester

    public function test_unauthorized_user_can_not_view_create_semester()
    {
        $this->unauthorized_user()
            ->get('/dashboard/semesters/create')
            ->assertForbidden();
    }

    //test authorized user can view create semester

    public function test_authorized_user_can_view_create_semester()
    {
        $this->authorized_user(['create semester'])
            ->get('/dashboard/semesters/create')
            ->assertSuccessful();
    }

    //test unauthorized user can not store a semester

    public function test_unauthorized_user_can_not_store_a_semester()
    {
        $this->unauthorized_user()
            ->post('/dashboard/semesters')
            ->assertForbidden();
    }

    //test authorized user can store a semester

    public function test_authorized_user_can_store_a_semester()
    {
        $this->authorized_user(['create semester'])
            ->post('/dashboard/semesters', ['name' => 'Test semester', 'academic_year_id' => 1]);

        $this->assertDatabaseHas('semesters', ['name' => 'Test semester', 'academic_year_id' => 1]);
    }

    //test unauthorized user can not update a semester

    public function test_unauthorized_user_can_not_update_a_semester()
    {
        $semester = Semester::factory()->create();

        $this->unauthorized_user()
            ->put("/dashboard/semesters/$semester->id", ['name' => 'Test semester', 'academic_year_id' => 1])
            ->assertForbidden();
    }

    //test authorized user can update a semester

    public function test_authorized_user_can_update_a_semester()
    {
        $semester = Semester::factory()->create();

        $this->authorized_user(['update semester'])
            ->put("/dashboard/semesters/$semester->id", ['name' => 'Test semester']);
        $this->assertDatabaseHas('semesters', [
            'id'   => $semester->id,
            'name' => 'Test semester',
        ]);
    }

    //test unauthorized user can not delete a semester

    public function test_unauthorized_user_can_not_delete_a_semester()
    {
        $semester = Semester::factory()->create();

        $this->unauthorized_user()
            ->delete("/dashboard/semesters/$semester->id")
            ->assertForbidden();
    }

    //test authorized user can delete a semester

    public function test_authorized_user_can_delete_a_semester()
    {
        $semester = Semester::factory()->create();

        $this->authorized_user(['delete semester'])
            ->delete("/dashboard/semesters/$semester->id");

        $this->assertModelMissing($semester);
    }

    // test unauthorized user can not set semester

    public function test_unauthorized_user_can_not_set_semester()
    {
        $this->unauthorized_user()
            ->post('/dashboard/semesters/set', ['semester_id' => 1])
            ->assertForbidden();
    }

    // test authorized user can set semester

    public function test_authorized_user_can_set_semester()
    {
        $semester = Semester::factory()->create();

        $this->authorized_user(['set semester'])
            ->post('/dashboard/semesters/set', ['semester_id' => $semester->id]);

        $this->assertDatabaseHas('schools', [
            'id'          => auth()->user()->school->id,
            'semester_id' => $semester->id,
        ]);
    }
}
