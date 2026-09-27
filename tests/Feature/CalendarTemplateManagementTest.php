<?php

namespace Tests\Feature;

use App\Actions\Calendar\SaveCalendarTemplate;
use App\Actions\Organization\GrantOrganizationMembership;
use App\Enums\AcademicPeriodStatus;
use App\Livewire\CalendarTemplateForm;
use App\Models\AcademicYear;
use App\Models\CalendarTemplate;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CalendarTemplateManagementTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_organization_administrator_can_save_a_template_with_periods(): void
    {
        $organization = Organization::factory()->create();
        $this->actingAs($this->organizationAdministrator($organization));

        $this->fillTemplate(Livewire::test(CalendarTemplateForm::class, ['organization' => $organization]))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $template = CalendarTemplate::query()->where('organization_id', $organization->id)->firstOrFail();

        $this->assertSame('Three-term calendar', $template->name);
        $this->assertTrue($template->is_default);
        $this->assertCount(3, $template->periods);
        $this->assertSame('term', $template->periods->first()->type->value);
        $this->assertFalse(Route::has('organizations.calendar-templates.store'));
        $this->assertFalse(Route::has('organizations.calendar-templates.update'));
    }

    public function test_a_sub_period_keeps_its_parent_when_it_sorts_before_it(): void
    {
        $organization = Organization::factory()->create();
        $this->actingAs($this->organizationAdministrator($organization));
        $template = CalendarTemplate::factory()->create(['organization_id' => $organization->id, 'cycle_length_days' => 365]);
        $termOne = $template->periods()->create(['name' => 'Term 1', 'type' => 'term', 'position' => 1, 'start_offset_days' => 0, 'length_days' => 84]);
        $termTwo = $template->periods()->create(['name' => 'Term 2', 'type' => 'term', 'position' => 2, 'start_offset_days' => 112, 'length_days' => 84]);
        $template->periods()->create(['name' => 'Mid-term break', 'type' => 'term', 'position' => 1, 'start_offset_days' => 140, 'length_days' => 7, 'parent_id' => $termTwo->id]);

        Livewire::test(CalendarTemplateForm::class, ['organization' => $organization, 'calendarTemplate' => $template])
            ->assertSet('periods.1.name', 'Term 2')
            ->assertSet('periods.2.name', 'Mid-term break')
            ->assertSet('periods.2.parent_index', '2')
            ->call('save')
            ->assertHasNoErrors();

        $break = $template->periods()->where('name', 'Mid-term break')->firstOrFail();
        $this->assertSame('Term 2', $break->parent?->name);
        $this->assertNotSame($termOne->id, $break->parent_id);
    }

    public function test_a_parent_row_is_read_by_the_number_the_form_showed(): void
    {
        $organization = Organization::factory()->create();

        $template = app(SaveCalendarTemplate::class)->save($organization, [
            'name' => 'Gapped calendar',
            'cycle_length_days' => 365,
            'periods' => [
                ['name' => 'Term 1', 'type' => 'term', 'position' => 1, 'start_offset_days' => 0, 'length_days' => 84],
                ['name' => '', 'type' => 'term'],
                ['name' => 'Term 2', 'type' => 'term', 'position' => 2, 'start_offset_days' => 112, 'length_days' => 84],
                ['name' => 'Half term', 'type' => 'term', 'position' => 1, 'start_offset_days' => 140, 'length_days' => 7, 'parent_index' => 3],
            ],
        ]);

        $this->assertSame('Term 2', $template->periods()->where('name', 'Half term')->firstOrFail()->parent?->name);
    }

    /**
     * @return array<string, array{0: array<int, array<string, string>>, 1: string}>
     */
    public static function periodsThatDoNotFit(): array
    {
        return [
            'past the end of the year' => [[
                ['name' => 'Term 1', 'type' => 'term', 'position' => '1', 'start_offset_days' => '300', 'length_days' => '84', 'parent_index' => ''],
            ], 'ends after the school year ends'],
            'outside its parent' => [[
                ['name' => 'Term 1', 'type' => 'term', 'position' => '1', 'start_offset_days' => '0', 'length_days' => '84', 'parent_index' => ''],
                ['name' => 'Break', 'type' => 'term', 'position' => '1', 'start_offset_days' => '80', 'length_days' => '10', 'parent_index' => '1'],
            ], 'must fall inside row 1'],
            'overlapping a sibling' => [[
                ['name' => 'Term 1', 'type' => 'term', 'position' => '1', 'start_offset_days' => '0', 'length_days' => '84', 'parent_index' => ''],
                ['name' => 'Term 2', 'type' => 'term', 'position' => '2', 'start_offset_days' => '80', 'length_days' => '84', 'parent_index' => ''],
            ], 'Rows 1 and 2 share days'],
        ];
    }

    /**
     * @param  array<int, array<string, string>>  $periods
     */
    #[DataProvider('periodsThatDoNotFit')]
    public function test_a_period_that_does_not_fit_is_refused_with_its_row(array $periods, string $message): void
    {
        $organization = Organization::factory()->create();
        $this->actingAs($this->organizationAdministrator($organization));

        $component = $this->fillTemplate(Livewire::test(CalendarTemplateForm::class, ['organization' => $organization]))
            ->set('periods', array_map(fn (array $period): array => $period + ['label' => ''], $periods))
            ->call('save')
            ->assertHasErrors('periods')
            ->assertNoRedirect();

        $this->assertStringContainsString($message, $component->errors()->first('periods'));
        $this->assertFalse(CalendarTemplate::query()->where('organization_id', $organization->id)->exists());
    }

    public function test_removing_a_row_moves_later_parent_numbers_up(): void
    {
        $organization = Organization::factory()->create();
        $this->actingAs($this->organizationAdministrator($organization));

        Livewire::test(CalendarTemplateForm::class, ['organization' => $organization])
            ->set('periods.2.parent_index', '')
            ->call('addPeriod')
            ->set('periods.3.parent_index', '3')
            ->call('addPeriod')
            ->set('periods.4.parent_index', '1')
            ->call('removePeriod', 0)
            ->assertCount('periods', 4)
            ->assertSet('periods.0.name', 'Term 2')
            ->assertSet('periods.2.parent_index', '2')
            ->assertSet('periods.3.parent_index', '');
    }

    public function test_a_template_of_another_organization_cannot_be_opened_through_this_one(): void
    {
        $organization = Organization::factory()->create();
        $otherTemplate = CalendarTemplate::factory()->create(['organization_id' => Organization::factory()->create()->id]);
        $this->actingAs($this->organizationAdministrator($organization));

        Livewire::test(CalendarTemplateForm::class, ['organization' => $organization, 'calendarTemplate' => $otherTemplate])
            ->assertNotFound();
    }

    public function test_an_organization_administrator_can_generate_a_draft_cycle_for_a_campus(): void
    {
        $organization = Organization::factory()->create();
        $school = School::factory()->create(['organization_id' => $organization->id]);
        $user = $this->organizationAdministrator($organization);
        $template = CalendarTemplate::factory()->create([
            'organization_id' => $organization->id,
            'cycle_length_days' => 365,
        ]);
        $template->periods()->createMany([
            ['name' => 'Term 1', 'type' => 'term', 'position' => 1, 'start_offset_days' => 0, 'length_days' => 84],
            ['name' => 'Term 2', 'type' => 'term', 'position' => 2, 'start_offset_days' => 112, 'length_days' => 84],
        ]);

        $this->actingAs($user)
            ->post(route('organizations.calendar-templates.cycles.store', [$organization, $template]), [
                'school_id' => $school->id,
                'starts_on' => '2030-09-01',
            ])
            ->assertRedirect();

        $year = AcademicYear::query()->where('school_id', $school->id)->where('start_year', 2030)->firstOrFail();

        $this->assertSame(AcademicPeriodStatus::Draft, $year->status);
        $this->assertCount(2, $year->academicPeriods);
    }

    public function test_an_organization_administrator_can_open_the_calendar_template_workspace(): void
    {
        $organization = Organization::factory()->create();
        $user = $this->organizationAdministrator($organization);
        $template = CalendarTemplate::factory()->create(['organization_id' => $organization->id]);
        $template->periods()->create([
            'name' => 'Term 1',
            'type' => 'term',
            'position' => 1,
            'start_offset_days' => 0,
            'length_days' => 84,
        ]);

        $this->actingAs($user)
            ->get(route('organizations.calendar-templates.index', $organization))
            ->assertOk()
            ->assertSee(school_term('academic_year', 'School year').' calendar templates')
            ->assertSee($template->name);

        $this->actingAs($user)
            ->get(route('organizations.calendar-templates.edit', [$organization, $template]))
            ->assertOk()
            ->assertSee('School year periods')
            ->assertSee('Generate a campus school year');
    }

    public function test_an_organization_administrator_can_override_and_restore_a_campus_calendar(): void
    {
        $organization = Organization::factory()->create();
        $school = School::factory()->create(['organization_id' => $organization->id]);
        $user = $this->organizationAdministrator($organization);
        $template = CalendarTemplate::factory()->create(['organization_id' => $organization->id]);

        $this->actingAs($user)
            ->post(route('organizations.calendar-templates.campuses.override', [$organization, $template, $school]), ['reason' => 'This campus uses a trimester schedule.'])
            ->assertRedirect();

        $this->assertSame($template->id, $school->fresh()->calendar_template_id);

        $this->actingAs($user)
            ->delete(route('organizations.calendar-templates.campuses.inherit', [$organization, $template, $school]), ['reason' => 'The campus realigned with the organization.'])
            ->assertRedirect();

        $this->assertNull($school->fresh()->calendar_template_id);
    }

    public function test_a_user_without_organization_scope_cannot_change_a_template(): void
    {
        $organization = Organization::factory()->create();
        $this->actingAs(User::factory()->create());

        Livewire::test(CalendarTemplateForm::class, ['organization' => $organization])->assertForbidden();
    }

    private function fillTemplate(Testable $component): Testable
    {
        return $component
            ->set('name', 'Three-term calendar')
            ->set('description', 'A calendar for three teaching terms.')
            ->set('cycleLengthDays', '365')
            ->set('isDefault', true)
            ->set('autoOpen', true)
            ->set('generateAheadWeeks', '8')
            ->set('remindDaysBefore', '14');
    }

    private function organizationAdministrator(Organization $organization): User
    {
        $user = $this->nonMember();
        app(GrantOrganizationMembership::class)->grant($user, $organization);

        return $user->fresh();
    }
}
