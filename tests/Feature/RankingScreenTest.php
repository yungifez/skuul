<?php

namespace Tests\Feature;

use App\Enums\CohortType;
use App\Enums\Feature;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\CourseOffering;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A position is worked out from the published results when it is asked for,
 * and equal averages share one.
 */
class RankingScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ranking is the one feature that starts off, so a school has to
        // choose it before any of these screens mean anything.
        app(FeatureManager::class)->enable(Feature::Ranking);
    }

    public function test_the_screen_asks_for_a_group_first(): void
    {
        $this->authorized_user(['read ranking']);

        $this->get(route('rankings.index'))
            ->assertOk()
            ->assertSee('Choose a class or group first')
            ->assertSee('Class or group')
            ->assertSee('md:grid-cols-2 lg:grid-cols-[repeat(5,minmax(0,1fr))_auto] lg:items-end');
    }

    public function test_a_group_ranks_learners_across_its_child_classes(): void
    {
        $this->authorized_user(['read ranking']);
        $group = AcademicLevel::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Kindergarten',
            'is_group' => true,
            'parent_id' => null,
        ]);
        $firstClass = AcademicLevel::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Kindergarten 1',
            'parent_id' => $group->id,
        ]);
        $secondClass = AcademicLevel::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Kindergarten 2',
            'parent_id' => $group->id,
        ]);
        $firstSection = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => current_academic_year_id(),
            'academic_level_id' => $firstClass->id,
        ]);
        $secondSection = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => current_academic_year_id(),
            'academic_level_id' => $secondClass->id,
        ]);
        $offering = $this->offering();
        $offering->update(['academic_level_id' => $firstClass->id]);
        $first = $this->learnerIn($firstSection, 'Ada Bell');
        $second = $this->learnerIn($secondSection, 'Grace Ola');
        $this->publishResult($first, $offering, 80);
        $this->publishResult($second, $offering, 60);

        $this->get(route('rankings.index', ['academic_level_id' => $group->id]))
            ->assertOk()
            ->assertSee('Groups · whole-group teaching')
            ->assertSeeInOrder(['Ada Bell', 'Grace Ola']);
    }

    public function test_a_home_group_is_put_in_order(): void
    {
        $this->authorized_user(['read ranking']);
        $section = $this->section();
        $offering = $this->offering();

        $first = $this->learnerIn($section, 'Ada Bell');
        $second = $this->learnerIn($section, 'Grace Ola');
        $this->publishResult($first, $offering, 80);
        $this->publishResult($second, $offering, 60);

        $response = $this->get(route('rankings.index', ['academic_cycle_section_id' => $section->id]));

        $response->assertOk()->assertSee('Ada Bell')->assertSee('Grace Ola');
        $response->assertSeeInOrder(['Ada Bell', 'Grace Ola']);
    }

    public function test_two_equal_averages_share_a_position(): void
    {
        $this->authorized_user(['read ranking']);
        $section = $this->section();
        $offering = $this->offering();

        $this->publishResult($this->learnerIn($section, 'Ada Bell'), $offering, 70);
        $this->publishResult($this->learnerIn($section, 'Grace Ola'), $offering, 70);
        $this->publishResult($this->learnerIn($section, 'Ben Sowa'), $offering, 50);

        $this->get(route('rankings.index', ['academic_cycle_section_id' => $section->id]))
            ->assertOk()
            ->assertSeeInOrder(['1', 'Ada Bell', '1', 'Grace Ola', '3', 'Ben Sowa']);
    }

    public function test_only_the_newest_revision_of_a_result_counts(): void
    {
        $this->authorized_user(['read ranking']);
        $section = $this->section();
        $offering = $this->offering();
        $learner = $this->learnerIn($section, 'Ada Bell');

        $this->publishResult($learner, $offering, 40);
        $this->publishResult($learner, $offering, 90, revision: 2);

        $this->get(route('rankings.index', ['academic_cycle_section_id' => $section->id]))
            ->assertOk()
            ->assertSee('90.00%')
            ->assertDontSee('40.00%');
    }

    public function test_a_group_is_put_in_order(): void
    {
        $this->authorized_user(['read ranking', 'read cohort']);
        $section = $this->section();
        $offering = $this->offering();
        $cohort = Cohort::create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Scholarship group',
            'type' => CohortType::Scholarship,
        ]);

        $inGroup = $this->learnerIn($section, 'Ada Bell');
        $outside = $this->learnerIn($section, 'Grace Ola');
        CohortMember::create(['cohort_id' => $cohort->id, 'student_record_id' => $inGroup->id, 'joined_on' => now()]);
        $this->publishResult($inGroup, $offering, 80);
        $this->publishResult($outside, $offering, 90);

        $this->get(route('rankings.index', ['cohort_id' => $cohort->id]))
            ->assertOk()
            ->assertSee('Ada Bell')
            ->assertSee('80.00%')
            ->assertDontSee('90.00%');
    }

    public function test_a_learner_with_no_published_result_is_not_in_the_order(): void
    {
        $this->authorized_user(['read ranking']);
        $section = $this->section();
        $this->learnerIn($section, 'Ada Bell');

        $this->get(route('rankings.index', ['academic_cycle_section_id' => $section->id]))
            ->assertOk()
            ->assertSee('Nothing to put in order');
    }

    public function test_a_school_that_does_not_rank_has_no_screen(): void
    {
        $this->authorized_user(['read ranking']);
        app(FeatureManager::class)->disable(Feature::Ranking);

        $this->get(route('rankings.index'))->assertNotFound();
    }

    public function test_the_screen_needs_permission(): void
    {
        $this->unauthorized_user();

        $this->get(route('rankings.index'))->assertForbidden();
    }

    /**
     * Make a home group in the working school.
     */
    private function section(): AcademicCycleSection
    {
        return AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => current_academic_year_id(),
        ]);
    }

    /**
     * Make a subject offering in the working school.
     */
    private function offering(): CourseOffering
    {
        return CourseOffering::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => current_academic_year_id(),
            'academic_period_id' => current_academic_period_id(),
        ]);
    }

    /**
     * Enrol one named learner in the home group.
     */
    private function learnerIn(AcademicCycleSection $section, string $name): StudentRecord
    {
        return StudentRecord::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_cycle_section_id' => $section->id,
            'user_id' => User::factory()->create(['name' => $name])->id,
        ]);
    }

    /**
     * Publish one result for a learner.
     */
    private function publishResult(StudentRecord $enrollment, CourseOffering $offering, float $percentage, int $revision = 1): ResultSnapshot
    {
        return ResultSnapshot::create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'course_offering_id' => $offering->id,
            'revision' => $revision,
            'percentage' => $percentage,
            'payload' => [],
            'published_at' => now(),
        ]);
    }
}
