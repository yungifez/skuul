<?php

namespace Tests\Feature;

use App\Actions\Timetable\PublishTimetable;
use App\Enums\AcademicStructureStatus;
use App\Enums\InstructionalModel;
use App\Enums\RosterMode;
use App\Enums\TimetableStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\CreateTimetableForm;
use App\Livewire\EditCourseOfferingRoster;
use App\Livewire\ListTimetablesTable;
use App\Livewire\ManageTimetable;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\CourseOffering;
use App\Models\InstructionalModelSetting;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Models\Weekday;
use App\Policies\TimetablePolicy;
use App\Services\Timetable\TimeSlotService;
use App\Services\Timetable\TimetableConflictChecker;
use App\Services\Timetable\TimetableGrid;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A subject can meet at its own times, apart from any one section.
 *
 * Campuses that teach a timetable of separate subjects, where learners move
 * between rooms, build their week from these timetables.
 */
class SubjectTimetableTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private AcademicPeriod $period;

    private AcademicCycleSection $section;

    protected function setUp(): void
    {
        parent::setUp();

        $this->period = AcademicPeriod::query()->findOrFail(current_academic_period_id());
        $this->period->update(['starts_on' => now()->subMonth()->toDateString(), 'ends_on' => now()->addMonths(3)->toDateString()]);
        InstructionalModelSetting::query()->updateOrCreate(
            ['school_id' => $this->workingSchool()->id, 'academic_year_id' => $this->period->academic_year_id],
            ['model' => InstructionalModel::SubjectBasedSchedule],
        );
        $this->section = AcademicCycleSection::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $this->period->academic_year_id,
            'status' => AcademicStructureStatus::Active,
        ]);
    }

    public function test_the_create_form_makes_a_timetable_for_one_subject(): void
    {
        $this->authorized_user(['create timetable', 'read timetable', 'update timetable']);
        $algebra = $this->namedOffering('Mathematics', 'Algebra B');

        Livewire::test(CreateTimetableForm::class)
            ->set('name', 'Algebra B times')
            ->set('academicPeriodId', $this->period->id)
            ->set('scope', 'offering')
            ->assertSee('Algebra B · Mathematics')
            ->set('courseOfferingId', $algebra->id)
            ->call('save')
            ->assertHasNoErrors();

        $timetable = Timetable::query()->where('name', 'Algebra B times')->sole();
        $this->assertSame($algebra->id, $timetable->course_offering_id);
        $this->assertNull($timetable->academic_cycle_section_id);
    }

    public function test_a_subject_timetable_holds_only_its_own_subject(): void
    {
        $this->authorized_user(['update timetable']);
        $algebra = $this->namedOffering('Mathematics', 'Algebra B');
        $timetable = $this->subjectTimetable($algebra, 'Algebra B times', publish: false);
        $slot = TimetableTimeSlot::create(['timetable_id' => $timetable->id, 'start_time' => '10:00', 'stop_time' => '11:00']);
        $other = $this->subject('History');

        $this->assertSame(['Mathematics'], Livewire::test(ManageTimetable::class, ['timetable' => $timetable])->instance()->subjects->pluck('name')->all());

        $this->expectException(InvalidValueException::class);

        app(TimeSlotService::class)->placeRecord($slot, Weekday::firstOrFail()->id, 'subject', $other->id);
    }

    public function test_the_section_builder_offers_the_subjects_of_its_named_learners(): void
    {
        $this->authorized_user(['update timetable']);
        $learner = $this->learner();
        $this->namedOffering('Spanish', null, [$learner]);
        $timetable = Timetable::factory()->create(['academic_period_id' => $this->period->id, 'academic_cycle_section_id' => $this->section->id]);

        $this->assertContains('Spanish', Livewire::test(ManageTimetable::class, ['timetable' => $timetable])->instance()->subjects->pluck('name')->all());
    }

    public function test_publishing_a_subject_timetable_leaves_the_schoolwide_one_in_use(): void
    {
        $this->authorized_user([]);
        $schoolwide = Timetable::factory()->create(['academic_period_id' => $this->period->id, 'academic_cycle_section_id' => null, 'name' => 'Assemblies']);
        app(PublishTimetable::class)->publish($schoolwide);
        $algebra = $this->namedOffering('Mathematics', 'Algebra B');
        $first = Timetable::factory()->create(['academic_period_id' => $this->period->id, 'academic_cycle_section_id' => null, 'course_offering_id' => $algebra->id]);
        $second = Timetable::factory()->create(['academic_period_id' => $this->period->id, 'academic_cycle_section_id' => null, 'course_offering_id' => $algebra->id]);

        app(PublishTimetable::class)->publish($first);
        app(PublishTimetable::class)->publish($second);

        $this->assertSame(TimetableStatus::Published, $schoolwide->fresh()->status);
        $this->assertSame(TimetableStatus::Archived, $first->fresh()->status);
        $this->assertSame(TimetableStatus::Published, $second->fresh()->status);
    }

    public function test_a_timetable_cannot_belong_to_a_section_and_a_subject(): void
    {
        $algebra = $this->namedOffering('Mathematics', 'Algebra B');

        $this->expectExceptionMessage('one section or one subject, not both');

        Timetable::factory()->create(['academic_period_id' => $this->period->id, 'academic_cycle_section_id' => $this->section->id, 'course_offering_id' => $algebra->id]);
    }

    public function test_only_the_learners_of_a_subject_open_its_timetable(): void
    {
        $taker = $this->learner();
        $other = $this->learner();
        $algebra = $this->namedOffering('Mathematics', 'Algebra B', [$taker]);
        $timetable = $this->subjectTimetable($algebra, 'Algebra B times');

        $this->actingAsMemberOf($this->workingSchool(), $taker->user);
        $this->assertTrue(app(TimetablePolicy::class)->view($taker->user, $timetable));

        $this->actingAsMemberOf($this->workingSchool(), $other->user);
        $this->assertFalse(app(TimetablePolicy::class)->view($other->user, $timetable));
    }

    public function test_a_learner_lists_the_timetables_of_the_subjects_they_take(): void
    {
        $taker = $this->learner();
        $this->subjectTimetable($this->namedOffering('Mathematics', 'Algebra B', [$taker]), 'Algebra B times');
        $this->subjectTimetable($this->namedOffering('Mathematics', 'Algebra A'), 'Algebra A times');

        $this->actingAsMemberOf($this->workingSchool(), $taker->user);

        Livewire::test(ListTimetablesTable::class)
            ->assertSee('Algebra B times')
            ->assertDontSee('Algebra A times');
    }

    public function test_the_grid_names_the_teacher_of_a_subject_timetable(): void
    {
        $this->authorized_user(['read timetable']);
        $algebra = $this->namedOffering('Mathematics', 'Algebra B');
        $teacher = $this->teacherOf($algebra, 'Ms Okafor');
        $timetable = $this->subjectTimetable($algebra, 'Algebra B times', '08:00', '09:00');

        $grid = app(TimetableGrid::class)->of($timetable);

        $this->assertSame([$teacher->name], $grid['rows'][0]['cells'][Weekday::firstOrFail()->id]['teachers']);
    }

    public function test_a_teacher_cannot_teach_two_subject_timetables_at_once(): void
    {
        $this->authorized_user([]);
        $algebraA = $this->namedOffering('Mathematics', 'Algebra A');
        $algebraB = $this->namedOffering('Mathematics', 'Algebra B');
        $teacher = $this->teacherOf($algebraA, 'Ms Okafor');
        $this->teacherOf($algebraB, $teacher);
        $this->subjectTimetable($algebraA, 'Algebra A times', '08:00', '09:00');
        $draft = $this->subjectTimetable($algebraB, 'Algebra B times', '08:30', '09:30', publish: false);

        $conflicts = app(TimetableConflictChecker::class)->conflicts($draft);

        $this->assertContains('Ms Okafor already teaches Algebra A times at 08:30 on that day.', $conflicts);
    }

    public function test_a_learner_cannot_sit_two_subjects_at_once(): void
    {
        $this->authorized_user([]);
        $shared = $this->learner();
        $chemistry = $this->namedOffering('Chemistry', null, [$shared]);
        $art = $this->namedOffering('Art', null, [$shared]);
        $music = $this->namedOffering('Music', null, [$this->learner()]);
        $this->subjectTimetable($chemistry, 'Chemistry times', '10:00', '11:00');
        $this->subjectTimetable($music, 'Music times', '10:00', '11:00');
        $draft = $this->subjectTimetable($art, 'Art times', '10:30', '11:30', publish: false);

        $conflicts = app(TimetableConflictChecker::class)->conflicts($draft);

        $this->assertContains('1 learner of this subject also has Chemistry times at 10:30 on that day.', $conflicts);
        $this->assertCount(1, $conflicts);
    }

    public function test_the_portal_week_joins_the_section_and_subject_timetables(): void
    {
        $learner = $this->learner();
        $homeroom = $this->homeSectionOffering('Homeroom');
        $this->sectionTimetable([[$homeroom->subject, '08:00', '08:30']]);
        $algebra = $this->namedOffering('Mathematics', 'Algebra B', [$learner]);
        $this->teacherOf($algebra, 'Ms Okafor');
        $this->subjectTimetable($algebra, 'Algebra B times', '09:00', '10:00');
        $this->subjectTimetable($this->namedOffering('Mathematics', 'Algebra A'), 'Algebra A times', '11:00', '12:00');

        $this->actingAs($learner->user)
            ->get(route('portal.timetable.show', $learner))
            ->assertOk()
            ->assertSeeInOrder(['Homeroom', 'Algebra B · Mathematics', 'Ms Okafor'])
            ->assertSee('Algebra B times')
            ->assertDontSee('Algebra A times');
    }

    public function test_a_teacher_reads_their_own_week(): void
    {
        $algebra = $this->namedOffering('Mathematics', 'Algebra B');
        $teacher = $this->teacherOf($algebra, 'Ms Okafor');
        $this->subjectTimetable($algebra, 'Algebra B times', '09:00', '10:00');
        $this->subjectTimetable($this->namedOffering('History', null), 'History times', '11:00', '12:00');
        school_context()->set($this->workingSchool(), remember: false);
        $teacher->assignRole('teacher');
        $teacher->givePermissionTo('read timetable');

        $this->actingAsMemberOf($this->workingSchool(), $teacher)
            ->get(route('timetables.week'))
            ->assertOk()
            ->assertSee('Algebra B · Mathematics')
            ->assertSee('09:00')
            ->assertDontSee('History times');
    }

    public function test_a_schoolwide_timetable_shows_its_subjects_to_a_planner(): void
    {
        $planner = $this->authorized_user(['read timetable']);
        $subject = $this->subject('Study hall');
        $timetable = Timetable::factory()->create(['academic_period_id' => $this->period->id, 'academic_cycle_section_id' => null]);
        $this->place($timetable, $subject, '13:00', '14:00');

        $grid = app(TimetableGrid::class)->of($timetable, viewer: auth()->user());

        $this->assertSame('Study hall', $grid['rows'][0]['cells'][Weekday::firstOrFail()->id]['name']);
    }

    public function test_the_roster_editor_names_an_offering(): void
    {
        $this->authorized_user(['update subject']);
        $offering = $this->namedOffering('Mathematics', null);

        Livewire::test(EditCourseOfferingRoster::class, ['courseOffering' => $offering])
            ->set('name', 'Algebra B')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Algebra B', $offering->fresh()->name);
        $this->assertSame('Algebra B · Mathematics', $offering->fresh()->nameWithSubject());
    }

    /**
     * Make a named-learner offering of the current period in the test section's level.
     *
     * @param  array<int, StudentRecord>  $learners
     */
    private function namedOffering(string $subjectName, ?string $name, array $learners = []): CourseOffering
    {
        $offering = CourseOffering::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $this->period->academic_year_id,
            'academic_period_id' => $this->period->id,
            'academic_level_id' => $this->section->academic_level_id,
            'subject_id' => $this->subject($subjectName)->id,
            'name' => $name,
            'roster_mode' => RosterMode::IndividualRoster,
        ]);
        $offering->studentRecords()->attach(array_map(fn (StudentRecord $learner): int => $learner->id, $learners));

        return $offering;
    }

    private function homeSectionOffering(string $subjectName): CourseOffering
    {
        $offering = CourseOffering::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $this->period->academic_year_id,
            'academic_period_id' => $this->period->id,
            'academic_level_id' => $this->section->academic_level_id,
            'subject_id' => $this->subject($subjectName)->id,
            'roster_mode' => RosterMode::HomeSection,
        ]);
        $offering->cycleSections()->attach($this->section);

        return $offering;
    }

    private function subject(string $name): Subject
    {
        return Subject::query()->where('school_id', $this->workingSchool()->id)->where('name', $name)->first()
            ?? Subject::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => $name]);
    }

    /**
     * Make a learner of the test section who can read their timetable.
     */
    private function learner(): StudentRecord
    {
        $user = $this->memberOf($this->workingSchool());
        school_context()->set($this->workingSchool(), remember: false);
        $user->assignRole('student');
        $user->givePermissionTo('read timetable');

        return StudentRecord::create([
            'user_id' => $user->id,
            'school_id' => $this->workingSchool()->id,
            'academic_cycle_section_id' => $this->section->id,
            'admission_number' => fake()->unique()->bothify('####????'),
            'admission_date' => now()->toDateString(),
            'is_primary' => true,
        ]);
    }

    private function teacherOf(CourseOffering $offering, User|string $teacher): User
    {
        $teacher = $teacher instanceof User ? $teacher : $this->memberOf($this->workingSchool(), User::factory()->create(['name' => $teacher]));

        TeachingAssignment::create([
            'school_id' => $offering->school_id,
            'subject_id' => $offering->subject_id,
            'user_id' => $teacher->id,
            'academic_year_id' => $offering->academic_year_id,
            'academic_period_id' => $offering->academic_period_id,
            'course_offering_id' => $offering->id,
            'starts_on' => now()->subDay()->toDateString(),
        ]);

        return $teacher;
    }

    /**
     * Make a timetable for one offering with one weekly lesson on the first weekday.
     */
    private function subjectTimetable(CourseOffering $offering, string $name, string $start = '08:00', string $stop = '09:00', bool $publish = true): Timetable
    {
        $timetable = Timetable::factory()->create([
            'name' => $name,
            'academic_period_id' => $offering->academic_period_id,
            'academic_cycle_section_id' => null,
            'course_offering_id' => $offering->id,
        ]);
        $this->place($timetable, $offering->subject, $start, $stop);

        if ($publish) {
            Timetable::query()->whereKey($timetable->id)->update(['status' => TimetableStatus::Published->value, 'published_at' => now()]);
        }

        return $timetable->fresh();
    }

    /**
     * Publish a timetable for the test section with the given lessons.
     *
     * @param  array<int, array{Subject, string, string}>  $lessons
     */
    private function sectionTimetable(array $lessons): Timetable
    {
        $timetable = Timetable::factory()->create([
            'name' => 'Section week',
            'academic_period_id' => $this->period->id,
            'academic_cycle_section_id' => $this->section->id,
        ]);

        foreach ($lessons as [$subject, $start, $stop]) {
            $this->place($timetable, $subject, $start, $stop);
        }

        Timetable::query()->whereKey($timetable->id)->update(['status' => TimetableStatus::Published->value, 'published_at' => now()]);

        return $timetable;
    }

    private function place(Timetable $timetable, Subject $subject, string $start, string $stop): void
    {
        $slot = TimetableTimeSlot::create(['timetable_id' => $timetable->id, 'start_time' => $start, 'stop_time' => $stop]);
        TimetableRecord::create([
            'timetable_time_slot_id' => $slot->id,
            'weekday_id' => Weekday::firstOrFail()->id,
            'timetable_time_slot_weekdayable_id' => $subject->id,
            'timetable_time_slot_weekdayable_type' => $subject->getMorphClass(),
        ]);
    }
}
