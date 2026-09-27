<?php

namespace Tests\Feature;

use App\Actions\Curriculum\RollForwardCourseOfferings;
use App\Enums\AcademicPeriodType;
use App\Enums\AcademicStructureStatus;
use App\Enums\RosterMode;
use App\Livewire\RollOverSubjects;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\School;
use App\Models\Subject;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseOfferingRollForwardTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorized_user([]);
    }

    public function test_it_rolls_a_subject_into_a_later_year_when_the_visible_period_name_matches(): void
    {
        [$source, $target, $sourcePeriod, $targetPeriod, $sourceSection, $subject] = $this->rollForwardContext();
        $this->createOffering($source, $sourcePeriod, $sourceSection, $subject);

        $preview = app(RollForwardCourseOfferings::class)->preview($source, $target);

        $this->assertCount(1, $preview['copies']);
        $this->assertCount(0, $preview['problems']);
        $this->assertSame($targetPeriod->id, $preview['copies']->first()['period']->id);
    }

    public function test_it_ignores_a_changed_display_label_when_matching_reporting_periods(): void
    {
        [$source, $target, $sourcePeriod, $targetPeriod, $sourceSection, $subject] = $this->rollForwardContext(
            sourceLabel: 'Term 1',
            targetLabel: 'Autumn',
        );
        $this->createOffering($source, $sourcePeriod, $sourceSection, $subject);

        $preview = app(RollForwardCourseOfferings::class)->preview($source, $target);

        $this->assertCount(1, $preview['copies']);
        $this->assertCount(0, $preview['problems']);
        $this->assertSame($targetPeriod->id, $preview['copies']->first()['period']->id);
    }

    public function test_it_reports_a_missing_reporting_period_instead_of_creating_a_malformed_offering(): void
    {
        [$source, $target, $sourcePeriod, , $sourceSection, $subject] = $this->rollForwardContext(
            targetPosition: 2,
        );
        $this->createOffering($source, $sourcePeriod, $sourceSection, $subject);

        $preview = app(RollForwardCourseOfferings::class)->preview($source, $target);

        $this->assertCount(0, $preview['copies']);
        $this->assertCount(1, $preview['problems']);
        $this->assertSame(
            'The matching reporting period does not exist in the new year.',
            $preview['problems']->first()['reason'],
        );
    }

    public function test_a_second_section_is_copied_when_only_the_first_already_exists(): void
    {
        [$source, $target, $sourcePeriod, $targetPeriod, $sourceSection, $subject] = $this->rollForwardContext();
        $sourceSectionB = AcademicCycleSection::factory()->create([
            'school_id' => $source->school_id,
            'academic_year_id' => $source->id,
            'academic_level_id' => $sourceSection->academic_level_id,
            'name' => 'B',
            'status' => AcademicStructureStatus::Active,
        ]);
        $targetSectionB = AcademicCycleSection::factory()->create([
            'school_id' => $source->school_id,
            'academic_year_id' => $target->id,
            'academic_level_id' => $sourceSection->academic_level_id,
            'name' => 'B',
            'status' => AcademicStructureStatus::Active,
        ]);
        $this->createOffering($source, $sourcePeriod, $sourceSection, $subject);
        $this->createOffering($source, $sourcePeriod, $sourceSectionB, $subject);
        $targetSectionA = AcademicCycleSection::query()->where('academic_year_id', $target->id)->where('name', 'A')->sole();
        $this->createOffering($target, $targetPeriod, $targetSectionA, $subject);

        $created = app(RollForwardCourseOfferings::class)->rollForward($source, $target);

        $this->assertCount(1, $created);
        $this->assertSame([$targetSectionB->id], $created->first()->cycleSections->modelKeys());
    }

    public function test_the_screen_copies_the_subjects_once_and_returns_to_the_setup(): void
    {
        $this->authorized_user(['create subject', 'read subject']);
        [$source, $target, $sourcePeriod, , $sourceSection, $subject] = $this->rollForwardContext();
        $this->createOffering($source, $sourcePeriod, $sourceSection, $subject);

        $open = fn () => Livewire::withQueryParams(['source_academic_year_id' => $source->id, 'target_academic_year_id' => $target->id])
            ->test(RollOverSubjects::class, ['setup' => true]);
        $firstTab = $open()->assertSee('Copy 1 subject');
        $secondTab = $open();

        $firstTab->call('rollOver')->assertRedirect(route('academic-years.setup', [$target, 'subjects']));
        $secondTab->call('rollOver')->assertRedirect();

        $this->assertSame(1, CourseOffering::query()->where('academic_year_id', $target->id)->count());
        $this->assertSame("Nothing new to copy into {$target->name}.", session('success'));
    }

    public function test_the_screen_ignores_a_year_of_another_school(): void
    {
        $this->authorized_user(['create subject']);
        [, $target] = $this->rollForwardContext();
        $foreignYear = AcademicYear::factory()->create(['school_id' => School::factory()->create()->id]);

        Livewire::withQueryParams(['source_academic_year_id' => $foreignYear->id, 'target_academic_year_id' => $target->id])
            ->test(RollOverSubjects::class)
            ->assertNotSet('sourceAcademicYearId', (string) $foreignYear->id)
            ->set('sourceAcademicYearId', (string) $foreignYear->id)
            ->call('rollOver')
            ->assertHasErrors('sourceAcademicYearId');
    }

    /**
     * @return array{AcademicYear, AcademicYear, AcademicPeriod, AcademicPeriod, AcademicCycleSection, Subject}
     */
    private function rollForwardContext(
        string $sourceLabel = 'Term 1',
        string $targetLabel = 'Term 1',
        int $targetPosition = 1,
    ): array {
        $school = $this->workingSchool();
        $source = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'start_year' => 2030,
            'stop_year' => 2031,
        ]);
        $target = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'start_year' => 2031,
            'stop_year' => 2032,
        ]);
        $sourcePeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $source->id,
            'name' => 'Term 1',
            'label' => $sourceLabel,
            'type' => AcademicPeriodType::Term,
            'position' => 1,
        ]);
        $targetPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $target->id,
            'name' => 'Term 1',
            'label' => $targetLabel,
            'type' => AcademicPeriodType::Term,
            'position' => $targetPosition,
        ]);
        $level = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $sourceSection = AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $source->id,
            'academic_level_id' => $level->id,
            'name' => 'A',
            'status' => AcademicStructureStatus::Active,
        ]);
        AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $target->id,
            'academic_level_id' => $level->id,
            'name' => 'A',
            'status' => AcademicStructureStatus::Active,
        ]);
        $subject = Subject::factory()->create(['school_id' => $school->id]);

        return [$source, $target, $sourcePeriod, $targetPeriod, $sourceSection, $subject];
    }

    private function createOffering(
        AcademicYear $year,
        AcademicPeriod $period,
        AcademicCycleSection $section,
        Subject $subject,
    ): CourseOffering {
        $offering = CourseOffering::factory()->create([
            'school_id' => $year->school_id,
            'academic_year_id' => $year->id,
            'academic_period_id' => $period->id,
            'academic_level_id' => $section->academic_level_id,
            'subject_id' => $subject->id,
            'roster_mode' => RosterMode::HomeSection,
        ]);
        $offering->cycleSections()->attach($section);

        return $offering;
    }
}
