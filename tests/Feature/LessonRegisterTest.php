<?php

namespace Tests\Feature;

use App\Actions\Attendance\RecordAttendance;
use App\Enums\AcademicStructureStatus;
use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Enums\Feature;
use App\Enums\InstructionalModel;
use App\Enums\RosterMode;
use App\Enums\TimetableStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\AttendanceRegister;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\AttendanceRecord;
use App\Models\CourseOffering;
use App\Models\InstructionalModelSetting;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A campus where learners move between rooms takes a register in each lesson.
 */
class LessonRegisterTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private AcademicPeriod $period;

    private CourseOffering $chemistry;

    private StudentRecord $fromTenA;

    private StudentRecord $fromTenB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->period = AcademicPeriod::query()->findOrFail(current_academic_period_id());
        $this->period->update(['starts_on' => now()->subMonth()->toDateString(), 'ends_on' => now()->addMonths(3)->toDateString()]);
        InstructionalModelSetting::query()->updateOrCreate(
            ['school_id' => $this->workingSchool()->id, 'academic_year_id' => $this->period->academic_year_id],
            ['model' => InstructionalModel::SubjectBasedSchedule],
        );
        [$tenA, $tenB] = AcademicCycleSection::factory()->count(2)->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $this->period->academic_year_id,
            'status' => AcademicStructureStatus::Active,
        ]);
        $this->fromTenA = $this->learnerIn($tenA, 'Ada from 10A');
        $this->fromTenB = $this->learnerIn($tenB, 'Bola from 10B');
        $this->learnerIn($tenA, 'Chidi not in chemistry');
        $this->chemistry = CourseOffering::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $this->period->academic_year_id,
            'academic_period_id' => $this->period->id,
            'academic_level_id' => $tenA->academic_level_id,
            'subject_id' => Subject::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => 'Chemistry'])->id,
            'roster_mode' => RosterMode::IndividualRoster,
        ]);
        $this->chemistry->studentRecords()->attach([$this->fromTenA->id, $this->fromTenB->id]);
    }

    public function test_the_teacher_takes_the_register_of_their_lesson_from_its_roster(): void
    {
        $teacher = $this->teacher(teaching: $this->chemistry);

        $this->actingAsMemberOf($this->workingSchool(), $teacher);

        Livewire::test(AttendanceRegister::class, ['registerType' => 'lesson', 'courseOfferingId' => (string) $this->chemistry->id, 'attendedOn' => now()->toDateString()])
            ->assertSee('Ada from 10A')
            ->assertSee('Bola from 10B')
            ->assertDontSee('Chidi not in chemistry')
            ->set('statusesByStudent.'.$this->fromTenB->id, AttendanceStatus::Absent->value)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Lesson register saved.');

        $records = AttendanceRecord::query()->where('course_offering_id', $this->chemistry->id)->get()->keyBy('student_record_id');

        $this->assertCount(2, $records);
        $this->assertSame(AttendanceStatus::Absent, $records[$this->fromTenB->id]->status);
        $this->assertSame(AttendanceKind::Period, $records[$this->fromTenA->id]->kind);
        $this->assertSame($this->chemistry->subject_id, $records[$this->fromTenA->id]->subject_id);
    }

    public function test_a_teacher_of_another_subject_cannot_take_the_register(): void
    {
        $outsider = $this->teacher(teaching: null);

        $this->actingAsMemberOf($this->workingSchool(), $outsider);

        Livewire::test(AttendanceRegister::class, ['registerType' => 'lesson', 'courseOfferingId' => (string) $this->chemistry->id, 'attendedOn' => now()->toDateString()])
            ->assertDontSee('Ada from 10A')
            ->call('save')
            ->assertForbidden();

        $this->assertSame(0, AttendanceRecord::query()->count());
    }

    public function test_two_lessons_of_one_subject_on_a_day_keep_their_own_registers(): void
    {
        $this->authorized_user([]);
        [$morning, $afternoon] = $this->lessonTimes(['08:00', '09:00'], ['13:00', '14:00']);
        $record = app(RecordAttendance::class);

        $record->record($this->fromTenA, AttendanceStatus::Present, now(), AttendanceKind::Period, courseOffering: $this->chemistry, timeSlot: $morning);
        $record->record($this->fromTenA, AttendanceStatus::Absent, now(), AttendanceKind::Period, courseOffering: $this->chemistry, timeSlot: $afternoon);
        $record->record($this->fromTenA, AttendanceStatus::Present, now());

        $records = AttendanceRecord::query()->where('student_record_id', $this->fromTenA->id)->get();

        $this->assertCount(3, $records);
        $this->assertSame(AttendanceStatus::Absent, $records->firstWhere('timetable_time_slot_id', $afternoon->id)->status);
        $this->assertSame(AttendanceStatus::Present, $records->firstWhere('kind', AttendanceKind::Daily)->status);
    }

    public function test_the_register_offers_the_lesson_times_of_the_day(): void
    {
        $teacher = $this->teacher(teaching: $this->chemistry);
        [$morning] = $this->lessonTimes(['08:00', '09:00']);

        $this->actingAsMemberOf($this->workingSchool(), $teacher);

        Livewire::test(AttendanceRegister::class, ['registerType' => 'lesson', 'courseOfferingId' => (string) $this->chemistry->id, 'attendedOn' => now()->toDateString()])
            ->assertSee('08:00–09:00')
            ->set('timeSlotId', (string) $morning->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame([$morning->id], AttendanceRecord::query()->pluck('timetable_time_slot_id')->unique()->values()->all());
    }

    public function test_a_learner_outside_the_roster_is_refused(): void
    {
        $this->authorized_user([]);
        $outsider = StudentRecord::query()->whereHas('user', fn ($user) => $user->where('name', 'Chidi not in chemistry'))->sole();

        $this->expectException(InvalidValueException::class);

        app(RecordAttendance::class)->record($outsider, AttendanceStatus::Present, now(), AttendanceKind::Period, courseOffering: $this->chemistry);
    }

    public function test_a_school_without_lesson_registers_sees_only_the_daily_one(): void
    {
        $teacher = $this->teacher(teaching: $this->chemistry);
        features()->enable(Feature::Attendance, config: ['lesson_register' => false]);

        $this->actingAsMemberOf($this->workingSchool(), $teacher);

        Livewire::test(AttendanceRegister::class)->assertDontSee('The register of one lesson');
    }

    private function learnerIn(AcademicCycleSection $section, string $name): StudentRecord
    {
        return StudentRecord::create([
            'user_id' => User::factory()->create(['name' => $name])->id,
            'school_id' => $section->school_id,
            'academic_cycle_section_id' => $section->id,
            'admission_number' => fake()->unique()->bothify('####????'),
            'admission_date' => now()->subYear()->toDateString(),
        ]);
    }

    private function teacher(?CourseOffering $teaching): User
    {
        $teacher = $this->memberOf($this->workingSchool());
        school_context()->set($this->workingSchool(), remember: false);
        $teacher->assignRole('teacher');
        $teacher->givePermissionTo(['read attendance', 'take attendance']);

        if ($teaching !== null) {
            TeachingAssignment::create([
                'school_id' => $teaching->school_id,
                'subject_id' => $teaching->subject_id,
                'user_id' => $teacher->id,
                'academic_year_id' => $teaching->academic_year_id,
                'academic_period_id' => $teaching->academic_period_id,
                'course_offering_id' => $teaching->id,
                'starts_on' => now()->subDay()->toDateString(),
            ]);
        }

        return $teacher->fresh();
    }

    /**
     * Publish chemistry's own timetable with a lesson today at each of the given times.
     *
     * @param  array{string, string}  ...$times
     * @return array<int, TimetableTimeSlot>
     */
    private function lessonTimes(array ...$times): array
    {
        $timetable = Timetable::factory()->create([
            'academic_period_id' => $this->period->id,
            'academic_cycle_section_id' => null,
            'course_offering_id' => $this->chemistry->id,
        ]);
        $slots = [];

        foreach ($times as [$start, $stop]) {
            $slot = TimetableTimeSlot::create(['timetable_id' => $timetable->id, 'start_time' => $start, 'stop_time' => $stop]);
            TimetableRecord::create([
                'timetable_time_slot_id' => $slot->id,
                'weekday_id' => now()->dayOfWeekIso,
                'timetable_time_slot_weekdayable_id' => $this->chemistry->subject_id,
                'timetable_time_slot_weekdayable_type' => $this->chemistry->subject->getMorphClass(),
            ]);
            $slots[] = $slot;
        }

        Timetable::query()->whereKey($timetable->id)->update(['status' => TimetableStatus::Published->value, 'published_at' => now()]);

        return $slots;
    }
}
