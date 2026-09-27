<?php

namespace Tests\Feature;

use App\Actions\Curriculum\AssignTeacher;
use App\Actions\Curriculum\ChangeCourseOfferingStatus;
use App\Actions\Curriculum\CreateCourseOffering;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\CourseOfferingStatus;
use App\Enums\InstructionalModel;
use App\Enums\Role;
use App\Enums\RosterMode;
use App\Enums\TeachingRole;
use App\Exceptions\InvalidValueException;
use App\Livewire\CourseOfferingDirectory;
use App\Livewire\CreateCourseOffering as CreateCourseOfferingForm;
use App\Livewire\EditCourseOfferingRoster;
use App\Livewire\SetUpSubjectAcrossLevels;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\InstructionalModelSetting;
use App\Models\School;
use App\Models\SchoolOperatingProfile;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Policies\CourseOfferingPolicy;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseOfferingTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_course_offering_keeps_its_exact_period_and_default_home_sections(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();

        $courseOffering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [$cycleSection->id],
        );

        $this->assertSame(CourseOfferingStatus::Draft, $courseOffering->status);
        $this->assertSame($academicPeriod->id, $courseOffering->academic_period_id);
        $this->assertSame([$cycleSection->id], $courseOffering->cycleSections->modelKeys());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::CourseOfferingCreated)->forSubject($courseOffering)->first());
    }

    public function test_a_course_offering_refuses_a_section_outside_its_academic_level(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext();
        $otherLevel = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id]);
        $otherSection = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $otherLevel->id,
        ]);

        $this->expectException(InvalidValueException::class);

        app(CreateCourseOffering::class)->create($subject, $academicYear, $academicPeriod, $academicLevel, [$otherSection->id]);
    }

    public function test_only_one_matching_active_offering_can_exist(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();
        $create = app(CreateCourseOffering::class);
        $first = $create->create($subject, $academicYear, $academicPeriod, $academicLevel, [$cycleSection->id]);
        $second = $create->create($subject, $academicYear, $academicPeriod, $academicLevel, [$cycleSection->id]);

        app(ChangeCourseOfferingStatus::class)->change($first, CourseOfferingStatus::Active);

        $this->expectException(InvalidValueException::class);

        app(ChangeCourseOfferingStatus::class)->change($second, CourseOfferingStatus::Active);
    }

    public function test_an_offering_cannot_activate_until_its_period_is_operational(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext(AcademicPeriodStatus::Scheduled);
        $courseOffering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [],
            RosterMode::AcademicLevel,
        );

        $this->expectException(InvalidValueException::class);

        app(ChangeCourseOfferingStatus::class)->change($courseOffering, CourseOfferingStatus::Active);
    }

    public function test_a_subject_schedule_can_use_a_named_learner_roster(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();
        InstructionalModelSetting::create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $academicYear->id,
            'model' => InstructionalModel::SubjectBasedSchedule,
        ]);
        /** @var User $student */
        $student = User::factory()->create();
        $studentRecord = StudentRecord::create([
            'user_id' => $student->id,
            'school_id' => $this->workingSchool()->id,
            'academic_cycle_section_id' => $cycleSection->id,
            'admission_number' => fake()->unique()->bothify('####????'),
            'admission_date' => now()->toDateString(),
        ]);

        $courseOffering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [],
            RosterMode::IndividualRoster,
            [$studentRecord->id],
        );

        $this->assertSame(RosterMode::IndividualRoster, $courseOffering->roster_mode);
        $this->assertSame([$studentRecord->id], $courseOffering->studentRecords->modelKeys());
        $this->assertTrue($courseOffering->cycleSections->isEmpty());
    }

    public function test_a_course_offering_keeps_its_teacher_assignment(): void
    {
        $this->authorized_user([]);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();
        $courseOffering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [$cycleSection->id],
        );
        $teacher = $this->memberOf($this->workingSchool());
        $teacher->assignRole(Role::Teacher->value);

        $assignment = app(AssignTeacher::class)->assign(
            $courseOffering,
            $teacher,
            TeachingRole::Lead,
        );

        $this->assertSame($courseOffering->id, $assignment->course_offering_id);
        $this->assertSame($teacher->id, $courseOffering->fresh()->teachingAssignments()->sole()->user_id);
        $this->assertSame(1, TeachingAssignment::where('course_offering_id', $courseOffering->id)->count());
    }

    public function test_a_curriculum_manager_can_create_and_view_course_offerings(): void
    {
        $this->authorized_user(['create subject', 'read subject']);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();
        $secondPeriod = AcademicPeriod::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $academicYear->id,
            'position' => 2,
        ]);
        SchoolOperatingProfile::query()->updateOrCreate(
            ['school_id' => $this->workingSchool()->id],
            ['preset' => 'home_sections', 'labels' => array_merge(SchoolOperatingProfile::labelsFor('home_sections'), ['section' => 'Stream'])],
        );
        InstructionalModelSetting::query()->updateOrCreate(
            [
                'school_id' => $this->workingSchool()->id,
                'academic_year_id' => $academicYear->id,
            ],
            ['model' => InstructionalModel::Hybrid],
        );

        $this->get(route('course-offerings.create', ['academic_year_id' => $academicYear->id]))
            ->assertOk()
            ->assertSeeLivewire(CreateCourseOfferingForm::class);

        Livewire::test(CreateCourseOfferingForm::class, ['academicYearId' => $academicYear->id])
            ->assertSee('Who attends')
            ->assertSee('One stream')
            ->assertSee('Combined streams')
            ->assertSee('All '.strtolower(school_terms('period', 'periods')).' in the '.strtolower(school_term('academic_year', 'school year')))
            ->assertDontSee('home section')
            ->set('subjectId', $subject->id)
            ->set('academicLevelId', $academicLevel->id)
            ->assertSee($cycleSection->label ?? $cycleSection->name)
            ->set('rosterMode', RosterMode::HomeSection->value)
            ->set('academicCycleSectionIds', [$cycleSection->id])
            ->set('academicPeriodId', 'all')
            ->set('plannedPeriodsPerWeek', 5)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('course-offerings.index'));

        $this->assertSame(2, CourseOffering::query()
            ->where('school_id', $this->workingSchool()->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('subject_id', $subject->id)
            ->count());
        $this->assertDatabaseHas('course_offerings', [
            'school_id' => $this->workingSchool()->id,
            'academic_period_id' => $academicPeriod->id,
            'subject_id' => $subject->id,
        ]);
        $this->assertDatabaseHas('course_offerings', [
            'school_id' => $this->workingSchool()->id,
            'academic_period_id' => $secondPeriod->id,
            'subject_id' => $subject->id,
        ]);

        $this->get(route('course-offerings.index'))->assertOk()->assertSee($subject->name);
    }

    public function test_a_draft_subject_is_activated_from_the_list(): void
    {
        $this->authorized_user(['read subject', 'update subject']);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext();
        $courseOffering = app(CreateCourseOffering::class)->create($subject, $academicYear, $academicPeriod, $academicLevel, [], RosterMode::AcademicLevel);

        $this->get(route('course-offerings.index'))->assertOk()->assertSeeLivewire(CourseOfferingDirectory::class);

        Livewire::test(CourseOfferingDirectory::class)
            ->assertSee($subject->name)
            ->call('activate', $courseOffering->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertSame(CourseOfferingStatus::Active, $courseOffering->fresh()->status);
    }

    public function test_a_refused_activation_stays_on_the_list_and_says_why(): void
    {
        $this->authorized_user(['read subject', 'update subject']);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext(AcademicPeriodStatus::Scheduled);
        $courseOffering = app(CreateCourseOffering::class)->create($subject, $academicYear, $academicPeriod, $academicLevel, [], RosterMode::AcademicLevel);

        Livewire::test(CourseOfferingDirectory::class)
            ->call('activate', $courseOffering->id)
            ->assertDispatched('status-message', type: 'danger');

        $this->assertSame(CourseOfferingStatus::Draft, $courseOffering->fresh()->status);
    }

    public function test_a_teacher_is_added_from_the_list(): void
    {
        $this->authorized_user(['read subject', 'update subject']);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext();
        $courseOffering = app(CreateCourseOffering::class)->create($subject, $academicYear, $academicPeriod, $academicLevel, [], RosterMode::AcademicLevel);
        $teacher = $this->memberOf($this->workingSchool());
        $teacher->assignRole(Role::Teacher->value);

        Livewire::test(CourseOfferingDirectory::class)
            ->call('startAssigning', $courseOffering->id)
            ->assertSee($teacher->name)
            ->call('assignTeacher')
            ->assertHasErrors(['teacherId' => 'required'])
            ->set('teacherId', (string) $teacher->id)
            ->set('role', TeachingRole::Supporting->value)
            ->call('assignTeacher')
            ->assertHasNoErrors()
            ->assertSet('assigningId', null);

        $this->assertTrue(TeachingAssignment::query()
            ->where('course_offering_id', $courseOffering->id)
            ->where('user_id', $teacher->id)
            ->where('role', TeachingRole::Supporting)
            ->exists());
    }

    public function test_the_list_refuses_a_person_who_is_not_a_teacher_here(): void
    {
        $this->authorized_user(['read subject', 'update subject']);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext();
        $courseOffering = app(CreateCourseOffering::class)->create($subject, $academicYear, $academicPeriod, $academicLevel, [], RosterMode::AcademicLevel);
        $otherSchool = School::factory()->create();
        $stranger = $this->memberOf($otherSchool);
        school_context()->set($otherSchool, remember: false);
        $stranger->assignRole(Role::Teacher->value);
        school_context()->set($this->workingSchool(), remember: false);

        Livewire::test(CourseOfferingDirectory::class)
            ->call('startAssigning', $courseOffering->id)
            ->set('teacherId', (string) $stranger->id)
            ->call('assignTeacher')
            ->assertHasErrors('teacherId');

        $this->assertSame(0, TeachingAssignment::query()->where('course_offering_id', $courseOffering->id)->count());
    }

    public function test_a_reader_cannot_change_a_subject_from_the_list(): void
    {
        $this->authorized_user(['read subject']);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext();
        $courseOffering = app(CreateCourseOffering::class)->create($subject, $academicYear, $academicPeriod, $academicLevel, [], RosterMode::AcademicLevel);

        Livewire::test(CourseOfferingDirectory::class)
            ->assertSee($subject->name)
            ->assertDontSee('Activate')
            ->call('activate', $courseOffering->id)
            ->assertForbidden();

        $this->assertSame(CourseOfferingStatus::Draft, $courseOffering->fresh()->status);
    }

    public function test_the_list_cannot_touch_another_schools_subject(): void
    {
        $otherSchool = School::factory()->create();
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext(school: $otherSchool);
        $foreign = CourseOffering::factory()->create([
            'school_id' => $otherSchool->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => $academicLevel->id,
            'subject_id' => $subject->id,
        ]);
        $this->authorized_user(['read subject', 'update subject']);

        $directory = Livewire::test(CourseOfferingDirectory::class)->assertDontSee($subject->name);

        $this->assertThrows(fn () => $directory->call('activate', $foreign->id), ModelNotFoundException::class);
        $this->assertThrows(fn () => $directory->call('startAssigning', $foreign->id), ModelNotFoundException::class);

        $this->assertSame(CourseOfferingStatus::Draft, $foreign->fresh()->status);
    }

    public function test_the_list_filters_by_subject(): void
    {
        $this->authorized_user(['read subject']);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext();
        app(CreateCourseOffering::class)->create($subject, $academicYear, $academicPeriod, $academicLevel, [], RosterMode::AcademicLevel);
        $other = Subject::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Zz Other Subject']);
        app(CreateCourseOffering::class)->create($other, $academicYear, $academicPeriod, $academicLevel, [], RosterMode::AcademicLevel);

        Livewire::withQueryParams(['subject_id' => $subject->id])
            ->test(CourseOfferingDirectory::class)
            ->assertSee($subject->name)
            ->assertDontSee('Zz Other Subject</a>', false);
    }

    public function test_the_bulk_setup_page_opens_on_the_year_in_the_link(): void
    {
        $this->authorized_user(['create subject']);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext();

        $this->get(route('course-offerings.bulk-create.form', ['academic_year_id' => $academicYear->id, 'setup' => 1]))
            ->assertOk()
            ->assertSeeLivewire(SetUpSubjectAcrossLevels::class)
            ->assertSee($academicPeriod->displayName)
            ->assertSee($academicLevel->name)
            ->assertSee($subject->name)
            ->assertDontSee('<april:', false)
            ->assertDontSee('<slot:', false);
    }

    public function test_someone_without_create_access_cannot_open_the_bulk_setup(): void
    {
        $this->authorized_user(['read subject']);
        [, $academicYear] = $this->courseContext();

        Livewire::test(SetUpSubjectAcrossLevels::class, ['academicYear' => $academicYear])->assertForbidden();
    }

    public function test_the_bulk_setup_adds_one_offering_for_each_chosen_class_and_group(): void
    {
        $this->authorized_user(['create subject', 'read subject']);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();
        $secondSection = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]);
        $group = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'is_group' => true]);
        InstructionalModelSetting::query()->updateOrCreate(
            ['school_id' => $this->workingSchool()->id, 'academic_year_id' => $academicYear->id],
            ['model' => InstructionalModel::Hybrid],
        );

        Livewire::test(SetUpSubjectAcrossLevels::class, ['academicYear' => $academicYear])
            ->set('subjectId', $subject->id)
            ->set('academicPeriodId', (string) $academicPeriod->id)
            ->set('levelIds', [(string) $academicLevel->id, (string) $group->id])
            ->assertSee('Everyone in the group')
            ->assertSee($secondSection->label ?? $secondSection->name)
            ->set("configurations.{$academicLevel->id}.roster_mode", RosterMode::CombinedHomeSections->value)
            ->set("configurations.{$academicLevel->id}.section_ids", [(string) $cycleSection->id, (string) $secondSection->id])
            ->set("configurations.{$academicLevel->id}.planned_periods_per_week", '4')
            ->set("configurations.{$group->id}.capacity", '60')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('course-offerings.index'));

        $classOffering = CourseOffering::query()->where('academic_level_id', $academicLevel->id)->where('subject_id', $subject->id)->sole();
        $groupOffering = CourseOffering::query()->where('academic_level_id', $group->id)->where('subject_id', $subject->id)->sole();

        $this->assertSame(RosterMode::CombinedHomeSections, $classOffering->roster_mode);
        $this->assertSame(4, $classOffering->planned_periods_per_week);
        $this->assertEqualsCanonicalizing([$cycleSection->id, $secondSection->id], $classOffering->cycleSections()->pluck('academic_cycle_sections.id')->all());
        $this->assertSame(RosterMode::AcademicLevel, $groupOffering->roster_mode);
        $this->assertSame(60, $groupOffering->capacity);
        $this->assertSame($academicPeriod->id, $groupOffering->academic_period_id);
    }

    public function test_the_bulk_setup_asks_for_a_subject_a_period_and_a_class(): void
    {
        $this->authorized_user(['create subject']);
        [, $academicYear] = $this->courseContext();

        Livewire::test(SetUpSubjectAcrossLevels::class, ['academicYear' => $academicYear])
            ->call('save')
            ->assertHasErrors(['subjectId' => 'required', 'academicPeriodId' => 'required', 'levelIds' => 'required']);

        $this->assertSame(0, CourseOffering::query()->count());
    }

    public function test_the_bulk_setup_refuses_a_period_of_another_year(): void
    {
        $this->authorized_user(['create subject']);
        [$subject, $academicYear, , $academicLevel] = $this->courseContext();
        [, , $otherPeriod] = $this->courseContext();

        Livewire::test(SetUpSubjectAcrossLevels::class, ['academicYear' => $academicYear])
            ->set('subjectId', $subject->id)
            ->set('academicPeriodId', (string) $otherPeriod->id)
            ->set('levelIds', [(string) $academicLevel->id])
            ->call('save')
            ->assertHasErrors('academicPeriodId');
    }

    public function test_the_bulk_setup_cannot_open_a_year_of_another_school(): void
    {
        $this->authorized_user(['create subject']);
        $otherYear = AcademicYear::factory()->create(['school_id' => School::factory()->create()->id]);

        Livewire::test(SetUpSubjectAcrossLevels::class, ['academicYear' => $otherYear])->assertNotFound();
    }

    public function test_subject_setup_table_shows_catalogue_assignments_and_actions(): void
    {
        $this->authorized_user(['read subject', 'create subject', 'update subject']);
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();
        $offering = CourseOffering::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'subject_id' => $subject->id,
            'academic_level_id' => $academicLevel->id,
        ]);
        $offering->cycleSections()->attach($cycleSection);
        $unassignedSubject = Subject::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Unassigned subject']);

        $this->get(route('course-offerings.bulk-create', ['academic_year_id' => $academicYear->id]))
            ->assertSuccessful()
            ->assertSee('Subjects for '.$academicYear->name)
            ->assertSee($subject->short_name)
            ->assertSee($academicLevel->name.' · '.$cycleSection->name)
            ->assertSee($academicPeriod->displayName)
            ->assertSee($unassignedSubject->name)
            ->assertSee('Manage offerings')
            ->assertSee('Set up across levels');
    }

    public function test_subject_setup_table_hides_creation_actions_without_create_permission(): void
    {
        $this->authorized_user(['read subject']);
        [, $academicYear] = $this->courseContext();

        $this->get(route('course-offerings.bulk-create', ['academic_year_id' => $academicYear->id]))
            ->assertSuccessful()
            ->assertSee('Manage offerings')
            ->assertDontSee('Set up across levels')
            ->assertDontSee('Add one offering');
    }

    public function test_a_school_user_cannot_update_an_offering_from_another_school(): void
    {
        $this->authorized_user(['update subject']);
        /** @var School $otherSchool */
        $otherSchool = School::factory()->create();
        school_context()->set($otherSchool, remember: false);
        [$subject, $academicYear, $academicPeriod, $academicLevel] = $this->courseContext(school: $otherSchool);
        $courseOffering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [],
            RosterMode::AcademicLevel,
        );
        school_context()->set($this->workingSchool(), remember: false);

        $this->assertFalse(app(CourseOfferingPolicy::class)->update(auth()->user(), $courseOffering));
    }

    public function test_the_roster_can_name_learners_and_return_to_the_year_setup(): void
    {
        $this->authorized_user(['update subject']);
        [$courseOffering, $studentRecord] = $this->namedLearnerOffering();

        Livewire::test(EditCourseOfferingRoster::class, ['courseOffering' => $courseOffering, 'setup' => true])
            ->assertSet('rosterMode', RosterMode::IndividualRoster->value)
            ->assertSee($studentRecord->user->name)
            ->set('studentRecordIds', [(string) $studentRecord->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('academic-years.setup', [$courseOffering->academic_year_id, 'subjects']));

        $this->assertSame([$studentRecord->id], $courseOffering->fresh()->studentRecords->modelKeys());
    }

    public function test_the_roster_refuses_a_learner_of_another_class(): void
    {
        $this->authorized_user(['update subject']);
        [$courseOffering] = $this->namedLearnerOffering();
        [, , , , $otherSection] = $this->courseContext();
        $outsider = StudentRecord::create([
            'user_id' => User::factory()->create()->id,
            'school_id' => $this->workingSchool()->id,
            'academic_cycle_section_id' => $otherSection->id,
            'admission_number' => fake()->unique()->bothify('####????'),
            'admission_date' => now()->toDateString(),
        ]);

        Livewire::test(EditCourseOfferingRoster::class, ['courseOffering' => $courseOffering])
            ->set('studentRecordIds', [(string) $outsider->id])
            ->call('save')
            ->assertHasErrors('studentRecordIds.0');

        $this->assertSame([], $courseOffering->fresh()->studentRecords->modelKeys());
    }

    public function test_a_reader_cannot_open_the_roster_editor(): void
    {
        $this->authorized_user(['read subject']);
        [$courseOffering] = $this->namedLearnerOffering();

        Livewire::test(EditCourseOfferingRoster::class, ['courseOffering' => $courseOffering])->assertForbidden();
    }

    /**
     * Make an offering that names its learners, with one learner in its class who is not named yet.
     *
     * @return array{CourseOffering, StudentRecord}
     */
    private function namedLearnerOffering(): array
    {
        [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection] = $this->courseContext();
        InstructionalModelSetting::create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $academicYear->id,
            'model' => InstructionalModel::SubjectBasedSchedule,
        ]);
        $studentRecord = StudentRecord::create([
            'user_id' => User::factory()->create()->id,
            'school_id' => $this->workingSchool()->id,
            'academic_cycle_section_id' => $cycleSection->id,
            'admission_number' => fake()->unique()->bothify('####????'),
            'admission_date' => now()->toDateString(),
        ]);
        $courseOffering = app(CreateCourseOffering::class)->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            [],
            RosterMode::IndividualRoster,
            [],
        );

        return [$courseOffering, $studentRecord];
    }

    /**
     * @return array{Subject, AcademicYear, AcademicPeriod, AcademicLevel, AcademicCycleSection}
     */
    public function test_the_add_subject_form_asks_for_sections_and_a_period_of_the_chosen_year(): void
    {
        $this->authorized_user(['create subject', 'read subject']);
        [$subject, $academicYear, , $academicLevel] = $this->courseContext();
        [, , $otherPeriod] = $this->courseContext();
        $before = CourseOffering::query()->where('subject_id', $subject->id)->count();

        Livewire::test(CreateCourseOfferingForm::class, ['academicYearId' => $academicYear->id])
            ->set('subjectId', $subject->id)
            ->set('academicLevelId', $academicLevel->id)
            ->set('rosterMode', RosterMode::HomeSection->value)
            ->set('academicPeriodId', (string) $otherPeriod->id)
            ->call('save')
            ->assertHasErrors(['academicCycleSectionIds' => 'required', 'academicPeriodId']);

        $this->assertSame($before, CourseOffering::query()->where('subject_id', $subject->id)->count());
    }

    public function test_choosing_a_group_switches_the_form_to_everyone_in_the_group(): void
    {
        $this->authorized_user(['create subject', 'read subject']);
        [, $academicYear] = $this->courseContext();
        $group = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id, 'is_group' => true]);

        Livewire::test(CreateCourseOfferingForm::class, ['academicYearId' => $academicYear->id])
            ->set('academicLevelId', $group->id)
            ->assertSet('rosterMode', RosterMode::AcademicLevel->value)
            ->assertSee('Everyone in '.$group->name);
    }

    public function test_a_person_without_create_access_cannot_open_the_add_subject_form(): void
    {
        $this->unauthorized_user();

        Livewire::test(CreateCourseOfferingForm::class)->assertForbidden();
    }

    private function courseContext(AcademicPeriodStatus $periodStatus = AcademicPeriodStatus::Open, ?School $school = null): array
    {
        $school ??= $this->workingSchool();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        /** @var Subject $subject */
        $subject = Subject::factory()->create([
            'school_id' => $school->id,
        ]);
        /** @var AcademicYear $academicYear */
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        /** @var AcademicPeriod $academicPeriod */
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'status' => $periodStatus,
        ]);
        $cycleSection = AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]);

        return [$subject, $academicYear, $academicPeriod, $academicLevel, $cycleSection];
    }
}
