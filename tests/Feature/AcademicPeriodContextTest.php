<?php

namespace Tests\Feature;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\School;
use App\Services\Academic\AcademicPeriodContext;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The academic period being worked in lives in the request, not on the school.
 */
class AcademicPeriodContextTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_person_starts_in_the_school_default_period(): void
    {
        $school = $this->workingSchool();

        academic_period_context()->forget();
        academic_period_context()->resolveFor($school->fresh());

        $this->assertSame($school->academic_year_id, current_academic_year_id());
        $this->assertSame($school->academic_period_id, current_academic_period_id());
    }

    public function test_a_remembered_period_wins_over_the_school_default(): void
    {
        $school = $this->workingSchool();
        $year = AcademicYear::factory()->create(['school_id' => $school->id]);

        $this->actingAsMemberOf($school)
            ->withSession([AcademicPeriodContext::YEAR_SESSION_KEY => $year->id])
            ->get('dashboard')
            ->assertSuccessful();

        $this->assertSame($year->id, current_academic_year_id());
    }

    public function test_a_period_of_another_school_is_ignored(): void
    {
        $other = School::factory()->create();
        $theirYear = AcademicYear::factory()->create(['school_id' => $other->id]);
        $school = $this->workingSchool();

        $this->actingAsMemberOf($school)
            ->withSession([AcademicPeriodContext::YEAR_SESSION_KEY => $theirYear->id])
            ->get('dashboard')
            ->assertSuccessful();

        $this->assertSame($school->academic_year_id, current_academic_year_id());
    }

    public function test_changing_the_cycle_drops_a_period_of_the_old_cycle(): void
    {
        $school = $this->workingSchool();
        $year = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $school->academic_year_id,
        ]);

        academic_period_context()->setAcademicPeriod($academicPeriod, remember: false);
        academic_period_context()->setAcademicYear($year, remember: false);

        $this->assertNull(current_academic_period_id());
    }

    public function test_a_period_outside_the_working_cycle_is_refused(): void
    {
        $school = $this->workingSchool();
        $otherYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $otherYear->id,
        ]);

        $this->authorized_user(['set academic period'])
            ->post('/dashboard/academic-periods/set', ['academic_period_id' => $academicPeriod->id])
            ->assertSessionMissing(AcademicPeriodContext::ACADEMIC_PERIOD_SESSION_KEY);
    }

    public function test_two_people_can_work_in_different_periods(): void
    {
        $school = $this->workingSchool();
        $year = AcademicYear::factory()->create(['school_id' => $school->id]);

        // The first person moves to another year.
        $this->authorized_user(['set academic year'])
            ->post('/dashboard/academic-years/set', ['academic_year_id' => $year->id])
            ->assertSessionHas(AcademicPeriodContext::YEAR_SESSION_KEY, $year->id);

        // The second person still opens in the school default.
        academic_period_context()->forget();
        academic_period_context()->resolveFor($school->fresh());

        $this->assertSame($school->academic_year_id, current_academic_year_id());
    }
}
