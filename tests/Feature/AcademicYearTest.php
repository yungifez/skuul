<?php

namespace Tests\Feature;

use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;
    // test ubauthorized user cannot see academic years

    public function test_unauthorized_user_cannot_see_academic_years()
    {
        $this->unauthorized_user()
            ->get('/dashboard/academic-years')->assertForbidden();
    }

    // test authorized user can see academic years

    public function test_authorized_user_can_see_academic_years()
    {
        $this->authorized_user(['read academic year'])
            ->get('/dashboard/academic-years')->assertOk();
    }

    //test unauthorized user cannot view create academic year

    public function test_unauthorized_user_cannot_create_academic_year()
    {
        $this->unauthorized_user()
            ->post('/dashboard/academic-years')->assertForbidden();
    }

    // test authorized user can view create academic years

    public function test_authorized_user_can_create_academic_years()
    {
        $this->authorized_user(['create academic year'])
            ->get('/dashboard/academic-years/create')->assertOk();
    }

    //test unauthorized user cannot create academic year

    public function test_unauthorized_user_cannot_store_academic_year()
    {
        $this->unauthorized_user()->post('/dashboard/academic-years')->assertForbidden();
    }

    //test authorized user can create academic year

    public function test_user_can_create_academic_year()
    {
        $this->authorized_user(['create academic year'])
            ->post('/dashboard/academic-years', [
                'start_year' => '3030',
                'stop_year'  => '4040',
            ])->assertRedirect();

        $this->assertDatabaseHas('academic_years', [
            'start_year' => '3030',
            'stop_year'  => '4040',
        ]);
    }

    //test unauthorized user cannot view edit academic year

    public function test_unauthorized_user_cannot_edit_academic_year()
    {
        $this->unauthorized_user()->get('/dashboard/academic-years/1/edit')->assertForbidden();
    }

    //test authorized user can view edit academic year

    public function test_authorized_user_can_edit_academic_year()
    {
        $this->authorized_user(['update academic year'])
            ->get('/dashboard/academic-years/1/edit')->assertOk();
    }

    //test unauthorized user cannot update academic year

    public function test_unauthorized_user_cannot_update_academic_year()
    {
        $this->unauthorized_user()
            ->put('/dashboard/academic-years/1')->assertForbidden();
    }

    //test authorized user can update academic year

    public function test_authorized_user_can_update_academic_year()
    {
        $this->authorized_user(['update academic year'])
            ->put('/dashboard/academic-years/1', [
                'start_year' => '3030',
                'stop_year'  => '4040',
            ]);

        $this->assertDatabaseHas('academic_years', [
            'id'         => '1',
            'start_year' => '3030',
            'stop_year'  => '4040',
        ]);
    }

    //test unauthorized user cannot delete academic year

    public function test_unauthorized_user_cannot_delete_academic_year()
    {
        $this->unauthorized_user()
            ->delete('/dashboard/academic-years/1')->assertForbidden();
    }

    //test authorized user can delete academic year

    public function test_authorized_user_can_delete_academic_year()
    {
        $academicYear = \App\Models\AcademicYear::factory()->create();
        $this->authorized_user(['delete academic year'])
            ->delete("/dashboard/academic-years/$academicYear->id");

        $this->assertDatabaseMissing('academic_years', [
            'id' => $academicYear->id,
        ]);
    }

    // test unauthorized user cannot set academic year

    public function test_unauthorized_user_cannot_set_academic_year()
    {
        $this->unauthorized_user()
            ->post('/dashboard/academic-years/set')->assertForbidden();
    }

    // test authorized user can set academic year

    public function test_authorized_user_can_set_academic_year()
    {
        $academicYear = \App\Models\AcademicYear::factory()->create();
        $this->authorized_user(['set academic year'])->post('/dashboard/academic-years/set', [
            'academic_year_id' => $academicYear->id,
        ]);
        $school_id = auth()->user()->school_id;
        $this->assertDatabaseHas('schools', [
            'id'               => $school_id,
            'academic_year_id' => $academicYear->id,
        ]);
    }
}
