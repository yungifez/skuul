<?php

namespace Tests\Feature;

use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Enums\GradeEntryState;
use App\Enums\LessonNoteStatus;
use App\Enums\ResultApprovalStatus;
use App\Enums\SyllabusStatus;
use App\Enums\TimetableStatus;
use App\Enums\TopicCoverageStatus;
use App\Models\AttendanceRecord;
use App\Models\CourseOffering;
use App\Models\Exam;
use App\Models\GradeEntry;
use App\Models\GradingScale;
use App\Models\GraduationPlan;
use App\Models\LessonNote;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Syllabus;
use App\Models\SyllabusTopicCoverage;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Services\Graduation\GraduationProgress;
use App\Services\Syllabus\SyllabusCoverageService;
use Database\Seeders\Demo\DemoSchool;
use Database\Seeders\DemoSchoolSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoTeachingRecordsTest extends TestCase
{
    use RefreshDatabase;

    private DemoSchool $demo;

    protected function setUp(): void
    {
        parent::setUp();

        $seeder = new DemoSchoolSeeder;
        Model::unguarded(fn () => $seeder->run());
        $this->demo = $seeder->demoSchool();
    }

    public function test_every_section_has_a_published_timetable_and_10a_has_a_draft_revision(): void
    {
        foreach ($this->demo->sections as $name => $section) {
            $published = Timetable::query()
                ->where('academic_cycle_section_id', $section->id)
                ->where('status', TimetableStatus::Published)
                ->sole();

            $this->assertSame("$name weekly schedule", $published->name);
            $this->assertSame(
                5 * 8,
                TimetableRecord::query()->whereIn('timetable_time_slot_id', $published->timeSlots()->select('id'))->count(),
            );
        }

        $draft = Timetable::query()
            ->where('academic_cycle_section_id', $this->demo->sections['10A']->id)
            ->where('status', TimetableStatus::Draft)
            ->sole();

        $this->assertSame(2, $draft->revision);
    }

    public function test_the_latest_register_mixes_attendance_states_and_exams_sit_in_the_semester(): void
    {
        $latest = AttendanceRecord::query()
            ->where('academic_cycle_section_id', $this->demo->sections['10A']->id)
            ->where('kind', AttendanceKind::Daily)
            ->max('attended_on');

        $this->assertNotNull($latest);
        $this->assertTrue(now()->startOfDay()->greaterThanOrEqualTo($latest));

        $statuses = AttendanceRecord::query()
            ->where('academic_cycle_section_id', $this->demo->sections['10A']->id)
            ->whereDate('attended_on', $latest)
            ->get()
            ->map(fn (AttendanceRecord $record): AttendanceStatus => $record->status);

        $this->assertCount(5, $statuses);
        $this->assertContains(AttendanceStatus::Present, $statuses);
        $this->assertContains(AttendanceStatus::Absent, $statuses);
        $this->assertContains(AttendanceStatus::Late, $statuses);
        $this->assertContains(AttendanceStatus::Excused, $statuses);

        $period = $this->demo->currentPeriod;
        $season = strtok($period->name, ' ');
        $midterm = Exam::query()->where('academic_period_id', $period->id)->where('name', "$season midterm exams")->sole();

        $this->assertTrue($midterm->active);
        $this->assertTrue($midterm->start_date->betweenIncluded($period->starts_on, $period->ends_on));
        $this->assertSame(4, Exam::query()->whereIn('academic_period_id', collect($this->demo->periods)->pluck('id'))->count());
    }

    public function test_syllabus_coverage_shows_covered_partial_and_outstanding_topics(): void
    {
        $published = Syllabus::query()->where('status', SyllabusStatus::Published)->count();
        $this->assertSame(56, $published);

        $statuses = SyllabusTopicCoverage::query()->pluck('status');
        $this->assertContains(TopicCoverageStatus::Covered, $statuses);
        $this->assertContains(TopicCoverageStatus::Partial, $statuses);

        $summaries = Syllabus::query()
            ->where('status', SyllabusStatus::Published)
            ->get()
            ->flatMap(fn (Syllabus $syllabus): array => app(SyllabusCoverageService::class)->summary($syllabus));

        $this->assertTrue($summaries->contains(fn (array $row): bool => $row['behind'] > 0), 'No class is behind its plan.');
        $this->assertTrue($summaries->every(fn (array $row): bool => $row['covered'] < $row['total']), 'A class has nothing left to teach.');

        $english = $this->offering('10A', 'English Language Arts');
        $this->assertTrue(LessonNote::query()->where('course_offering_id', $english->id)->where('status', LessonNoteStatus::Submitted)->exists());
        $this->assertTrue(Syllabus::query()->where('course_offering_id', $english->id)->where('status', SyllabusStatus::Draft)->where('revision', 2)->exists());
    }

    public function test_10a_english_has_marks_states_letter_grades_approved_results_and_diploma_progress(): void
    {
        $english = $this->offering('10A', 'English Language Arts');
        $entries = GradeEntry::query()->whereIn('grade_item_id', $english->gradeItems()->select('id'))->get();

        $this->assertTrue($entries->contains(fn (GradeEntry $entry): bool => $entry->state === GradeEntryState::Graded && $entry->points !== null));
        $this->assertTrue($entries->contains('state', GradeEntryState::Exempt));
        $this->assertTrue($entries->contains('state', GradeEntryState::Absent));
        $this->assertTrue($entries->contains(fn (GradeEntry $entry): bool => $entry->grading_scale_option_id !== null));

        $scale = GradingScale::query()->where('name', 'US letter grades')->sole();
        $this->assertSame(['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'F'], $scale->options()->pluck('label')->all());

        $results = ResultSnapshot::query()->whereBelongsTo($english)->get();
        $this->assertSame(6, $results->count());
        $this->assertTrue($results->every(fn (ResultSnapshot $result): bool => $result->approval_status === ResultApprovalStatus::Approved));
        $this->assertSame(2, $results->max('revision'));
        $this->assertSame(
            5,
            ResultSnapshot::query()->whereBelongsTo($this->offering('10B', 'English Language Arts'))->where('approval_status', ResultApprovalStatus::Pending)->count(),
        );

        $diploma = GraduationPlan::query()->where('name', 'Riverside High School Diploma')->sole();
        $enrollment = StudentRecord::query()->where('user_id', $this->demo->demoStudent->id)->sole();
        $progress = app(GraduationProgress::class)->for($diploma, $enrollment);

        $this->assertSame(18, $progress['credits_required']);
        $this->assertGreaterThan(0, $progress['credits_earned']);
        $this->assertCount(3, $progress['stages']);
    }

    private function offering(string $section, string $subject): CourseOffering
    {
        return CourseOffering::query()
            ->where('subject_id', $this->demo->subjects[$subject]->id)
            ->whereHas('cycleSections', fn ($sections) => $sections->whereKey($this->demo->sections[$section]->id))
            ->sole();
    }
}
