<?php

namespace Tests\Feature;

use App\Enums\AcademicPeriodStatus;
use App\Enums\Role;
use App\Livewire\SetAcademicPeriod;
use App\Livewire\ShowAcademicYear;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\User;
use App\Services\Academic\AcademicPeriodContext;
use App\Services\AcademicYear\AcademicYearService;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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
            ->assertSee(school_terms('academic_year', 'School years'));
    }

    public function test_the_working_year_picker_lists_published_years_but_not_drafts(): void
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => current_school_id()]);
        $draftYear = AcademicYear::factory()->create(['school_id' => current_school_id(), 'status' => AcademicPeriodStatus::Draft]);

        $this->authorized_user(['read academic year', 'set academic year'])
            ->get('/dashboard/academic-years')
            ->assertOk()
            ->assertSee('id="working-year"', false)
            ->assertSee('value="'.$academicYear->id.'"', false)
            ->assertDontSee('value="'.$draftYear->id.'"', false)
            ->assertDontSee('name="academic_year_id"', false);
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
            ->assertSee('Set up a '.strtolower(school_term('academic_year', 'school year')));
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
        $academicYear = AcademicYear::factory()->create([
            'school_id' => current_school_id(),
            'status' => AcademicPeriodStatus::Draft,
        ]);

        $this->authorized_user(['update academic year'])
            ->get("/dashboard/academic-years/{$academicYear->id}/edit")
            ->assertOk()
            ->assertSee('Save draft')
            ->assertSee('Reporting periods');
    }

    public function test_a_draft_calendar_overview_points_to_the_next_setup_step(): void
    {
        $academicYear = AcademicYear::factory()->create([
            'school_id' => current_school_id(),
            'status' => AcademicPeriodStatus::Draft,
        ]);

        $this->authorized_user(['read academic year', 'update academic year'])
            ->get(route('academic-years.show', $academicYear))
            ->assertOk()
            ->assertSee('Continue setup')
            ->assertSee('Dates and periods')
            ->assertSee('Continue to dates and periods')
            ->assertSee(route('academic-years.setup', [$academicYear, 'calendar']), false);
    }

    public function test_the_calendar_overview_lists_periods_with_their_actions_in_one_menu(): void
    {
        $academicYear = AcademicYear::factory()->create([
            'school_id' => current_school_id(),
            'status' => AcademicPeriodStatus::Open,
        ]);
        $period = AcademicPeriod::factory()->create([
            'school_id' => current_school_id(),
            'academic_year_id' => $academicYear->id,
            'parent_id' => null,
            'status' => AcademicPeriodStatus::Open,
            'starts_on' => null,
            'ends_on' => null,
        ]);

        $this->authorized_user(['read academic year', 'update academic period', 'close academic period', 'set academic period'])
            ->get(route('academic-years.show', $academicYear))
            ->assertOk()
            ->assertSee('aria-label="Actions for '.$period->displayName.'"', false)
            ->assertSee(route('academic-periods.edit', $period), false)
            ->assertSee('beginClosingPeriod('.$period->id.')', false)
            ->assertSee('No dates')
            ->assertDontSee('Reporting boundaries drive gradebooks')
            ->assertDontSee('Working '.strtolower(school_term('period', 'academic period')).'</');
    }

    public function test_the_calendar_overview_sorts_its_exam_list(): void
    {
        // The overview of the working calendar carries the period switcher as
        // a child component, so this also covers the switcher's root element.
        $this->authorized_user(['read academic year', 'read exam', 'set academic period']);

        $academicYear = current_academic_year();

        $this->assertNotNull($academicYear);

        Livewire::test(ShowAcademicYear::class, ['academicYear' => $academicYear])
            ->call('updateTable', ['sort' => ['key' => 'name', 'direction' => 'desc']])
            ->assertOk();
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
        $this->unauthorized_user();

        Livewire::test(SetAcademicPeriod::class, ['compact' => true])
            ->set('workingYearId', current_academic_year_id())
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

        $this->authorized_user(['set academic year']);

        Livewire::test(SetAcademicPeriod::class, ['compact' => true])
            ->assertSeeHtml('id="working-year"')
            ->set('workingYearId', $academicYear->id)
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertSame($academicYear->id, session(AcademicPeriodContext::YEAR_SESSION_KEY));
        $this->assertSame($schoolBefore, current_school()->fresh()->academic_year_id);
    }

    public function test_a_school_calendar_of_another_school_cannot_be_set(): void
    {
        $other = School::factory()->create();
        $academicYear = AcademicYear::factory()->create(['school_id' => $other->id]);

        $this->authorized_user(['set academic year']);
        $workingYearBefore = session(AcademicPeriodContext::YEAR_SESSION_KEY);

        Livewire::test(SetAcademicPeriod::class, ['compact' => true])
            ->set('workingYearId', $academicYear->id)
            ->assertHasErrors('workingYearId')
            ->assertNoRedirect();

        $this->assertSame($workingYearBefore, session(AcademicPeriodContext::YEAR_SESSION_KEY));
    }

    public function test_the_working_year_picker_needs_permission_to_change(): void
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['set academic period']);

        Livewire::test(SetAcademicPeriod::class, ['compact' => true])
            ->assertDontSeeHtml('id="working-year"')
            ->call('setWorkingYear', app(AcademicYearService::class))
            ->assertForbidden();
    }

    public function test_a_teacher_can_choose_the_working_calendar_and_term(): void
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $teacher = User::factory()->create();

        school_context()->set($school, remember: false);
        $teacher->assignRole(Role::Teacher);

        $this->actingAsMemberOf($school, $teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Working '.strtolower(school_term('academic_year', 'school year')));

        Livewire::test(SetAcademicPeriod::class, ['compact' => true])
            ->set('workingYearId', $academicYear->id)
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertSame($academicYear->id, session(AcademicPeriodContext::YEAR_SESSION_KEY));

        Livewire::test(SetAcademicPeriod::class)
            ->set('workingPeriodId', $academicPeriod->id)
            ->assertHasNoErrors();

        $this->assertSame($academicPeriod->id, session(AcademicPeriodContext::ACADEMIC_PERIOD_SESSION_KEY));
    }
}
