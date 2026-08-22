<?php

namespace Tests\Feature;

use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The screens a school manager uses to set up academic levels and the home
 * sections of one academic cycle.
 */
class AcademicStructureScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_level_index_separates_reusable_levels_from_yearly_sections(): void
    {
        $actor = $this->authorized_user(['read class', 'create class', 'update class']);
        AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Kestrel Stage', 'label' => 'Class']);

        $actor->get(route('academic-levels.index'))
            ->assertOk()
            ->assertSee('Levels are reusable. Sections are not.')
            ->assertSee('Kestrel Stage')
            ->assertSee(route('academic-levels.create'), false);
    }

    public function test_the_level_index_shows_an_empty_state_and_a_way_in(): void
    {
        $actor = $this->authorized_user(['read class', 'create class'], School::factory()->create());

        $actor->get(route('academic-levels.index'))
            ->assertOk()
            ->assertSee('No academic levels yet')
            ->assertSee('data-resource-create-action="'.route('academic-levels.create').'"', false);
    }

    public function test_the_level_index_filters_by_status(): void
    {
        $actor = $this->authorized_user(['read class']);
        AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Kestrel Stage']);
        AcademicLevel::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'name'      => 'Merlin Stage',
            'status'    => AcademicStructureStatus::Archived,
        ]);

        $actor->get(route('academic-levels.index', ['status' => AcademicStructureStatus::Archived->value]))
            ->assertOk()
            ->assertSee('Merlin Stage')
            ->assertDontSee('Kestrel Stage');
    }

    public function test_the_level_show_screen_reads_in_plain_words(): void
    {
        $actor = $this->authorized_user(['read class']);
        $parent = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Primary']);
        $academicLevel = AcademicLevel::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'name'      => 'Primary 4',
            'label'     => 'Class',
            'parent_id' => $parent->id,
        ]);

        $actor->get(route('academic-levels.show', $academicLevel))
            ->assertOk()
            ->assertSee('What this level is')
            ->assertSee('Local label')
            ->assertSee('Primary')
            ->assertSee('Sits under')
            ->assertSee('No cycle section uses this level yet');
    }

    public function test_a_manager_can_edit_a_level_and_the_change_is_audited(): void
    {
        $actor = $this->authorized_user(['read class', 'update class']);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Primary 4']);

        $actor->get(route('academic-levels.edit', $academicLevel))->assertOk()->assertSee('Edit the reusable level');

        $actor->put(route('academic-levels.update', $academicLevel), [
            'name'     => 'Grade 4',
            'label'    => 'Grade',
            'position' => 4,
        ])->assertRedirect(route('academic-levels.show', $academicLevel));

        $this->assertSame('Grade 4', $academicLevel->fresh()->name);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AcademicLevelUpdated)->forSubject($academicLevel)->first());
    }

    public function test_a_level_is_archived_only_when_no_section_of_it_still_runs(): void
    {
        $actor = $this->authorized_user(['read class', 'update class']);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id]);
        $section = AcademicCycleSection::factory()->create([
            'school_id'         => $this->workingSchool()->id,
            'academic_level_id' => $academicLevel->id,
            'status'            => AcademicStructureStatus::Active,
        ]);

        $actor->from(route('academic-levels.show', $academicLevel))
            ->put(route('academic-levels.status.update', $academicLevel), ['status' => AcademicStructureStatus::Archived->value])
            ->assertSessionHas('danger');

        $this->assertSame(AcademicStructureStatus::Active, $academicLevel->fresh()->status);

        $section->update(['status' => AcademicStructureStatus::Archived]);

        $actor->from(route('academic-levels.show', $academicLevel))
            ->put(route('academic-levels.status.update', $academicLevel), ['status' => AcademicStructureStatus::Archived->value])
            ->assertSessionHas('success');

        $this->assertSame(AcademicStructureStatus::Archived, $academicLevel->fresh()->status);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AcademicLevelStatusChanged)->forSubject($academicLevel)->first());
    }

    public function test_an_archived_level_sends_an_editor_back_with_an_explanation(): void
    {
        $actor = $this->authorized_user(['read class', 'update class']);
        $academicLevel = AcademicLevel::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'status'    => AcademicStructureStatus::Archived,
        ]);

        $actor->get(route('academic-levels.edit', $academicLevel))
            ->assertRedirect(route('academic-levels.show', $academicLevel))
            ->assertSessionHas('danger');
    }

    public function test_a_reader_cannot_reach_the_level_edit_screen(): void
    {
        $actor = $this->authorized_user(['read class']);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id]);

        $actor->get(route('academic-levels.edit', $academicLevel))->assertForbidden();
        $actor->put(route('academic-levels.update', $academicLevel), ['name' => 'Grade 4'])->assertForbidden();
    }

    public function test_the_cycle_section_index_defaults_to_the_cycle_being_worked_in(): void
    {
        $actor = $this->authorized_user(['read section']);
        $school = $this->workingSchool();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $thisCycle = AcademicYear::factory()->create(['school_id' => $school->id, 'start_year' => 2026, 'stop_year' => 2027]);
        $lastCycle = AcademicYear::factory()->create(['school_id' => $school->id, 'start_year' => 2025, 'stop_year' => 2026]);
        $school->forceFill(['academic_year_id' => $thisCycle->id])->save();

        AcademicCycleSection::factory()->create([
            'school_id'         => $school->id, 'academic_year_id' => $thisCycle->id,
            'academic_level_id' => $academicLevel->id, 'name' => 'Green',
        ]);
        AcademicCycleSection::factory()->create([
            'school_id'         => $school->id, 'academic_year_id' => $lastCycle->id,
            'academic_level_id' => $academicLevel->id, 'name' => 'Amber',
        ]);

        $actor->get(route('academic-cycle-sections.index'))
            ->assertOk()
            ->assertSee('Green')
            ->assertDontSee('Amber');

        $actor->get(route('academic-cycle-sections.index', ['academic_year_id' => '']))
            ->assertOk()
            ->assertSee('Green')
            ->assertSee('Amber');
    }

    public function test_the_cycle_section_index_filters_by_level_and_status(): void
    {
        $actor = $this->authorized_user(['read section']);
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $primary = AcademicLevel::factory()->create(['school_id' => $school->id, 'name' => 'Primary 4']);
        $junior = AcademicLevel::factory()->create(['school_id' => $school->id, 'name' => 'Junior 1']);

        AcademicCycleSection::factory()->create([
            'school_id'         => $school->id, 'academic_year_id' => $academicYear->id,
            'academic_level_id' => $primary->id, 'name' => 'Green', 'status' => AcademicStructureStatus::Active,
        ]);
        AcademicCycleSection::factory()->create([
            'school_id'         => $school->id, 'academic_year_id' => $academicYear->id,
            'academic_level_id' => $junior->id, 'name' => 'Amber', 'status' => AcademicStructureStatus::Draft,
        ]);

        $actor->get(route('academic-cycle-sections.index', ['academic_year_id' => $academicYear->id, 'academic_level_id' => $primary->id]))
            ->assertOk()
            ->assertSee('Green')
            ->assertDontSee('Amber');

        $actor->get(route('academic-cycle-sections.index', [
            'academic_year_id' => $academicYear->id,
            'status'           => AcademicStructureStatus::Draft->value,
        ]))
            ->assertOk()
            ->assertSee('Amber')
            ->assertDontSee('>Green<', false);
    }

    public function test_the_create_screen_asks_for_a_level_before_anything_else(): void
    {
        $actor = $this->authorized_user(['create section', 'create class'], School::factory()->create());

        $actor->get(route('academic-cycle-sections.create'))
            ->assertOk()
            ->assertSee('Add an academic level first');
    }

    public function test_the_create_screen_preselects_the_level_it_was_opened_from(): void
    {
        $actor = $this->authorized_user(['create section']);
        $school = $this->workingSchool();
        AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id, 'name' => 'Primary 4']);

        $actor->get(route('academic-cycle-sections.create', ['academic_level_id' => $academicLevel->id]))
            ->assertOk()
            ->assertSee('value="'.$academicLevel->id.'" selected', false)
            ->assertSee('3. Optional details');
    }

    public function test_a_manager_can_edit_a_cycle_section_without_moving_its_cycle(): void
    {
        $actor = $this->authorized_user(['read section', 'update section']);
        $school = $this->workingSchool();
        $teacher = $this->teacher();
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'name'      => 'Kestrel',
            'status'    => AcademicStructureStatus::Active,
        ]);

        $actor->get(route('academic-cycle-sections.edit', $section))
            ->assertOk()
            ->assertSee('The cycle and the level are fixed.');

        $actor->put(route('academic-cycle-sections.update', $section), [
            'name'                => 'Kestrel',
            'room'                => 'Block B, Room 2',
            'capacity'            => 40,
            'homeroom_teacher_id' => $teacher->id,
        ])->assertRedirect(route('academic-cycle-sections.show', $section));

        $section->refresh();
        $this->assertSame('Block B, Room 2', $section->room);
        $this->assertSame(40, $section->capacity);
        $this->assertSame($teacher->id, $section->homeroom_teacher_id);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AcademicCycleSectionUpdated)->forSubject($section)->first());
    }

    public function test_a_repeated_section_name_reads_as_a_message_not_a_crash(): void
    {
        $actor = $this->authorized_user(['create section', 'read section', 'update section']);
        $school = $this->workingSchool();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $taken = ['academic_year_id' => $academicYear->id, 'academic_level_id' => $academicLevel->id, 'name' => 'Osprey'];
        AcademicCycleSection::factory()->create($taken + ['school_id' => $school->id]);

        $actor->from(route('academic-cycle-sections.create'))
            ->post(route('academic-cycle-sections.store'), $taken)
            ->assertRedirect(route('academic-cycle-sections.create'))
            ->assertSessionHasErrors('name');

        // The same name is still free in another level of the same cycle.
        $otherLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $actor->post(route('academic-cycle-sections.store'), [
            'academic_year_id'  => $academicYear->id,
            'academic_level_id' => $otherLevel->id,
            'name'              => 'Osprey',
        ])->assertSessionHasNoErrors();
    }

    public function test_a_cycle_section_can_be_archived_from_its_own_screen(): void
    {
        $actor = $this->authorized_user(['read section', 'update section']);
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'name'      => 'Merlin',
            'status'    => AcademicStructureStatus::Active,
        ]);

        $actor->get(route('academic-cycle-sections.show', $section))->assertOk()->assertSee('Archive');

        $actor->from(route('academic-cycle-sections.show', $section))
            ->put(route('academic-cycle-sections.status.update', $section), ['status' => AcademicStructureStatus::Archived->value])
            ->assertSessionHas('success');

        $this->assertSame(AcademicStructureStatus::Archived, $section->fresh()->status);

        $actor->get(route('academic-cycle-sections.edit', $section))
            ->assertRedirect(route('academic-cycle-sections.show', $section))
            ->assertSessionHas('danger');
    }

    public function test_the_cycle_section_show_screen_says_the_section_serves_one_cycle(): void
    {
        $actor = $this->authorized_user(['read section']);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Primary 4']);
        $section = AcademicCycleSection::factory()->create([
            'school_id'         => $this->workingSchool()->id,
            'academic_level_id' => $academicLevel->id,
            'name'              => 'Green',
            'room'              => 'Block A',
        ]);

        $actor->get(route('academic-cycle-sections.show', $section))
            ->assertOk()
            ->assertSee('One cycle only')
            ->assertSee('This section serves '.$section->academicYear->name)
            ->assertSee('Block A')
            ->assertSee('no learners, no teachers, no attendance');
    }

    public function test_the_roll_forward_screen_reviews_the_copy_before_it_is_made(): void
    {
        $actor = $this->authorized_user(['create section', 'read section']);
        $school = $this->workingSchool();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id, 'name' => 'Primary 4']);
        $source = AcademicYear::factory()->create(['school_id' => $school->id, 'start_year' => 2025, 'stop_year' => 2026]);
        $target = AcademicYear::factory()->create(['school_id' => $school->id, 'start_year' => 2026, 'stop_year' => 2027]);

        AcademicCycleSection::factory()->create([
            'school_id'         => $school->id, 'academic_year_id' => $source->id,
            'academic_level_id' => $academicLevel->id, 'name' => 'Green',
        ]);
        AcademicCycleSection::factory()->create([
            'school_id'         => $school->id, 'academic_year_id' => $source->id,
            'academic_level_id' => $academicLevel->id, 'name' => 'Amber',
        ]);
        AcademicCycleSection::factory()->create([
            'school_id'         => $school->id, 'academic_year_id' => $target->id,
            'academic_level_id' => $academicLevel->id, 'name' => 'Amber',
        ]);

        $actor->get(route('academic-cycle-sections.roll-forward.show', [
            'source_academic_year_id' => $source->id,
            'target_academic_year_id' => $target->id,
        ]))
            ->assertOk()
            ->assertSee('Will be created as drafts')
            ->assertSee('Already in '.$target->name)
            ->assertSee('It never copies')
            ->assertSee('Learners and their placements')
            ->assertSee('Create 1 draft section in '.$target->name);

        // Reviewing writes nothing.
        $this->assertSame(1, AcademicCycleSection::query()->where('academic_year_id', $target->id)->count());
    }

    public function test_confirming_a_roll_forward_twice_is_safe(): void
    {
        $actor = $this->authorized_user(['create section', 'read section']);
        $school = $this->workingSchool();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $source = AcademicYear::factory()->create(['school_id' => $school->id, 'start_year' => 2025, 'stop_year' => 2026]);
        $target = AcademicYear::factory()->create(['school_id' => $school->id, 'start_year' => 2026, 'stop_year' => 2027]);
        AcademicCycleSection::factory()->create([
            'school_id'         => $school->id, 'academic_year_id' => $source->id,
            'academic_level_id' => $academicLevel->id, 'name' => 'Green',
        ]);

        $payload = ['source_academic_year_id' => $source->id, 'target_academic_year_id' => $target->id];

        $actor->post(route('academic-cycle-sections.roll-forward'), $payload)
            ->assertRedirect(route('academic-cycle-sections.index', ['academic_year_id' => $target->id]));
        $actor->post(route('academic-cycle-sections.roll-forward'), $payload)
            ->assertRedirect(route('academic-cycle-sections.index', ['academic_year_id' => $target->id]));

        $copies = AcademicCycleSection::query()->where('academic_year_id', $target->id)->get();
        $this->assertCount(1, $copies);
        $this->assertSame(AcademicStructureStatus::Draft, $copies->sole()->status);
        $this->assertCount(1, AuditEvent::ofAction(AuditAction::AcademicCycleSectionsRolledForward)->get());
    }

    public function test_a_reader_cannot_open_the_roll_forward_screen(): void
    {
        $actor = $this->authorized_user(['read section']);

        $actor->get(route('academic-cycle-sections.roll-forward.show'))->assertForbidden();
    }

    private function teacher(): User
    {
        $teacher = $this->memberOf($this->workingSchool());
        $teacher->assignRole(Role::Teacher->value);

        return $teacher;
    }
}
