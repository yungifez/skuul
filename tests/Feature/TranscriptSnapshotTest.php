<?php

namespace Tests\Feature;

use App\Actions\Report\PublishTranscript;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\TranscriptSnapshot;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranscriptSnapshotTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_transcript_captures_latest_results_across_academic_periods(): void
    {
        $school = $this->workingSchool();
        $actor = $this->memberOf($school);
        $student = StudentRecord::factory()->create(['school_id' => $school->id]);
        $year = AcademicYear::factory()->create(['school_id' => $school->id]);
        $firstPeriod = AcademicPeriod::factory()->create(['school_id' => $school->id, 'academic_year_id' => $year->id]);
        $secondPeriod = AcademicPeriod::factory()->create(['school_id' => $school->id, 'academic_year_id' => $year->id]);
        $first = $this->offering($school->id, $year->id, $firstPeriod->id);
        $second = $this->offering($school->id, $year->id, $secondPeriod->id);
        ResultSnapshot::create(['school_id' => $school->id, 'student_record_id' => $student->id, 'course_offering_id' => $first->id, 'revision' => 1, 'percentage' => 60, 'payload' => [], 'published_at' => now()]);
        $latest = ResultSnapshot::create(['school_id' => $school->id, 'student_record_id' => $student->id, 'course_offering_id' => $first->id, 'revision' => 2, 'percentage' => 80, 'payload' => [], 'published_at' => now()]);
        ResultSnapshot::create(['school_id' => $school->id, 'student_record_id' => $student->id, 'course_offering_id' => $second->id, 'revision' => 1, 'percentage' => 70, 'payload' => [], 'published_at' => now()]);

        $transcript = app(PublishTranscript::class)->publish($student, $actor);

        $this->assertSame(1, $transcript->revision);
        $this->assertCount(2, $transcript->payload['results']);
        $this->assertContains($latest->id, array_column($transcript->payload['results'], 'source_result_snapshot_id'));

        $this->authorized_user(['read report', 'create report']);
        $this->post(route('transcripts.store'), ['student_record_id' => $student->id])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');
        $this->get(route('transcripts.index'))
            ->assertOk()
            ->assertSee('Issued transcripts');
        $this->assertSame(2, TranscriptSnapshot::count());
    }

    private function offering(int $schoolId, int $yearId, int $periodId): CourseOffering
    {
        return CourseOffering::factory()->create(['school_id' => $schoolId, 'academic_year_id' => $yearId, 'academic_period_id' => $periodId, 'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $schoolId])->id, 'subject_id' => Subject::factory()->create(['school_id' => $schoolId])->id]);
    }
}
