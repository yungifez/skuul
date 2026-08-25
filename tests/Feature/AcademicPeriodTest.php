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

    public function test_unauthorized_user_cannot_open_the_academic_calendar_from_the_period_url(): void
    {
        $this->unauthorized_user()
            ->get(route('academic-periods.index'))
            ->assertForbidden();
    }

    public function test_the_period_url_redirects_to_the_current_calendar(): void
    {
        $this->authorized_user(['read academic period'])
            ->get(route('academic-periods.index'))
            ->assertRedirect(route('academic-years.show', current_academic_year()));
    }

    public function test_a_user_can_set_the_working_academic_period_from_the_calendar_flow(): void
    {
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => current_school_id(),
            'academic_year_id' => current_school()->academic_year_id,
        ]);
        $schoolBefore = current_school()->academic_period_id;

        $this->authorized_user(['set academic period'])
            ->post(route('academic-periods.set-academic-period'), ['academic_period_id' => $academicPeriod->id])
            ->assertSessionHas(AcademicPeriodContext::ACADEMIC_PERIOD_SESSION_KEY, $academicPeriod->id);

        $this->assertSame($schoolBefore, current_school()->fresh()->academic_period_id);
    }
}
