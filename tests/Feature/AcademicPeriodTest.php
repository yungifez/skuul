<?php

namespace Tests\Feature;

use App\Models\AcademicPeriod;
use App\Services\Academic\AcademicPeriodContext;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicPeriodTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    // test unauthorized user can not view all academic periods

    public function test_unauthorized_user_cannot_view_all_academic_periods()
    {
        $this->unauthorized_user()
            ->get('/dashboard/academic-periods')
            ->assertForbidden();
    }

    // test authorized user can view all academic periods

    public function test_authorized_user_can_view_all_academic_periods()
    {
        $this->authorized_user(['read academic period'])
            ->get('/dashboard/academic-periods')
            ->assertSuccessful();
    }

    // test unauthorized user can not view an academic period

    public function test_unauthorized_user_cannot_view_an_academic_period()
    {
        $this->unauthorized_user()
            ->get('/dashboard/academic-periods/1')
            ->assertForbidden();
    }

    // test authorized user can view an academic period

    public function test_authorized_user_can_view_an_academic_period()
    {
        // The academic period screen has no detail page yet, so the route returns 404.
        $this->authorized_user(['read academic period'])
            ->get('/dashboard/academic-periods/1')
            ->assertNotFound();
    }

    // test unauthorized user can not create an academic period

    public function test_unauthorized_user_can_not_view_create_academic_period()
    {
        $this->unauthorized_user()
            ->get('/dashboard/academic-periods/create')
            ->assertForbidden();
    }

    // test authorized user can view create academic period

    public function test_the_create_screen_sends_the_user_to_the_inline_form_on_the_index()
    {
        // A period is created inline on the index, so there is no create page.
        $this->authorized_user(['create academic period'])
            ->get('/dashboard/academic-periods/create')
            ->assertRedirect(route('academic-periods.index'));
    }

    // test unauthorized user can not store an academic period

    public function test_unauthorized_user_can_not_store_an_academic_period()
    {
        $this->unauthorized_user()
            ->post('/dashboard/academic-periods')
            ->assertForbidden();
    }

    // test authorized user can store an academic period

    public function test_authorized_user_can_store_an_academic_period()
    {
        $this->authorized_user(['create academic period'])
            ->post('/dashboard/academic-periods', ['name' => 'Test academic period', 'academic_year_id' => 1]);

        $this->assertDatabaseHas('academic_periods', ['name' => 'Test academic period', 'academic_year_id' => 1]);
    }

    // test unauthorized user can not update an academic period

    public function test_unauthorized_user_can_not_update_an_academic_period()
    {
        $academicPeriod = AcademicPeriod::factory()->create();

        $this->unauthorized_user()
            ->put("/dashboard/academic-periods/$academicPeriod->id", ['name' => 'Test academic period', 'academic_year_id' => 1])
            ->assertForbidden();
    }

    // test authorized user can update an academic period

    public function test_authorized_user_can_update_an_academic_period()
    {
        $academicPeriod = AcademicPeriod::factory()->create();

        $this->authorized_user(['update academic period'])
            ->put("/dashboard/academic-periods/$academicPeriod->id", ['name' => 'Test academic period']);
        $this->assertDatabaseHas('academic_periods', [
            'id'   => $academicPeriod->id,
            'name' => 'Test academic period',
        ]);
    }

    // test unauthorized user can not delete an academic period

    public function test_unauthorized_user_can_not_delete_an_academic_period()
    {
        $academicPeriod = AcademicPeriod::factory()->create();

        $this->unauthorized_user()
            ->delete("/dashboard/academic-periods/$academicPeriod->id")
            ->assertForbidden();
    }

    // test authorized user can delete an academic period

    public function test_authorized_user_can_delete_an_academic_period()
    {
        $academicPeriod = AcademicPeriod::factory()->create();

        $this->authorized_user(['delete academic period'])
            ->delete("/dashboard/academic-periods/$academicPeriod->id");

        $this->assertModelMissing($academicPeriod);
    }

    // test unauthorized user can not set academic period

    public function test_unauthorized_user_can_not_set_academic_period()
    {
        $this->unauthorized_user()
            ->post('/dashboard/academic-periods/set', ['academic_period_id' => 1])
            ->assertForbidden();
    }

    // test authorized user can set academic period

    public function test_authorized_user_can_set_academic_period()
    {
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id'        => current_school_id(),
            'academic_year_id' => current_school()->academic_year_id,
        ]);
        $schoolBefore = current_school()->academic_period_id;

        $this->authorized_user(['set academic period'])
            ->post('/dashboard/academic-periods/set', ['academic_period_id' => $academicPeriod->id])
            ->assertSessionHas(AcademicPeriodContext::ACADEMIC_PERIOD_SESSION_KEY, $academicPeriod->id);

        // The working academic period belongs to the request, so the school row does not move.
        $this->assertSame($schoolBefore, current_school()->fresh()->academic_period_id);
    }
}
