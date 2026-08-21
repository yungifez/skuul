<?php

namespace Tests\Feature;

use App\Enums\AuditAction;
use App\Enums\Feature;
use App\Models\AuditEvent;
use App\Models\School;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
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
}
