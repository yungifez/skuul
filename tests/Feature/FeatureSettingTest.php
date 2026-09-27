<?php

namespace Tests\Feature;

use App\Enums\AuditAction;
use App\Enums\Feature;
use App\Livewire\Layouts\Menu;
use App\Livewire\ManageSchoolFeatures;
use App\Models\AuditEvent;
use App\Models\GraduationPlan;
use App\Models\Program;
use App\Models\School;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A school decides which parts of the application it uses.
 */
class FeatureSettingTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_feature_is_on_unless_a_school_says_otherwise(): void
    {
        $this->assertTrue(app(FeatureManager::class)->enabled(Feature::Attendance));
    }

    public function test_ranking_starts_off(): void
    {
        $this->assertFalse(app(FeatureManager::class)->enabled(Feature::Ranking));
    }

    public function test_a_school_can_turn_a_feature_off(): void
    {
        $this->authorized_user([]);
        $features = app(FeatureManager::class);

        $features->disable(Feature::Attendance);

        $this->assertFalse($features->enabled(Feature::Attendance));
        $this->assertTrue($features->disabled(Feature::Attendance));
    }

    public function test_one_school_does_not_decide_for_another(): void
    {
        $this->authorized_user([]);
        $other = School::factory()->create();
        $features = app(FeatureManager::class);

        $features->disable(Feature::Attendance);

        $this->assertTrue($features->enabled(Feature::Attendance, $other));
    }

    public function test_a_school_setting_beats_the_platform_setting(): void
    {
        $this->authorized_user([]);
        $features = app(FeatureManager::class);
        $features->disable(Feature::Portal, school: null);

        $features->enable(Feature::Portal, $this->workingSchool());

        $this->assertTrue($features->enabled(Feature::Portal, $this->workingSchool()));
    }

    public function test_a_feature_carries_its_own_settings(): void
    {
        $this->authorized_user([]);
        $features = app(FeatureManager::class);

        $features->enable(Feature::Attendance, config: ['registers' => ['daily']]);

        $this->assertSame(['daily'], $features->config(Feature::Attendance, 'registers'));
        $this->assertSame('none', $features->config(Feature::Attendance, 'missing', 'none'));
    }

    public function test_turning_a_feature_on_or_off_is_written_to_the_audit_log(): void
    {
        $this->authorized_user([]);
        $features = app(FeatureManager::class);

        $setting = $features->disable(Feature::Discipline);

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::FeatureDisabled)->forSubject($setting)->first());

        $features->enable(Feature::Discipline);

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::FeatureEnabled)->first());
    }

    public function test_the_helper_answers_for_the_working_school(): void
    {
        $this->authorized_user([]);
        app(FeatureManager::class)->disable(Feature::Events);

        $this->assertFalse(feature_enabled(Feature::Events));
        $this->assertTrue(feature_enabled(Feature::Attendance));
    }

    public function test_a_route_of_a_disabled_feature_is_hidden(): void
    {
        Route::middleware(['web', 'auth', 'feature:attendance'])
            ->get('/test-attendance', fn (): string => 'register');

        $actor = $this->authorized_user([]);

        $actor->get('/test-attendance')->assertSuccessful();

        app(FeatureManager::class)->disable(Feature::Attendance);

        $actor->get('/test-attendance')->assertNotFound();
    }

    public function test_every_feature_reports_an_answer(): void
    {
        $answers = app(FeatureManager::class)->all();

        $this->assertCount(count(Feature::cases()), $answers);
        $this->assertArrayHasKey('attendance', $answers);
    }

    public function test_the_sidebar_does_not_repeat_feature_setting_queries(): void
    {
        $this->authorized_user([]);
        $featureQueries = 0;

        DB::listen(function (QueryExecuted $query) use (&$featureQueries): void {
            if (str_contains($query->sql, 'feature_settings')) {
                $featureQueries++;
            }
        });

        Livewire::test(Menu::class);

        $this->assertLessThanOrEqual(2, $featureQueries);
    }

    public function test_a_school_administrator_can_manage_optional_school_tools(): void
    {
        $actor = $this->authorized_user(['manage school settings']);

        $actor->get(route('schools.features.edit'))
            ->assertOk()
            ->assertSee('Choose the tools your school uses')
            ->assertSeeLivewire(ManageSchoolFeatures::class);

        Livewire::test(ManageSchoolFeatures::class)
            ->set('enabled.attendance', false)
            ->assertDispatched('school-features-changed')
            ->assertDispatched('status-message', type: 'success', message: 'Attendance turned off.');

        $this->assertFalse(app(FeatureManager::class)->enabled(Feature::Attendance));
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::FeatureDisabled)->first());
        $this->assertDatabaseHas('feature_settings', ['school_id' => $this->workingSchool()->id, 'feature' => 'attendance', 'enabled' => false, 'updated_by' => auth()->id()]);
    }

    public function test_a_school_turns_boarding_on_and_the_sidebar_follows(): void
    {
        $this->authorized_user(['manage school settings', 'read boarding']);

        Livewire::test(Menu::class)->assertDontSee(route('dormitories.index'));

        Livewire::test(ManageSchoolFeatures::class)->set('enabled.boarding', true);

        $this->assertTrue(app(FeatureManager::class)->enabled(Feature::Boarding));
        app()->forgetInstance(FeatureManager::class);
        Livewire::test(Menu::class)->dispatch('school-features-changed')->assertSee(route('dormitories.index'));
    }

    public function test_an_unknown_tool_is_ignored(): void
    {
        $this->authorized_user(['manage school settings']);

        Livewire::test(ManageSchoolFeatures::class)
            ->set('enabled.not-a-feature', true)
            ->assertSet('enabled', app(FeatureManager::class)->all());
    }

    public function test_the_feature_screen_groups_the_tools_and_says_what_each_one_does(): void
    {
        $actor = $this->authorized_user(['manage school settings']);
        app(FeatureManager::class)->disable(Feature::Discipline);

        $response = $actor->get(route('schools.features.edit'))->assertOk();

        foreach (array_keys(Feature::grouped()) as $group) {
            $response->assertSee($group);
        }

        $response->assertSee(Feature::Wellbeing->description())
            ->assertSee('8 of 12 tools are on')
            ->assertDontSee('What this screen changes')
            ->assertDontSee('This tool starts off');
    }

    public function test_a_school_can_hide_graduation_and_programme_tools_without_deleting_them(): void
    {
        $actor = $this->authorized_user(['read graduation plan', 'read program']);
        $plan = GraduationPlan::create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Senior diploma',
        ]);
        $program = Program::create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Robotics club',
        ]);

        app(FeatureManager::class)->disable(Feature::GraduationPlans, $this->workingSchool()->id);
        app(FeatureManager::class)->disable(Feature::Programmes, $this->workingSchool()->id);

        $actor->get(route('graduation-plans.index'))->assertNotFound();
        $actor->get(route('programs.index'))->assertNotFound();

        Livewire::test(Menu::class)
            ->assertDontSee('Graduation plans')
            ->assertDontSee('Programmes');

        $this->assertModelExists($plan);
        $this->assertModelExists($program);
    }

    public function test_an_unauthorized_user_cannot_open_or_change_school_tools(): void
    {
        $this->unauthorized_user()
            ->get(route('schools.features.edit'))
            ->assertForbidden();

        Livewire::test(ManageSchoolFeatures::class)->assertForbidden();

        $this->assertTrue(app(FeatureManager::class)->enabled(Feature::Attendance));
    }
}
