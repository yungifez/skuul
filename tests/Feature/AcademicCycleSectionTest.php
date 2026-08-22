<?php

namespace Tests\Feature;

use App\Actions\Curriculum\CreateAcademicCycleSection;
use App\Actions\Curriculum\CreateAcademicLevel;
use App\Actions\Curriculum\RollForwardAcademicCycleSections;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\School;
use App\Models\User;
use App\Policies\AcademicCycleSectionPolicy;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicCycleSectionTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_level_is_a_school_owned_reusable_structure(): void
    {
        $this->authorized_user(['create class']);

        $academicLevel = app(CreateAcademicLevel::class)->create(
            'Primary 4',
            'Class',
            'P4',
        );

        $this->assertSame($this->workingSchool()->id, $academicLevel->school_id);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AcademicLevelCreated)->forSubject($academicLevel)->first());
    }

    public function test_a_cycle_section_belongs_to_one_exact_cycle_and_starts_as_a_draft(): void
    {
        $this->authorized_user(['create section']);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id]);
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        $teacher = $this->teacher();

        $section = app(CreateAcademicCycleSection::class)->create(
            $academicYear,
            $academicLevel,
            'Green',
            ['stream' => 'Morning', 'capacity' => 32],
            homeroomTeacher: $teacher,
        );

        $this->assertSame(AcademicStructureStatus::Draft, $section->status);
        $this->assertSame($academicYear->id, $section->academic_year_id);
        $this->assertSame($academicLevel->id, $section->academic_level_id);
        $this->assertSame($teacher->id, $section->homeroom_teacher_id);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AcademicCycleSectionCreated)->forSubject($section)->first());
    }

    public function test_a_cycle_section_refuses_a_level_from_another_school(): void
    {
        $this->authorized_user(['create section']);
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        $otherSchool = School::factory()->create();
        $otherLevel = AcademicLevel::factory()->create(['school_id' => $otherSchool->id]);

        $this->expectException(InvalidValueException::class);

        app(CreateAcademicCycleSection::class)->create($academicYear, $otherLevel, 'Green');
    }

    public function test_roll_forward_copies_structure_as_drafts_without_reusing_sections(): void
    {
        $this->authorized_user(['create section']);
        $academicLevel = app(CreateAcademicLevel::class)->create('Primary 4');
        $source = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        $target = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        app(CreateAcademicCycleSection::class)->create(
            $source,
            $academicLevel,
            'Green',
            ['stream' => 'Morning', 'capacity' => 32],
        );

        $created = app(RollForwardAcademicCycleSections::class)->rollForward($source, $target);

        $this->assertCount(1, $created);
        $this->assertSame($target->id, $created->sole()->academic_year_id);
        $this->assertSame(AcademicStructureStatus::Draft, $created->sole()->status);
        $this->assertSame(0, app(RollForwardAcademicCycleSections::class)->rollForward($source, $target)->count());
        $this->assertCount(1, AuditEvent::ofAction(AuditAction::AcademicCycleSectionsRolledForward)->get());
    }

    public function test_a_manager_can_create_and_activate_a_cycle_section_from_the_ui(): void
    {
        $actor = $this->authorized_user(['create section', 'read section', 'update section']);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id]);
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);

        $actor->get(route('academic-cycle-sections.create'))->assertOk()->assertSee('Add one home section for one cycle');
        $actor->post(route('academic-cycle-sections.store'), [
            'academic_year_id'  => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'name'              => 'Green',
            'capacity'          => 32,
        ])->assertRedirect();

        /** @var AcademicCycleSection $section */
        $section = AcademicCycleSection::query()
            ->where('academic_year_id', $academicYear->id)
            ->where('academic_level_id', $academicLevel->id)
            ->sole();

        $actor->put(route('academic-cycle-sections.status.update', $section), [
            'status' => AcademicStructureStatus::Active->value,
        ])->assertRedirect();

        $this->assertSame(AcademicStructureStatus::Active, $section->fresh()->status);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AcademicCycleSectionStatusChanged)->forSubject($section)->first());
    }

    public function test_a_school_user_cannot_update_a_cycle_section_from_another_school(): void
    {
        $this->authorized_user(['update section']);
        $otherSchool = School::factory()->create();
        $section = AcademicCycleSection::factory()->create(['school_id' => $otherSchool->id]);

        $this->assertFalse(app(AcademicCycleSectionPolicy::class)->update(auth()->user(), $section));
    }

    private function teacher(): User
    {
        $teacher = $this->memberOf($this->workingSchool());
        $teacher->assignRole(Role::Teacher->value);

        return $teacher;
    }
}
