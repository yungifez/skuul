<?php

namespace Tests\Feature;

use App\Enums\AcademicPeriodStatus;
use App\Enums\InstructionalModel;
use App\Models\AcademicYear;
use App\Models\InstructionalModelSetting;
use App\Services\School\SchoolSetupChecklist;
use App\Traits\FeatureTestTrait;
use DateTimeInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolSetupChecklistTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_year_that_has_not_started_asks_for_a_teaching_approach(): void
    {
        $this->workOn($this->year(now()->addMonth(), AcademicPeriodStatus::Scheduled));

        $step = $this->teachingStep();

        $this->assertFalse($step['complete']);
        $this->assertSame('No teaching approach has been chosen for the current school year.', $step['reason']);
    }

    public function test_a_chosen_teaching_approach_completes_the_step(): void
    {
        $year = $this->year(now()->addMonth(), AcademicPeriodStatus::Scheduled);
        InstructionalModelSetting::create(['school_id' => $year->school_id, 'academic_year_id' => $year->id, 'model' => InstructionalModel::Hybrid]);
        $this->workOn($year);

        $this->assertTrue($this->teachingStep()['complete']);
    }

    public function test_a_school_that_sets_up_mid_year_is_not_asked_for_a_teaching_approach_it_cannot_give(): void
    {
        $this->workOn($this->year(now()->subMonth(), AcademicPeriodStatus::Open));

        $step = $this->teachingStep();

        $this->assertTrue($step['complete']);
        $this->assertSame('', $step['reason']);
    }

    public function test_a_draft_year_whose_first_day_has_passed_is_not_asked_either(): void
    {
        $this->workOn($this->year(now()->subWeek(), AcademicPeriodStatus::Draft));

        $this->assertTrue($this->teachingStep()['complete']);
    }

    private function year(DateTimeInterface $startsOn, AcademicPeriodStatus $status): AcademicYear
    {
        return AcademicYear::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'starts_on' => $startsOn,
            'ends_on' => now()->addMonths(9),
            'status' => $status,
        ]);
    }

    private function workOn(AcademicYear $year): void
    {
        academic_period_context()->setAcademicYear($year, remember: false);
    }

    /**
     * @return array{key: string, title: string, description: string, reason: string, complete: bool, required: bool, group: string, url: string, action: string}
     */
    private function teachingStep(): array
    {
        $items = app(SchoolSetupChecklist::class)->for($this->workingSchool())['items'];

        return collect($items)->firstWhere('key', 'instructional_model');
    }
}
