<?php

namespace Tests\Feature;

use App\Actions\Attendance\RecordAttendance;
use App\Actions\Notice\PublishNotice;
use App\Enums\AcademicStructureStatus;
use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Enums\Feature;
use App\Enums\ResultApprovalStatus;
use App\Enums\RosterMode;
use App\Livewire\RankingFilters;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\CourseOffering;
use App\Models\Notice;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use App\Services\Attendance\AttendanceSummary;
use App\Services\Ranking\ResultRanking;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Notices, rankings, and attendance figures follow subject rosters, for campuses where learners move between rooms.
 */
class SubjectBasedReportingTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private CourseOffering $chemistry;

    private StudentRecord $first;

    private StudentRecord $second;

    private StudentRecord $outsider;

    protected function setUp(): void
    {
        parent::setUp();

        $period = AcademicPeriod::query()->findOrFail(current_academic_period_id());
        $period->update(['starts_on' => now()->subMonth()->toDateString(), 'ends_on' => now()->addMonths(3)->toDateString()]);
        [$tenA, $tenB] = AcademicCycleSection::factory()->count(2)->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $period->academic_year_id,
            'status' => AcademicStructureStatus::Active,
        ]);
        $this->first = $this->learnerIn($tenA, 'Ada');
        $this->second = $this->learnerIn($tenB, 'Bola');
        $this->outsider = $this->learnerIn($tenA, 'Chidi');
        $this->chemistry = $this->namedOffering($period, $tenA, 'Chemistry', [$this->first, $this->second]);
    }

    public function test_a_notice_reaches_the_learners_of_a_subject_from_any_section(): void
    {
        $this->authorized_user([]);
        $notice = Notice::create([
            'title' => 'Bring goggles',
            'content' => 'Bring goggles to chemistry.',
            'start_date' => now()->toDateString(),
            'stop_date' => now()->addWeek()->toDateString(),
            'school_id' => $this->workingSchool()->id,
            'audience' => ['scope' => 'offering', 'course_offering_ids' => [$this->chemistry->id]],
        ]);

        app(PublishNotice::class)->publish($notice);

        $this->assertEqualsCanonicalizing([$this->first->user_id, $this->second->user_id], $notice->recipients()->pluck('user_id')->all());
    }

    public function test_the_learners_of_one_subject_are_ranked_by_their_result_in_it(): void
    {
        $this->authorized_user(['read ranking']);
        features()->enable(Feature::Ranking);
        $this->publishResult($this->first, $this->chemistry, 60);
        $this->publishResult($this->second, $this->chemistry, 80);
        $art = $this->namedOffering($this->chemistry->academicPeriod, $this->first->academicCycleSection, 'Art', [$this->first, $this->outsider]);
        $this->publishResult($this->first, $art, 99);
        $this->publishResult($this->outsider, $art, 10);

        $rows = app(ResultRanking::class)->forCourseOffering($this->chemistry);

        $this->assertSame([$this->second->id, $this->first->id], $rows->pluck('student_record_id')->all());
        $this->assertSame([80.0, 60.0], $rows->pluck('average')->all());

        Livewire::test(RankingFilters::class)
            ->set('courseOfferingId', $this->chemistry->id)
            ->assertSee('Bola')
            ->assertDontSee('Chidi');
    }

    public function test_a_campus_without_a_daily_register_counts_its_lessons(): void
    {
        $this->authorized_user([]);
        features()->enable(Feature::Attendance, config: ['daily_register' => false]);
        $record = app(RecordAttendance::class);
        $record->record($this->first, AttendanceStatus::Present, now()->subDay(), AttendanceKind::Period, courseOffering: $this->chemistry);
        $record->record($this->first, AttendanceStatus::Absent, now(), AttendanceKind::Period, courseOffering: $this->chemistry);

        $summary = app(AttendanceSummary::class)->forStudent($this->first);

        $this->assertSame(AttendanceKind::Period->value, $summary['kind']);
        $this->assertSame(2, $summary['recorded']);
        $this->assertSame(50.0, $summary['rate']);

        $this->actingAsMemberOf($this->workingSchool(), $this->first->user)
            ->get(route('portal.attendance.show', $this->first))
            ->assertOk()
            ->assertSee('2 lessons recorded')
            ->assertSee('Lessons missed');
    }

    private function learnerIn(AcademicCycleSection $section, string $name): StudentRecord
    {
        $user = $this->memberOf($this->workingSchool(), User::factory()->create(['name' => $name]));
        school_context()->set($this->workingSchool(), remember: false);
        $user->assignRole('student');

        return StudentRecord::create([
            'user_id' => $user->id,
            'school_id' => $section->school_id,
            'academic_cycle_section_id' => $section->id,
            'admission_number' => fake()->unique()->bothify('####????'),
            'admission_date' => now()->subYear()->toDateString(),
            'is_primary' => true,
        ]);
    }

    /**
     * @param  array<int, StudentRecord>  $learners
     */
    private function namedOffering(AcademicPeriod $period, AcademicCycleSection $section, string $subjectName, array $learners): CourseOffering
    {
        $offering = CourseOffering::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'academic_year_id' => $period->academic_year_id,
            'academic_period_id' => $period->id,
            'academic_level_id' => $section->academic_level_id,
            'subject_id' => Subject::factory()->create(['school_id' => $this->workingSchool()->id, 'name' => $subjectName])->id,
            'roster_mode' => RosterMode::IndividualRoster,
        ]);
        $offering->studentRecords()->attach(array_map(fn (StudentRecord $learner): int => $learner->id, $learners));

        return $offering;
    }

    private function publishResult(StudentRecord $learner, CourseOffering $offering, float $percentage): void
    {
        ResultSnapshot::create([
            'school_id' => $learner->school_id,
            'student_record_id' => $learner->id,
            'course_offering_id' => $offering->id,
            'revision' => 1,
            'percentage' => $percentage,
            'payload' => ['percentage' => $percentage],
            'approval_status' => ResultApprovalStatus::Approved,
            'approved_at' => now(),
            'published_at' => now(),
        ]);
    }
}
