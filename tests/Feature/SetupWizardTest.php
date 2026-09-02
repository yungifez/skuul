<?php

namespace Tests\Feature;

use App\Actions\Academic\SaveAcademicCalendar;
use App\Actions\Organization\GrantOrganizationMembership;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicPeriodType;
use App\Http\Middleware\SetActiveAcademicPeriod;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Organization;
use App\Models\School;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SetupWizardTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_new_academic_year_screen_explains_the_setup_order(): void
    {
        $this->authorized_user(['create academic year'])
            ->get(route('academic-years.create'))
            ->assertSuccessful()
            ->assertSee('Dates and periods')
            ->assertSee('Teaching approach')
            ->assertSee('Review and publish')
            ->assertSee('data-slot="step"', false)
            ->assertSee('data-slot="step-indicator"', false)
            ->assertSee('data-state="active"', false)
            ->assertSee('aria-current="step"', false);
    }

    public function test_draft_academic_year_setup_does_not_require_a_working_year(): void
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'status' => AcademicPeriodStatus::Draft,
        ]);

        academic_period_context()->forget();
        $this->withoutMiddleware(SetActiveAcademicPeriod::class);

        $this->authorized_user(['update academic year'], $school)
            ->get(route('academic-years.setup', $academicYear))
            ->assertSuccessful()
            ->assertSee('Dates and periods')
            ->assertSee('Set up '.$academicYear->getAttribute('name'))
            ->assertSee('Setup saves automatically.')
            ->assertSee('Academic year setup help');
    }

    public function test_incomplete_future_step_returns_to_the_first_incomplete_step(): void
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'status' => AcademicPeriodStatus::Draft,
        ]);

        $this->authorized_user(['update academic year'], $school)
            ->get(route('academic-years.setup', [$academicYear, 'review']))
            ->assertSuccessful()
            ->assertSee('Dates and periods')
            ->assertSee('Set up '.$academicYear->getAttribute('name'));
    }

    public function test_completed_structure_step_points_to_subject_setup(): void
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
        ]);

        $this->authorized_user(['update academic year'], $school)
            ->get(route('academic-years.setup', [$academicYear, 'structure']))
            ->assertSuccessful()
            ->assertSee('Next step: Subjects for this year')
            ->assertSee('Continue to subjects for this year')
            ->assertSee(route('academic-years.setup', [$academicYear, 'subjects']), false);
    }

    public function test_course_offering_setup_accepts_an_explicit_draft_year(): void
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'status' => AcademicPeriodStatus::Draft,
        ]);

        academic_period_context()->forget();
        $this->withoutMiddleware(SetActiveAcademicPeriod::class);

        $this->authorized_user(['create subject'], $school)
            ->get(route('course-offerings.create', ['academic_year_id' => $academicYear->getKey()]))
            ->assertSuccessful()
            ->assertSee($academicYear->getAttribute('name'))
            ->assertSee('Choose what is taught, when, and to whom.')
            ->assertSee('Course offering help');
    }

    public function test_publishing_from_setup_sets_the_school_default_year(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user(['create academic year', 'update academic year'], $school);
        $academicYear = app(SaveAcademicCalendar::class)->save(
            $school,
            Carbon::parse('2030-09-01'),
            Carbon::parse('2031-08-31'),
            [[
                'name' => 'Term 1',
                'type' => AcademicPeriodType::Term->value,
                'starts_on' => '2030-09-01',
                'ends_on' => '2031-08-31',
            ]],
            auth()->user(),
        );

        $this->post(route('academic-years.setup.publish', $academicYear))
            ->assertRedirect(route('academic-years.show', $academicYear));

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'academic_year_id' => $academicYear->id,
        ]);
    }

    public function test_school_setup_explains_the_school_first_sequence(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user(['manage school settings'], $school)
            ->get(route('schools.setup', $school))
            ->assertSuccessful()
            ->assertSee('School details')
            ->assertSee('School language')
            ->assertSee('Academic year')
            ->assertSee('aria-current="step"', false);
    }

    public function test_creating_a_school_redirects_to_quick_setup_and_provisions_language(): void
    {
        $organization = Organization::factory()->create();
        $this->authorized_user(['create school']);
        app(GrantOrganizationMembership::class)->grant(auth()->user(), $organization);

        $response = $this->post(route('schools.store'), [
            'organization_id' => $organization->id,
            'name' => 'Setup school',
            'address' => '123 Setup Street',
            'country' => 'Canada',
            'state' => 'British Columbia',
            'city' => 'Vancouver',
            'postal_code' => 'V6B 1A1',
        ]);

        $school = School::query()->where('name', 'Setup school')->firstOrFail();

        $response->assertRedirect(route('schools.setup', [$school, 'details']));
        $this->assertDatabaseHas('school_operating_profiles', [
            'school_id' => $school->id,
            'preset' => 'home_sections',
        ]);
        $this->assertTrue(auth()->user()->belongsToSchool($school));
    }
}
