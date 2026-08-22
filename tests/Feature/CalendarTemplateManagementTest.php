<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Enums\AcademicPeriodStatus;
use App\Models\AcademicYear;
use App\Models\CalendarTemplate;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTemplateManagementTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_organization_administrator_can_save_a_template_with_periods(): void
    {
        $organization = Organization::factory()->create();
        $user = $this->organizationAdministrator($organization);

        $this->actingAs($user)
            ->post(route('organizations.calendar-templates.store', $organization), $this->templatePayload())
            ->assertRedirect();

        $template = CalendarTemplate::query()->where('organization_id', $organization->id)->firstOrFail();

        $this->assertSame('Three-term calendar', $template->name);
        $this->assertTrue($template->is_default);
        $this->assertCount(3, $template->periods);
        $this->assertSame('term', $template->periods->first()->type->value);
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
            ->assertSee('Academic calendar templates')
            ->assertSee($template->name);

        $this->actingAs($user)
            ->get(route('organizations.calendar-templates.edit', [$organization, $template]))
            ->assertOk()
            ->assertSee('Cycle periods')
            ->assertSee('Generate a campus cycle');
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

        $this->actingAs(User::factory()->create())
            ->post(route('organizations.calendar-templates.store', $organization), $this->templatePayload())
            ->assertForbidden();
    }

    /**
     * @return array<string, mixed>
     */
    private function templatePayload(): array
    {
        return [
            'name' => 'Three-term calendar',
            'description' => 'A calendar for three teaching terms.',
            'cycle_length_days' => 365,
            'is_default' => true,
            'auto_open' => true,
            'generate_ahead_weeks' => 8,
            'remind_days_before' => 14,
            'periods' => [
                ['name' => 'Term 1', 'label' => 'Term 1', 'type' => 'term', 'position' => 1, 'start_offset_days' => 0, 'length_days' => 84],
                ['name' => 'Term 2', 'label' => 'Term 2', 'type' => 'term', 'position' => 2, 'start_offset_days' => 112, 'length_days' => 84],
                ['name' => 'Term 3', 'label' => 'Term 3', 'type' => 'term', 'position' => 3, 'start_offset_days' => 224, 'length_days' => 84],
            ],
        ];
    }

    private function organizationAdministrator(Organization $organization): User
    {
        $user = $this->nonMember();
        app(GrantOrganizationMembership::class)->grant($user, $organization);

        return $user->fresh();
    }
}
