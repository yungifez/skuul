<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\Semester;
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
        $this->assertSame($school->semester_id, current_semester_id());
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

    public function test_changing_the_year_drops_a_semester_of_the_old_year(): void
    {
        $school = $this->workingSchool();
        $year = AcademicYear::factory()->create(['school_id' => $school->id]);
        $semester = Semester::factory()->create([
            'school_id'        => $school->id,
            'academic_year_id' => $school->academic_year_id,
        ]);

        academic_period_context()->setSemester($semester, remember: false);
        academic_period_context()->setAcademicYear($year, remember: false);

        $this->assertNull(current_semester_id());
    }

    public function test_a_semester_outside_the_working_year_is_refused(): void
    {
        $school = $this->workingSchool();
        $otherYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $semester = Semester::factory()->create([
            'school_id'        => $school->id,
            'academic_year_id' => $otherYear->id,
        ]);

        $this->authorized_user(['set semester'])
            ->post('/dashboard/semesters/set', ['semester_id' => $semester->id])
            ->assertSessionMissing(AcademicPeriodContext::SEMESTER_SESSION_KEY);
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
