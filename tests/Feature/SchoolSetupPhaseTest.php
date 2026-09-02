<?php

namespace Tests\Feature;

use App\Enums\SchoolSetupPhaseStatus;
use App\Models\AcademicYear;
use App\Models\User;
use App\Services\School\SchoolSetupChecklist;
use App\Services\School\SchoolSetupPhaseService;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolSetupPhaseTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_required_setup_becomes_a_ready_phase(): void
    {
        $school = $this->workingSchool();
        $this->completeChecklist();

        $state = app(SchoolSetupPhaseService::class)->for($school);

        $this->assertSame(SchoolSetupPhaseStatus::Ready, $state['phase']->status);
        $this->assertTrue($state['show_ready_notice']);
        $this->assertTrue($state['show_dashboard_card']);
        $this->assertDatabaseHas('school_setup_phases', [
            'school_id' => $school->id,
            'phase_key' => 'daily-work-v1:academic-year-none',
            'status' => SchoolSetupPhaseStatus::Ready->value,
        ]);
    }

    public function test_acknowledging_a_ready_phase_hides_it_until_setup_is_needed_again(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user(['manage school settings'], $school);
        /** @var User $actor */
        $actor = auth()->user();
        $this->completeChecklist(3);
        $service = app(SchoolSetupPhaseService::class);

        $service->for($school);
        $this->assertTrue($service->acknowledge($school, $actor));

        $state = $service->for($school);

        $this->assertSame(SchoolSetupPhaseStatus::Acknowledged, $state['phase']->status);
        $this->assertFalse($state['show_ready_notice']);
        $this->assertFalse($state['show_dashboard_card']);
        $this->assertDatabaseHas('school_setup_phases', [
            'school_id' => $school->id,
            'status' => SchoolSetupPhaseStatus::Acknowledged->value,
            'acknowledged_by' => $actor->id,
        ]);
        $this->assertDatabaseHas('audit_events', [
            'school_id' => $school->id,
            'actor_id' => $actor->id,
            'action' => 'school_setup.acknowledged',
        ]);
    }

    public function test_a_setup_regression_reopens_the_acknowledged_phase(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user(['manage school settings'], $school);
        /** @var User $actor */
        $actor = auth()->user();
        $this->mockChecklist([
            ['academicYear' => null, 'required_remaining' => 0],
            ['academicYear' => null, 'required_remaining' => 0],
            ['academicYear' => null, 'required_remaining' => 1],
        ]);
        $service = app(SchoolSetupPhaseService::class);

        $service->for($school);
        $service->acknowledge($school, $actor);
        $state = $service->for($school);

        $this->assertSame(SchoolSetupPhaseStatus::InProgress, $state['phase']->status);
        $this->assertTrue($state['show_dashboard_card']);
        $this->assertFalse($state['show_ready_notice']);
    }

    public function test_a_new_academic_year_gets_a_new_setup_phase(): void
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $this->mockChecklist([
            ['academicYear' => null, 'required_remaining' => 0],
            ['academicYear' => $academicYear, 'required_remaining' => 0],
        ]);
        $service = app(SchoolSetupPhaseService::class);

        $service->for($school);
        $state = $service->for($school);

        $this->assertSame(SchoolSetupPhaseStatus::Ready, $state['phase']->status);
        $this->assertSame('daily-work-v1:academic-year-'.$academicYear->id, $state['phase_key']);
        $this->assertCount(2, $school->setupPhases()->get());
    }

    public function test_a_user_without_school_settings_permission_cannot_acknowledge_setup(): void
    {
        $school = $this->workingSchool();

        $this->unauthorized_user($school)
            ->post(route('schools.setup.acknowledge'))
            ->assertForbidden();
    }

    private function completeChecklist(int $calls = 1): void
    {
        $this->mockChecklist(array_fill(0, $calls, ['academicYear' => null, 'required_remaining' => 0]));
    }

    /**
     * @param  list<array{academicYear: null, required_remaining: int}>  $states
     */
    private function mockChecklist(array $states): void
    {
        $checklist = $this->createMock(SchoolSetupChecklist::class);
        $checklist->expects($this->exactly(count($states)))
            ->method('for')
            ->willReturnOnConsecutiveCalls(...$states);
        $this->app->instance(SchoolSetupChecklist::class, $checklist);
    }
}
