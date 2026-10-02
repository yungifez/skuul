<?php

namespace Tests\Feature;

use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Livewire\AcademicCycleSectionForm;
use App\Livewire\AcademicLevelForm;
use App\Livewire\AcademicStructureStatusControl;
use App\Livewire\AcademicYearStructureTree;
use App\Livewire\RollForwardSections;
use App\Livewire\SectionDirectory;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\School;
use App\Models\SchoolOperatingProfile;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
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
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Kestrel Stage']);

        $actor->get(route('academic-levels.index'))
            ->assertOk()
            ->assertSee('Classes and sections help')
            ->assertSee(route('academic-cycle-sections.index'), false)
            ->assertSee('Kestrel Stage')
            ->assertSee(route('academic-levels.edit', $academicLevel), false)
            ->assertSee(route('academic-levels.create'), false);
    }

    public function test_the_level_index_describes_group_teaching_once_and_keeps_the_summary_concise(): void
    {
        $actor = $this->authorized_user(['read class', 'create class', 'update class']);
        $schoolId = $this->workingSchool()->id;
        $group = AcademicLevel::factory()->create([
            'school_id' => $schoolId,
            'name' => 'Kindergarten',
            'is_group' => true,
        ]);
        AcademicLevel::factory()->count(2)->create([
            'school_id' => $schoolId,
            'parent_id' => $group->id,
        ]);

        $actor->get(route('academic-levels.index'))
            ->assertOk()
            ->assertSee('2 levels · can be taught together')
            ->assertSee('Group')
            ->assertDontSee('Group · whole-group teaching available')
            ->assertDontSee('Level group · can be taught as one group');
    }

    public function test_the_level_index_shows_an_empty_state_and_a_way_in(): void
    {
        $actor = $this->authorized_user(['read class', 'create class'], School::factory()->create());

        $actor->get(route('academic-levels.index'))
            ->assertOk()
            ->assertSee('No '.strtolower(school_terms('class_level', 'classes')).' yet')
            ->assertSee('data-resource-create-action="'.route('academic-levels.create').'"', false);
    }

    public function test_the_level_index_filters_by_status(): void
    {
        $actor = $this->authorized_user(['read class']);
        AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Kestrel Stage']);
        AcademicLevel::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Merlin Stage',
            'status' => AcademicStructureStatus::Archived,
        ]);

        $actor->get(route('academic-levels.index', ['status' => AcademicStructureStatus::Archived->value]))
            ->assertOk()
            ->assertSee('Merlin Stage')
            ->assertDontSee('Kestrel Stage')
            ->assertSee('aria-current="page"', false);

        $actor->get(route('academic-levels.index', ['status' => AcademicStructureStatus::Draft->value]))
            ->assertOk()
            ->assertSee('No class is draft. This school has')
            ->assertSee('Show every class');
    }

    public function test_the_structure_tree_keeps_its_display_flags_from_the_browser(): void
    {
        $this->authorized_user(['read class']);
        AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'status' => AcademicStructureStatus::Archived]);

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test(AcademicYearStructureTree::class, ['allowWithoutAcademicYear' => true, 'status' => AcademicStructureStatus::Active->value])
            ->set('status', null);
    }

    public function test_the_level_show_screen_reads_in_plain_words(): void
    {
        $actor = $this->authorized_user(['read class']);
        $parent = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Primary']);
        $academicLevel = AcademicLevel::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Primary 4',
            'parent_id' => $parent->id,
        ]);

        $actor->get(route('academic-levels.show', $academicLevel))
            ->assertOk()
            ->assertSee(route('academic-levels.show', $parent))
            ->assertSee(AcademicStructureStatus::Active->label())
            ->assertSee('No '.strtolower(school_terms('section', 'sections')))
            ->assertDontSee('Not set');
    }

    public function test_a_manager_can_edit_a_level_and_the_change_is_audited(): void
    {
        $actor = $this->authorized_user(['read class', 'update class']);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Primary 4']);

        $actor->get(route('academic-levels.edit', $academicLevel))
            ->assertOk()
            ->assertSee('Edit the reusable '.strtolower(school_term('class_level', 'class')))
            ->assertSee('Level name')
            ->assertSee('Level group (optional)')
            ->assertDontSee('Local label (optional)');

        Livewire::test(AcademicLevelForm::class, ['academicLevel' => $academicLevel])
            ->set('name', ' Grade 4 ')
            ->set('position', '4')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('academic-levels.show', $academicLevel));

        $this->assertSame('Grade 4', $academicLevel->fresh()->name);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AcademicLevelUpdated)->forSubject($academicLevel)->first());
    }

    public function test_adding_a_class_speaks_the_school_words(): void
    {
        $this->authorized_user(['read class', 'create class']);

        Livewire::test(AcademicLevelForm::class)
            ->set('name', 'Primary 5')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Class created. Add a section to use it this school year.', session('success'));
    }

    public function test_a_level_is_archived_only_when_no_section_of_it_still_runs(): void
    {
        $this->authorized_user(['read class', 'update class']);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id]);
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]);

        Livewire::test(AcademicStructureStatusControl::class, ['record' => $academicLevel])
            ->call('archive')
            ->assertDispatched('status-message', type: 'danger')
            ->assertNoRedirect();

        $this->assertSame(AcademicStructureStatus::Active, $academicLevel->fresh()->status);

        $section->update(['status' => AcademicStructureStatus::Archived]);

        Livewire::test(AcademicStructureStatusControl::class, ['record' => $academicLevel->fresh()])
            ->call('archive')
            ->assertRedirect();

        $this->assertSame(AcademicStructureStatus::Archived, $academicLevel->fresh()->status);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AcademicLevelStatusChanged)->forSubject($academicLevel)->first());
    }

    public function test_an_archived_level_sends_an_editor_back_with_an_explanation(): void
    {
        $actor = $this->authorized_user(['read class', 'update class']);
        $academicLevel = AcademicLevel::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'status' => AcademicStructureStatus::Archived,
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
        $this->assertFalse(Route::has('academic-levels.update'));
        $this->assertFalse(Route::has('academic-levels.store'));

        Livewire::test(AcademicLevelForm::class, ['academicLevel' => $academicLevel])->assertForbidden();
        Livewire::test(AcademicLevelForm::class)->assertForbidden();
    }

    public function test_a_level_added_from_school_setup_returns_to_the_classes_step(): void
    {
        $actor = $this->authorized_user(['read class', 'create class']);

        $actor->get(route('academic-levels.create', ['setup' => 1, 'school_setup' => 1]))->assertOk()->assertSee('Create class');

        Livewire::test(AcademicLevelForm::class, ['setup' => true, 'schoolSetup' => true])
            ->set('name', 'Primary 4')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('schools.setup', [current_school(), 'classes']));

        $this->assertTrue(AcademicLevel::inSchool()->where('name', 'Primary 4')->exists());
    }

    public function test_a_level_added_from_a_year_setup_returns_to_that_year_only_when_it_belongs_here(): void
    {
        $this->authorized_user(['read class', 'create class']);
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        $otherYear = AcademicYear::factory()->create(['school_id' => School::factory()->create()->id]);

        Livewire::test(AcademicLevelForm::class, ['setup' => true, 'academicYearId' => $academicYear->id])
            ->set('name', 'Primary 5')
            ->call('save')
            ->assertRedirect(route('academic-years.setup', [$academicYear, 'structure']));

        Livewire::test(AcademicLevelForm::class, ['setup' => true, 'academicYearId' => $otherYear->id])
            ->assertSet('academicYearId', null)
            ->set('name', 'Primary 6')
            ->call('save')
            ->assertRedirect(route('schools.setup', [current_school(), 'academic-year']));
    }

    public function test_a_level_can_only_sit_under_a_listed_group_of_this_school(): void
    {
        $this->authorized_user(['read class', 'create class']);
        $group = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'is_group' => true, 'name' => 'Kindergarten']);
        $plainLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'is_group' => false]);
        $foreignGroup = AcademicLevel::factory()->create(['school_id' => School::factory()->create()->id, 'is_group' => true]);

        foreach ([$plainLevel, $foreignGroup] as $wrongParent) {
            Livewire::test(AcademicLevelForm::class)
                ->set('name', 'KG 1')
                ->set('parentId', (string) $wrongParent->id)
                ->call('save')
                ->assertHasErrors(['parentId' => 'in']);
        }

        Livewire::test(AcademicLevelForm::class, ['preselectedParentId' => $group->id])
            ->assertSet('parentId', (string) $group->id)
            ->set('name', 'KG 1')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame($group->id, AcademicLevel::inSchool()->where('name', 'KG 1')->value('parent_id'));
    }

    public function test_choosing_a_group_clears_the_group_above_it(): void
    {
        $this->authorized_user(['read class', 'create class']);
        $group = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'is_group' => true]);

        Livewire::test(AcademicLevelForm::class)
            ->set('name', 'Lower school')
            ->set('parentId', (string) $group->id)
            ->set('isGroup', true)
            ->assertSet('parentId', '')
            ->call('save')
            ->assertHasNoErrors();

        $created = AcademicLevel::inSchool()->where('name', 'Lower school')->firstOrFail();
        $this->assertTrue($created->is_group);
        $this->assertNull($created->parent_id);
    }

    public function test_a_level_with_sections_cannot_become_a_group(): void
    {
        $this->authorized_user(['read class', 'update class']);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'is_group' => false]);
        AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_level_id' => $academicLevel->id,
        ]);

        Livewire::test(AcademicLevelForm::class, ['academicLevel' => $academicLevel])
            ->set('isGroup', true)
            ->call('save')
            ->assertHasErrors('isGroup')
            ->assertNoRedirect();

        $this->assertFalse($academicLevel->fresh()->is_group);
    }

    public function test_a_repeated_level_name_or_code_reads_as_a_message(): void
    {
        $this->authorized_user(['read class', 'create class']);
        AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Primary 4', 'code' => 'P4']);
        AcademicLevel::factory()->create(['school_id' => School::factory()->create()->id, 'name' => 'Primary 5', 'code' => 'P5']);

        Livewire::test(AcademicLevelForm::class)
            ->set('name', 'Primary 4')
            ->set('code', 'P4')
            ->call('save')
            ->assertHasErrors(['name' => 'unique', 'code' => 'unique']);

        Livewire::test(AcademicLevelForm::class)
            ->set('name', 'Primary 5')
            ->set('code', 'P5')
            ->call('save')
            ->assertHasNoErrors();
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
            'school_id' => $school->id, 'academic_year_id' => $thisCycle->id,
            'academic_level_id' => $academicLevel->id, 'name' => 'Green',
        ]);
        AcademicCycleSection::factory()->create([
            'school_id' => $school->id, 'academic_year_id' => $lastCycle->id,
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
            'school_id' => $school->id, 'academic_year_id' => $academicYear->id,
            'academic_level_id' => $primary->id, 'name' => 'Green', 'status' => AcademicStructureStatus::Active,
        ]);
        AcademicCycleSection::factory()->create([
            'school_id' => $school->id, 'academic_year_id' => $academicYear->id,
            'academic_level_id' => $junior->id, 'name' => 'Amber', 'status' => AcademicStructureStatus::Draft,
        ]);

        $actor->get(route('academic-cycle-sections.index', ['academic_year_id' => $academicYear->id, 'academic_level_id' => $primary->id]))
            ->assertOk()
            ->assertSee('Green')
            ->assertDontSee('Amber');

        $actor->get(route('academic-cycle-sections.index', [
            'academic_year_id' => $academicYear->id,
            'status' => AcademicStructureStatus::Draft->value,
        ]))
            ->assertOk()
            ->assertSee('Amber')
            ->assertDontSee('>Green<', false);
    }

    public function test_the_section_list_filters_live_and_ignores_another_schools_ids(): void
    {
        $this->authorized_user(['read section']);
        $school = $this->workingSchool();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $this->sectionIn($academicYear, $academicLevel, 'Kingfisher');
        $foreignYear = AcademicYear::factory()->create(['school_id' => School::factory()->create()->id]);
        $foreignSection = $this->sectionIn($foreignYear, AcademicLevel::factory()->create(['school_id' => $foreignYear->school_id]), 'Pelican');

        Livewire::withQueryParams(['academic_year_id' => $academicYear->id])
            ->test(SectionDirectory::class)
            ->assertSee('Kingfisher')
            ->set('status', AcademicStructureStatus::Archived->value)
            ->assertDontSee('Kingfisher')
            ->assertSee('Show every section')
            ->call('clearFilters')
            ->assertSet('academicYearId', '')
            ->assertSee('Kingfisher')
            ->assertDontSee('Pelican')
            ->set('academicYearId', (string) $foreignYear->id)
            ->assertDontSee('Pelican')
            ->assertDontSee(route('academic-cycle-sections.show', $foreignSection));
    }

    public function test_a_reader_cannot_open_the_section_list_without_permission(): void
    {
        $this->authorized_user([]);

        Livewire::test(SectionDirectory::class)->assertForbidden();
    }

    public function test_the_create_screen_asks_for_a_level_before_anything_else(): void
    {
        $actor = $this->authorized_user(['create section', 'create class'], School::factory()->create());

        $actor->get(route('academic-cycle-sections.create'))
            ->assertOk()
            ->assertSee('Add a '.strtolower(school_term('class_level', 'class')).' first');
    }

    public function test_the_create_screen_preselects_the_level_it_was_opened_from(): void
    {
        $actor = $this->authorized_user(['create section']);
        $school = $this->workingSchool();
        AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id, 'name' => 'Primary 4']);

        $actor->get(route('academic-cycle-sections.create', ['academic_level_id' => $academicLevel->id]))
            ->assertOk()
            ->assertSee('Optional details');

        Livewire::test(AcademicCycleSectionForm::class, ['preselectedAcademicLevelId' => $academicLevel->id])
            ->assertSet('academicLevelId', (string) $academicLevel->id);
    }

    public function test_a_manager_can_edit_a_cycle_section_without_moving_its_cycle(): void
    {
        $actor = $this->authorized_user(['read section', 'update section']);
        $school = $this->workingSchool();
        SchoolOperatingProfile::query()->updateOrCreate(
            ['school_id' => $school->id],
            ['preset' => 'home_sections', 'labels' => array_replace(SchoolOperatingProfile::labelsFor('home_sections'), ['homeroom_teacher' => 'Form teacher'])],
        );
        $teacher = $this->teacher();
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'name' => 'Kestrel',
            'status' => AcademicStructureStatus::Active,
        ]);

        $actor->get(route('academic-cycle-sections.edit', $section))
            ->assertOk()
            ->assertSee($section->academicYear->name)
            ->assertSee('Form teacher');

        Livewire::test(AcademicCycleSectionForm::class, ['academicCycleSection' => $section])
            ->set('room', ' Block B, Room 2 ')
            ->set('capacity', '40')
            ->set('homeroomTeacherId', (string) $teacher->id)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('academic-cycle-sections.show', $section));

        $section->refresh();
        $this->assertSame('Block B, Room 2', $section->room);
        $this->assertSame(40, $section->capacity);
        $this->assertSame($teacher->id, $section->homeroom_teacher_id);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AcademicCycleSectionUpdated)->forSubject($section)->first());
        $actor->get(route('academic-cycle-sections.show', $section))->assertOk()->assertSee('Form teacher');
    }

    public function test_a_repeated_section_name_reads_as_a_message_not_a_crash(): void
    {
        $actor = $this->authorized_user(['create section', 'read section', 'update section']);
        $school = $this->workingSchool();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $taken = ['academic_year_id' => $academicYear->id, 'academic_level_id' => $academicLevel->id, 'name' => 'Osprey'];
        AcademicCycleSection::factory()->create($taken + ['school_id' => $school->id]);

        Livewire::test(AcademicCycleSectionForm::class)
            ->set('academicYearId', (string) $academicYear->id)
            ->set('academicLevelId', (string) $academicLevel->id)
            ->set('name', 'Osprey')
            ->call('save')
            ->assertHasErrors(['name' => 'unique']);

        // The same name is still free in another level of the same cycle.
        $otherLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        Livewire::test(AcademicCycleSectionForm::class)
            ->set('academicYearId', (string) $academicYear->id)
            ->set('academicLevelId', (string) $otherLevel->id)
            ->set('name', 'Osprey')
            ->call('save')
            ->assertHasNoErrors();
    }

    public function test_a_section_added_from_school_setup_returns_to_the_classes_step(): void
    {
        $this->authorized_user(['create section', 'read section']);
        $school = $this->workingSchool();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);

        Livewire::test(AcademicCycleSectionForm::class, ['setup' => true, 'schoolSetup' => true, 'preselectedAcademicYearId' => $academicYear->id, 'preselectedAcademicLevelId' => $academicLevel->id])
            ->assertSet('academicYearId', (string) $academicYear->id)
            ->set('name', 'Heron')
            ->call('save')
            ->assertRedirect(route('schools.setup', [current_school(), 'classes']));

        Livewire::test(AcademicCycleSectionForm::class, ['setup' => true, 'preselectedAcademicYearId' => $academicYear->id, 'preselectedAcademicLevelId' => $academicLevel->id])
            ->set('name', 'Egret')
            ->call('save')
            ->assertRedirect(route('academic-years.setup', [$academicYear, 'structure']));
    }

    public function test_a_section_takes_only_this_schools_years_classes_and_teachers(): void
    {
        $this->authorized_user(['create section', 'read section']);
        $school = $this->workingSchool();
        $otherSchool = School::factory()->create();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $foreignYear = AcademicYear::factory()->create(['school_id' => $otherSchool->id]);
        $foreignLevel = AcademicLevel::factory()->create(['school_id' => $otherSchool->id]);
        $group = AcademicLevel::factory()->create(['school_id' => $school->id, 'is_group' => true]);
        $foreignTeacher = $this->memberOf($otherSchool);
        $foreignTeacher->assignRole(Role::Teacher->value);
        $foreignTeacher->schoolMemberships()->where('school_id', $school->id)->delete();

        Livewire::test(AcademicCycleSectionForm::class, ['preselectedAcademicYearId' => $foreignYear->id])
            ->assertSet('academicYearId', '')
            ->set('academicYearId', (string) $foreignYear->id)
            ->set('academicLevelId', (string) $foreignLevel->id)
            ->set('name', 'Wren')
            ->call('save')
            ->assertHasErrors(['academicYearId' => 'in', 'academicLevelId' => 'in'])
            ->set('academicYearId', (string) $academicYear->id)
            ->set('academicLevelId', (string) $group->id)
            ->call('save')
            ->assertHasErrors(['academicLevelId' => 'in'])
            ->set('academicLevelId', (string) $academicLevel->id)
            ->set('homeroomTeacherId', (string) $foreignTeacher->id)
            ->call('save')
            ->assertHasErrors(['homeroomTeacherId' => 'in']);

        $this->assertFalse(AcademicCycleSection::query()->where('name', 'Wren')->exists());
        $this->assertFalse(Route::has('academic-cycle-sections.store'));
        $this->assertFalse(Route::has('academic-cycle-sections.update'));
    }

    public function test_a_capacity_below_the_placed_learners_is_refused(): void
    {
        $this->authorized_user(['read section', 'update section']);
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'status' => AcademicStructureStatus::Active,
            'capacity' => 30,
        ]);
        StudentRecord::factory()->count(3)->create([
            'school_id' => $section->school_id,
            'academic_cycle_section_id' => $section->id,
            'status' => EnrollmentStatus::Active,
        ]);

        Livewire::test(AcademicCycleSectionForm::class, ['academicCycleSection' => $section])
            ->set('capacity', '2')
            ->call('save')
            ->assertHasErrors('capacity')
            ->assertNoRedirect()
            ->set('capacity', '3')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(3, $section->fresh()->capacity);
    }

    public function test_a_class_teacher_who_left_is_named_until_the_section_is_changed(): void
    {
        $this->authorized_user(['read section', 'update section']);
        $former = $this->memberOf(School::factory()->create());
        $former->forceFill(['name' => 'Moses Adeyemi'])->save();
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'status' => AcademicStructureStatus::Active,
            'homeroom_teacher_id' => $former->id,
        ]);

        Livewire::test(AcademicCycleSectionForm::class, ['academicCycleSection' => $section])
            ->assertSee('Moses Adeyemi (no longer at this school)')
            ->call('save')
            ->assertHasErrors(['homeroomTeacherId' => 'in'])
            ->set('homeroomTeacherId', '')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNull($section->fresh()->homeroom_teacher_id);
    }

    public function test_a_cycle_section_can_be_archived_from_its_own_screen(): void
    {
        $actor = $this->authorized_user(['read section', 'update section']);
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Merlin',
            'status' => AcademicStructureStatus::Active,
        ]);

        $actor->get(route('academic-cycle-sections.show', $section))->assertOk()->assertSee('Archive');

        Livewire::test(AcademicStructureStatusControl::class, ['record' => $section])
            ->call('archive')
            ->assertRedirect();

        $this->assertSame(AcademicStructureStatus::Archived, $section->fresh()->status);

        $actor->get(route('academic-cycle-sections.edit', $section))
            ->assertRedirect(route('academic-cycle-sections.show', $section))
            ->assertSessionHas('danger');
    }

    public function test_a_reader_sees_the_status_but_cannot_change_it(): void
    {
        $this->authorized_user(['read section']);
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'status' => AcademicStructureStatus::Draft,
        ]);

        Livewire::test(AcademicStructureStatusControl::class, ['record' => $section])
            ->assertSee(AcademicStructureStatus::Draft->label())
            ->assertDontSee('Activate')
            ->call('activate')
            ->assertForbidden();

        $this->assertSame(AcademicStructureStatus::Draft, $section->fresh()->status);
    }

    public function test_the_cycle_section_show_screen_says_the_section_serves_one_cycle(): void
    {
        $actor = $this->authorized_user(['read section']);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Primary 4']);
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_level_id' => $academicLevel->id,
            'name' => 'Green',
            'room' => 'Block A',
        ]);

        $actor->get(route('academic-cycle-sections.show', $section))
            ->assertOk()
            ->assertSee($section->academicYear->name)
            ->assertSee('Block A')
            ->assertSee('—')
            ->assertDontSee('Not set')
            ->assertDontSee('Roll into another year');
    }

    public function test_the_roll_forward_screen_reviews_the_copy_before_it_is_made(): void
    {
        $actor = $this->authorized_user(['create section', 'read section']);
        [$academicLevel, $source, $target] = $this->rollForwardYears();
        $this->sectionIn($source, $academicLevel, 'Green');
        $this->sectionIn($source, $academicLevel, 'Amber');
        $this->sectionIn($target, $academicLevel, 'Amber');

        $actor->get(route('academic-cycle-sections.roll-forward.show', [
            'source_academic_year_id' => $source->id,
            'target_academic_year_id' => $target->id,
        ]))->assertOk()->assertSeeLivewire(RollForwardSections::class);

        Livewire::withQueryParams(['source_academic_year_id' => $source->id, 'target_academic_year_id' => $target->id])
            ->test(RollForwardSections::class)
            ->assertSee('Will be created as drafts')
            ->assertSee('Already in '.$target->name)
            ->assertSee('Learners')
            ->assertSee('Create 1 draft section');

        // Reviewing writes nothing.
        $this->assertSame(1, AcademicCycleSection::query()->where('academic_year_id', $target->id)->count());
    }

    public function test_confirming_a_roll_forward_twice_is_safe(): void
    {
        $this->authorized_user(['create section', 'read section']);
        [$academicLevel, $source, $target] = $this->rollForwardYears();
        $this->sectionIn($source, $academicLevel, 'Green');

        foreach ([1, 2] as $attempt) {
            Livewire::withQueryParams(['source_academic_year_id' => $source->id, 'target_academic_year_id' => $target->id])
                ->test(RollForwardSections::class)
                ->call('rollForward')
                ->assertHasNoErrors()
                ->assertRedirect(route('academic-cycle-sections.index', ['academic_year_id' => $target->id]));
        }

        $copies = AcademicCycleSection::query()->where('academic_year_id', $target->id)->get();
        $this->assertCount(1, $copies);
        $this->assertSame(AcademicStructureStatus::Draft, $copies->sole()->status);
        $this->assertCount(1, AuditEvent::ofAction(AuditAction::AcademicCycleSectionsRolledForward)->get());
        $this->assertFalse(Route::has('academic-cycle-sections.roll-forward'));
    }

    public function test_a_roll_forward_defaults_to_the_year_before_and_returns_to_setup(): void
    {
        $this->authorized_user(['create section', 'read section']);
        [$academicLevel, $source, $target] = $this->rollForwardYears();
        $this->sectionIn($source, $academicLevel, 'Green');

        Livewire::withQueryParams(['target_academic_year_id' => $target->id])
            ->test(RollForwardSections::class, ['setup' => true])
            ->assertSet('sourceAcademicYearId', (string) $source->id)
            ->call('rollForward')
            ->assertRedirect(route('schools.setup', [current_school(), 'classes']));

        $this->assertTrue(AcademicCycleSection::query()->where('academic_year_id', $target->id)->where('name', 'Green')->exists());
    }

    public function test_a_roll_forward_leaves_archived_sections_and_retired_classes_behind(): void
    {
        $this->authorized_user(['create section', 'read section']);
        [$academicLevel, $source, $target] = $this->rollForwardYears();
        $school = $this->workingSchool();
        $retiredLevel = AcademicLevel::factory()->create(['school_id' => $school->id, 'name' => 'Old Stage']);
        $groupedLevel = AcademicLevel::factory()->create(['school_id' => $school->id, 'name' => 'Upper School']);
        $this->sectionIn($source, $academicLevel, 'Green');
        $this->sectionIn($source, $academicLevel, 'Closed Stream')->forceFill(['status' => AcademicStructureStatus::Archived])->save();
        $this->sectionIn($source, $retiredLevel, 'Blue');
        $this->sectionIn($source, $groupedLevel, 'Red');
        $retiredLevel->forceFill(['status' => AcademicStructureStatus::Archived])->save();
        $groupedLevel->forceFill(['is_group' => true])->save();

        Livewire::withQueryParams(['source_academic_year_id' => $source->id, 'target_academic_year_id' => $target->id])
            ->test(RollForwardSections::class)
            ->assertSee('Left behind')
            ->assertSee('Old Stage · Blue')
            ->assertSee('Create 1 draft section')
            ->call('rollForward');

        $this->assertSame(['Green'], AcademicCycleSection::query()->where('academic_year_id', $target->id)->pluck('name')->all());
    }

    public function test_a_roll_forward_into_the_same_or_a_closed_year_is_refused(): void
    {
        $this->authorized_user(['create section', 'read section']);
        [$academicLevel, $source, $target] = $this->rollForwardYears();
        $this->sectionIn($source, $academicLevel, 'Green');

        Livewire::withQueryParams(['source_academic_year_id' => $source->id, 'target_academic_year_id' => $source->id])
            ->test(RollForwardSections::class)
            ->assertSee('Choose a different academic cycle')
            ->assertDontSee('Create 1 draft section');

        $target->forceFill(['status' => AcademicPeriodStatus::Closed])->save();

        Livewire::withQueryParams(['source_academic_year_id' => $source->id, 'target_academic_year_id' => $target->id])
            ->test(RollForwardSections::class)
            ->assertSee($target->name.' is closed')
            ->assertDontSee('Create 1 draft section')
            ->call('rollForward')
            ->assertHasErrors('targetAcademicYearId')
            ->assertNoRedirect();

        $this->assertSame(0, AcademicCycleSection::query()->where('academic_year_id', $target->id)->count());
    }

    public function test_a_roll_forward_ignores_another_schools_year(): void
    {
        $this->authorized_user(['create section', 'read section']);
        [$academicLevel, $source] = $this->rollForwardYears();
        $this->sectionIn($source, $academicLevel, 'Green');
        $foreignYear = AcademicYear::factory()->create(['school_id' => School::factory()->create()->id]);

        Livewire::withQueryParams(['source_academic_year_id' => $source->id, 'target_academic_year_id' => $foreignYear->id])
            ->test(RollForwardSections::class)
            ->call('rollForward')
            ->assertHasErrors('sourceAcademicYearId')
            ->assertNoRedirect();

        $this->assertSame(0, AcademicCycleSection::query()->where('academic_year_id', $foreignYear->id)->count());
    }

    public function test_a_reader_cannot_open_the_roll_forward_screen(): void
    {
        $actor = $this->authorized_user(['read section']);

        $actor->get(route('academic-cycle-sections.roll-forward.show'))->assertForbidden();
        Livewire::test(RollForwardSections::class)->assertForbidden();
    }

    /**
     * @return array{0: AcademicLevel, 1: AcademicYear, 2: AcademicYear}
     */
    private function rollForwardYears(): array
    {
        $school = $this->workingSchool();

        return [
            AcademicLevel::factory()->create(['school_id' => $school->id, 'name' => 'Primary 4']),
            AcademicYear::factory()->create(['school_id' => $school->id, 'start_year' => 2090, 'stop_year' => 2091]),
            AcademicYear::factory()->create(['school_id' => $school->id, 'start_year' => 2091, 'stop_year' => 2092]),
        ];
    }

    private function sectionIn(AcademicYear $academicYear, AcademicLevel $academicLevel, string $name): AcademicCycleSection
    {
        return AcademicCycleSection::factory()->create([
            'school_id' => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'name' => $name,
        ]);
    }

    private function teacher(): User
    {
        $teacher = $this->memberOf($this->workingSchool());
        $teacher->assignRole(Role::Teacher->value);

        return $teacher;
    }
}
