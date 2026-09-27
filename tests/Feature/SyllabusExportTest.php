<?php

namespace Tests\Feature;

use App\Enums\SyllabusStatus;
use App\Enums\TopicCoverageStatus;
use App\Livewire\SyllabusCoverageReport;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\School;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\SyllabusTopicCoverage;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SyllabusExportTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_reader_prints_the_scheme_of_work(): void
    {
        $syllabus = $this->publishedSyllabus($this->courseOffering());
        SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => 2, 'title' => 'Fractions', 'objectives' => 'Add unlike fractions']);

        $this->authorized_user(['read syllabus'])
            ->get(route('syllabi.print', $syllabus))
            ->assertOk()
            ->assertSee('Scheme of work: '.$syllabus->name)
            ->assertSee('Fractions')
            ->assertSee('Add unlike fractions')
            ->assertSee(route('syllabi.show', $syllabus));
    }

    public function test_the_show_page_links_to_the_printable_scheme(): void
    {
        $syllabus = $this->publishedSyllabus($this->courseOffering());

        $this->authorized_user(['read syllabus'])
            ->get(route('syllabi.show', $syllabus))
            ->assertOk()
            ->assertSee(route('syllabi.print', $syllabus));
    }

    public function test_a_syllabus_of_another_school_cannot_be_printed(): void
    {
        $this->authorized_user(['read syllabus']);
        $otherSchool = School::query()->findOrFail(School::factory()->create()->getKey());
        $syllabus = $this->publishedSyllabus($this->courseOffering($otherSchool));

        $this->get(route('syllabi.print', $syllabus))->assertForbidden();
    }

    public function test_staff_without_read_permission_cannot_print(): void
    {
        $syllabus = $this->publishedSyllabus($this->courseOffering());

        $this->authorized_user([])
            ->get(route('syllabi.print', $syllabus))
            ->assertForbidden();
    }

    public function test_a_reviewer_downloads_the_coverage_report_as_csv(): void
    {
        $this->authorized_user(['read syllabus', 'approve syllabus']);
        $courseOffering = $this->courseOffering();
        $courseOffering->subject->update(['name' => '=Mathematics']);
        $courseOffering->academicLevel->update(['name' => 'Primary5']);
        $syllabus = $this->publishedSyllabus($courseOffering);
        $topic = SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => 1]);
        SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => 2]);
        SyllabusTopicCoverage::query()->create(['syllabus_topic_id' => $topic->id, 'status' => TopicCoverageStatus::Covered, 'covered_on' => now()->toDateString()]);

        $response = Livewire::test(SyllabusCoverageReport::class, ['academicPeriodId' => (string) $courseOffering->academic_period_id])
            ->call('export')
            ->assertFileDownloaded();

        $content = base64_decode((string) data_get($response->effects, 'download.content'));
        $this->assertStringContainsString('Subject,Class,Syllabus,Revision,Topics,Covered,"Partly covered",Skipped,"Behind plan","Percent covered"', $content);
        $this->assertStringContainsString("'=Mathematics,Primary5,", $content);
        $this->assertStringContainsString(',2,1,0,0,', $content);
    }

    public function test_the_coverage_export_leaves_out_classes_on_track_when_asked(): void
    {
        $this->authorized_user(['read syllabus', 'approve syllabus']);
        $courseOffering = $this->courseOffering();
        $syllabus = $this->publishedSyllabus($courseOffering);
        $topic = SyllabusTopic::factory()->create(['syllabus_id' => $syllabus->id, 'week' => 1]);
        SyllabusTopicCoverage::query()->create(['syllabus_topic_id' => $topic->id, 'status' => TopicCoverageStatus::Covered, 'covered_on' => now()->toDateString()]);

        $response = Livewire::test(SyllabusCoverageReport::class, ['academicPeriodId' => (string) $courseOffering->academic_period_id, 'onlyBehind' => true])
            ->call('export')
            ->assertFileDownloaded();

        $content = base64_decode((string) data_get($response->effects, 'download.content'));
        $this->assertStringNotContainsString($syllabus->name, $content);
    }

    public function test_teachers_cannot_download_the_coverage_report(): void
    {
        $this->authorized_user(['read syllabus']);

        Livewire::test(SyllabusCoverageReport::class)->assertForbidden();
    }

    private function publishedSyllabus(CourseOffering $courseOffering): Syllabus
    {
        return Syllabus::query()->findOrFail(Syllabus::factory()->create([
            'course_offering_id' => $courseOffering->id,
            'status' => SyllabusStatus::Published,
            'published_at' => now(),
        ])->getKey());
    }

    private function courseOffering(?School $school = null): CourseOffering
    {
        $school ??= $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id]);
        $academicPeriod = AcademicPeriod::factory()->create(['school_id' => $school->id, 'academic_year_id' => $academicYear->getKey()]);

        return CourseOffering::query()->findOrFail(CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->getKey(),
            'academic_period_id' => $academicPeriod->getKey(),
            'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $school->id])->id,
            'subject_id' => Subject::factory()->create(['school_id' => $school->id])->id,
        ])->getKey());
    }
}
