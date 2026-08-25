<?php

namespace Tests\Feature;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\School;
use App\Services\Academic\AcademicPeriodContext;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_unauthorized_user_cannot_see_school_calendars(): void
    {
        $this->unauthorized_user()
            ->get('/dashboard/academic-years')
            ->assertForbidden();
    }

    public function test_an_authorized_user_can_see_school_calendars(): void
    {
        $this->authorized_user(['read academic year'])
            ->get('/dashboard/academic-years')
            ->assertOk()
            ->assertSee('School calendars');
    }

    public function test_working_calendar_form_uses_the_april_ui_select(): void
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => current_school_id()]);

        $this->authorized_user(['read academic year', 'set academic year'])
            ->get('/dashboard/academic-years')
            ->assertOk()
            ->assertSee('data-slot="select"', false)
            ->assertSee('x-data="select(', false)
            ->assertSee('name="academic_year_id"', false)
            ->assertSee('value="'.$academicYear->id.'"', false);
    }

    public function test_an_unauthorized_user_cannot_open_calendar_setup(): void
    {
        $this->unauthorized_user()
            ->get('/dashboard/academic-years/create')
            ->assertForbidden();
    }

    public function test_an_authorized_user_can_open_calendar_setup(): void
    {
        $this->authorized_user(['create academic year'])
            ->get('/dashboard/academic-years/create')
            ->assertOk()
            ->assertSee('Set up a school calendar');
    }

    public function test_an_unauthorized_user_cannot_edit_a_school_calendar(): void
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => current_school_id()]);

        $this->unauthorized_user()
            ->get("/dashboard/academic-years/{$academicYear->id}/edit")
            ->assertForbidden();
    }

    public function test_an_authorized_user_can_open_a_school_calendar_draft_for_editing(): void
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => current_school_id()]);

        $this->authorized_user(['update academic year'])
            ->get("/dashboard/academic-years/{$academicYear->id}/edit")
            ->assertOk()
            ->assertSee('Edit draft school calendar');
    }

    public function test_an_unauthorized_user_cannot_delete_a_school_calendar(): void
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => current_school_id()]);

        $this->unauthorized_user()
            ->delete("/dashboard/academic-years/{$academicYear->id}")
            ->assertForbidden();
    }

    public function test_an_authorized_user_can_delete_a_school_calendar(): void
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => current_school_id()]);

        $this->authorized_user(['delete academic year'])
            ->delete("/dashboard/academic-years/{$academicYear->id}");

        $this->assertModelMissing($academicYear);
    }

    public function test_an_unauthorized_user_cannot_set_a_working_calendar(): void
    {
        $this->unauthorized_user()
            ->post('/dashboard/academic-years/set')
            ->assertForbidden();
    }

    public function test_an_authorized_user_can_set_a_published_calendar_as_the_working_calendar(): void
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => current_school_id()]);
        AcademicPeriod::factory()->create([
            'school_id' => current_school_id(),
            'academic_year_id' => $academicYear->id,
            'parent_id' => null,
        ]);
        $schoolBefore = current_school()->academic_year_id;

        $this->authorized_user(['set academic year'])
            ->post('/dashboard/academic-years/set', ['academic_year_id' => $academicYear->id])
            ->assertSessionHas(AcademicPeriodContext::YEAR_SESSION_KEY, $academicYear->id);

        $this->assertSame($schoolBefore, current_school()->fresh()->academic_year_id);
    }

    public function test_a_school_calendar_of_another_school_cannot_be_set(): void
    {
        $other = School::factory()->create();
        $academicYear = AcademicYear::factory()->create(['school_id' => $other->id]);

        $this->authorized_user(['set academic year'])
            ->post('/dashboard/academic-years/set', ['academic_year_id' => $academicYear->id])
            ->assertSessionMissing(AcademicPeriodContext::YEAR_SESSION_KEY);
    }
}
