<?php

namespace Tests\Feature;

use App\Actions\Curriculum\CreateCourseOffering;
use App\Actions\Curriculum\GrantOfferingException;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\InstructionalModel;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\InstructionalModelException;
use App\Models\InstructionalModelSetting;
use App\Models\School;
use App\Models\Subject;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * One subject taught outside the campus model, in writing.
 */
class OfferingExceptionTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_combined_class_is_refused_without_an_exception(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $first, $second] = $this->cycle();

        $this->expectException(InvalidValueException::class);

        app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [$first->id, $second->id],
            RosterMode::CombinedHomeSections,
        );
    }

    public function test_an_exception_lets_one_subject_combine_two_home_sections(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $first, $second] = $this->cycle();

        app(GrantOfferingException::class)->grant(
            $academicYear,
            $subject,
            RosterMode::CombinedHomeSections,
            'One combined music class for the whole level.',
        );

        $offering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [$first->id, $second->id],
            RosterMode::CombinedHomeSections,
        );

        $this->assertSame(RosterMode::CombinedHomeSections, $offering->roster_mode);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::OfferingExceptionGranted)->first());
    }

    public function test_an_exception_does_not_move_the_campus_answer(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear] = $this->cycle();

        app(GrantOfferingException::class)->grant(
            $academicYear,
            $subject,
            RosterMode::CombinedHomeSections,
            'One combined music class for the whole level.',
        );

        $this->assertSame(InstructionalModel::FixedHomeSections, instructional_model($academicYear));
    }

    public function test_an_exception_covers_only_the_subject_it_names(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $first, $second] = $this->cycle();
        $other = Subject::factory()->create(['school_id' => $academicYear->school_id]);

        app(GrantOfferingException::class)->grant(
            $academicYear,
            $subject,
            RosterMode::CombinedHomeSections,
            'One combined music class for the whole level.',
        );

        $this->expectException(InvalidValueException::class);

        app(CreateCourseOffering::class)->create(
            $other,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [$first->id, $second->id],
            RosterMode::CombinedHomeSections,
        );
    }

    public function test_an_exception_limited_to_a_level_does_not_cover_another(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->cycle();
        $otherLevel = AcademicLevel::factory()->create(['school_id' => $academicYear->school_id]);
        $sections = collect([1, 2])->map(fn (): AcademicCycleSection => AcademicCycleSection::factory()->create([
            'school_id' => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $otherLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]));

        app(GrantOfferingException::class)->grant(
            $academicYear,
            $subject,
            RosterMode::CombinedHomeSections,
            'One combined music class for this level only.',
            $academicLevel,
        );

        $this->expectException(InvalidValueException::class);

        app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $otherLevel,
            $sections->pluck('id')->all(),
            RosterMode::CombinedHomeSections,
        );
    }

    public function test_an_exception_is_not_written_twice(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear] = $this->cycle();
        $grant = app(GrantOfferingException::class);

        $grant->grant($academicYear, $subject, RosterMode::CombinedHomeSections, 'One combined music class.');
        $grant->grant($academicYear, $subject, RosterMode::CombinedHomeSections, 'One combined music class.');

        $this->assertSame(1, InstructionalModelException::count());
    }

    public function test_an_exception_the_model_already_allows_is_refused(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear] = $this->cycle();

        $this->expectException(InvalidValueException::class);

        app(GrantOfferingException::class)->grant(
            $academicYear,
            $subject,
            RosterMode::HomeSection,
            'This is what the campus already does.',
        );
    }

    public function test_an_exception_needs_a_reason(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear] = $this->cycle();

        $this->expectException(InvalidValueException::class);

        app(GrantOfferingException::class)->grant($academicYear, $subject, RosterMode::CombinedHomeSections, '  ');
    }

    public function test_a_subject_from_another_campus_is_refused(): void
    {
        $this->authorized_user([]);
        [, $academicYear] = $this->cycle();
        $stray = Subject::factory()->create(['school_id' => School::factory()->create()->id]);

        $this->expectException(InvalidValueException::class);

        app(GrantOfferingException::class)->grant(
            $academicYear,
            $stray,
            RosterMode::CombinedHomeSections,
            'One combined music class for the whole level.',
        );
    }

    public function test_taking_an_exception_back_stops_new_offerings(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $first, $second] = $this->cycle();
        $grant = app(GrantOfferingException::class);
        $exception = $grant->grant($academicYear, $subject, RosterMode::CombinedHomeSections, 'One combined music class.');

        $grant->revoke($exception);

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::OfferingExceptionRevoked)->first());

        $this->expectException(InvalidValueException::class);

        app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [$first->id, $second->id],
            RosterMode::CombinedHomeSections,
        );
    }

    public function test_an_exception_cannot_be_taken_back_twice(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear] = $this->cycle();
        $grant = app(GrantOfferingException::class);
        $exception = $grant->grant($academicYear, $subject, RosterMode::CombinedHomeSections, 'One combined music class.');
        $grant->revoke($exception);

        $this->expectException(InvalidValueException::class);

        $grant->revoke($exception->fresh());
    }

    public function test_staff_can_record_an_exception_from_the_teaching_setup_screen(): void
    {
        $actor = $this->authorized_user(['manage school settings']);
        [$subject, $academicYear] = $this->cycle();

        $actor->get(route('academic-years.instructional-model.edit', $academicYear->id))
            ->assertOk()
            ->assertSee('Subjects taught differently');

        $actor->post(route('academic-years.instructional-model.exceptions.store', $academicYear->id), [
            'subject_id' => $subject->id,
            'roster_mode' => RosterMode::CombinedHomeSections->value,
            'reason' => 'One combined music class for the whole level.',
        ])->assertRedirect(route('academic-years.instructional-model.edit', $academicYear->id));

        $this->assertDatabaseHas('instructional_model_exceptions', [
            'academic_year_id' => $academicYear->id,
            'subject_id' => $subject->id,
            'roster_mode' => RosterMode::CombinedHomeSections->value,
        ]);
    }

    /**
     * Build a cycle that keeps learners in one class group all day.
     *
     * @return array{Subject, AcademicYear, AcademicPeriod, AcademicLevel, AcademicCycleSection, AcademicCycleSection}
     */
    private function cycle(): array
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'status' => AcademicPeriodStatus::Open,
        ]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $subject = Subject::factory()->create(['school_id' => $school->id]);

        InstructionalModelSetting::create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'model' => InstructionalModel::FixedHomeSections,
        ]);

        $sections = collect([1, 2])->map(fn (): AcademicCycleSection => AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]));

        return [$subject, $academicYear, $academicPeriod, $academicLevel, $sections[0], $sections[1]];
    }
}
