<?php

namespace Tests\Feature;

use App\Actions\Report\PublishReportCard;
use App\Enums\AcademicPeriodStatus;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\ReportCardSnapshot;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportCardSnapshotTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_report_card_retains_the_latest_result_of_each_subject_and_revisions(): void
    {
        $school = $this->workingSchool();
        $actor = $this->memberOf($school);
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create(['school_id' => $school->id, 'academic_year_id' => $academicYear->id, 'status' => AcademicPeriodStatus::Closing]);
        $student = StudentRecord::factory()->create(['school_id' => $school->id]);
        $firstOffering = $this->offering($school->id, $academicYear->id, $academicPeriod->id);
        $secondOffering = $this->offering($school->id, $academicYear->id, $academicPeriod->id);
        $older = ResultSnapshot::create(['school_id' => $school->id, 'student_record_id' => $student->id, 'course_offering_id' => $firstOffering->id, 'revision' => 1, 'percentage' => 60, 'payload' => ['percentage' => 60], 'published_at' => now()]);
        $latest = ResultSnapshot::create(['school_id' => $school->id, 'student_record_id' => $student->id, 'course_offering_id' => $firstOffering->id, 'revision' => 2, 'percentage' => 80, 'payload' => ['percentage' => 80], 'published_at' => now()]);
        ResultSnapshot::create(['school_id' => $school->id, 'student_record_id' => $student->id, 'course_offering_id' => $secondOffering->id, 'revision' => 1, 'percentage' => 70, 'payload' => ['percentage' => 70], 'published_at' => now()]);

        $reportCard = app(PublishReportCard::class)->publish($student, $academicPeriod, $actor);

        $this->assertSame(1, $reportCard->revision);
        $this->assertSame(75.0, $reportCard->average_percentage);
        $this->assertCount(2, $reportCard->payload['results']);
        $this->assertContains($latest->id, array_column($reportCard->payload['results'], 'source_result_snapshot_id'));
        $this->assertNotContains($older->id, array_column($reportCard->payload['results'], 'source_result_snapshot_id'));

        $revision = app(PublishReportCard::class)->publish($student, $academicPeriod, $actor, 'Corrected subject result.');

        $this->assertSame(2, $revision->revision);
        $this->assertSame(1, $reportCard->fresh()->revision);
    }

    public function test_authorized_staff_can_publish_a_report_card_from_the_workspace(): void
    {
        $this->authorized_user(['read report', 'create report']);
        [$student, $period] = $this->reportableStudent();

        $this->get(route('report-cards.index'))
            ->assertOk()
            ->assertSee('Publish a report card');

        $this->post(route('report-cards.store'), [
            'student_record_id'  => $student->id,
            'academic_period_id' => $period->id,
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $reportCard = ReportCardSnapshot::query()->sole();
        $this->assertSame(1, ReportCardSnapshot::count());
        $this->get(route('report-cards.show', $reportCard))
            ->assertOk()
            ->assertSee('Subject results');
    }

    /** @return array{StudentRecord, AcademicPeriod} */
    private function reportableStudent(): array
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $period = AcademicPeriod::factory()->create(['school_id' => $school->id, 'academic_year_id' => $academicYear->id, 'status' => AcademicPeriodStatus::Closing]);
        $student = StudentRecord::factory()->create(['school_id' => $school->id]);
        $offering = $this->offering($school->id, $academicYear->id, $period->id);
        ResultSnapshot::create(['school_id' => $school->id, 'student_record_id' => $student->id, 'course_offering_id' => $offering->id, 'revision' => 1, 'percentage' => 70, 'payload' => ['percentage' => 70], 'published_at' => now()]);

        return [$student, $period];
    }

    private function offering(int $schoolId, int $academicYearId, int $academicPeriodId): CourseOffering
    {
        return CourseOffering::factory()->create(['school_id' => $schoolId, 'academic_year_id' => $academicYearId, 'academic_period_id' => $academicPeriodId, 'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $schoolId])->id, 'subject_id' => Subject::factory()->create(['school_id' => $schoolId])->id]);
    }
}
