<?php

namespace Tests\Feature;

use App\Actions\Finance\ChargeStudent;
use App\Actions\Report\RequestReport;
use App\Enums\AuditAction;
use App\Enums\ReportStatus;
use App\Exceptions\InvalidValueException;
use App\Jobs\BuildReport;
use App\Models\AuditEvent;
use App\Models\ReportRun;
use App\Models\School;
use App\Models\StudentRecord;
use App\Services\Report\ExportFormatRegistry;
use App\Services\Report\ReportRegistry;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Reports are requested, built by a worker, and collected afterwards.
 */
class ReportTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_registry_lists_the_reports(): void
    {
        $reports = app(ReportRegistry::class)->all();

        $this->assertArrayHasKey('student-balances', $reports);
        $this->assertArrayHasKey('class-list', $reports);
    }

    public function test_an_unknown_report_is_refused(): void
    {
        $this->expectException(InvalidValueException::class);

        app(ReportRegistry::class)->get('made-up-report');
    }

    public function test_a_request_is_recorded_and_queued(): void
    {
        Queue::fake();
        $this->authorized_user(['create report']);

        $run = app(RequestReport::class)->request('student-balances');

        $this->assertSame(ReportStatus::Queued, $run->status);
        $this->assertSame($this->workingSchool()->id, $run->school_id);
        Queue::assertPushed(BuildReport::class);
    }

    public function test_building_a_report_writes_a_file(): void
    {
        Storage::fake('local');
        $this->authorized_user(['create report']);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        $run = app(RequestReport::class)->request('student-balances');
        $run = $run->fresh();

        $this->assertSame(ReportStatus::Ready, $run->status);
        $this->assertNotNull($run->file_path);
        Storage::disk('local')->assertExists($run->file_path);
        $this->assertStringContainsString('Admission number', Storage::disk('local')->get($run->file_path));
        $this->assertStringContainsString('500', Storage::disk('local')->get($run->file_path));
    }

    public function test_a_report_only_reads_its_own_school(): void
    {
        Storage::fake('local');
        $this->authorized_user(['create report']);
        StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id, 'admission_number' => 'MINE/1']);
        StudentRecord::factory()->create(['school_id' => School::factory()->create()->id, 'admission_number' => 'THEIRS/1']);

        $run = app(RequestReport::class)->request('student-balances')->fresh();
        $csv = Storage::disk('local')->get($run->file_path);

        $this->assertStringContainsString('MINE/1', $csv);
        $this->assertStringNotContainsString('THEIRS/1', $csv);
    }

    public function test_a_failing_report_says_why(): void
    {
        $this->authorized_user(['create report']);
        $run = ReportRun::create([
            'school_id' => $this->workingSchool()->id,
            'type' => 'made-up-report',
        ]);

        try {
            app(BuildReport::class, ['reportRunId' => $run->id])->handle(app(ReportRegistry::class), app(ExportFormatRegistry::class));
        } catch (InvalidValueException) {
            // The job records the failure before it gives up.
        }

        $run = $run->fresh();

        $this->assertSame(ReportStatus::Failed, $run->status);
        $this->assertStringContainsString('made-up-report', (string) $run->error);
    }

    public function test_an_authorized_user_can_ask_for_a_report(): void
    {
        Queue::fake();

        $this->authorized_user(['create report'])
            ->post('/dashboard/reports', ['type' => 'class-list'])
            ->assertRedirect();

        $this->assertSame(1, ReportRun::count());
    }

    public function test_an_unauthorized_user_cannot_ask_for_a_report(): void
    {
        $this->unauthorized_user()
            ->post('/dashboard/reports', ['type' => 'class-list'])
            ->assertForbidden();
    }

    public function test_an_unknown_report_name_is_refused_by_the_form(): void
    {
        $this->authorized_user(['create report'])
            ->post('/dashboard/reports', ['type' => 'made-up-report'])
            ->assertSessionHasErrors('type');
    }

    public function test_a_finished_report_can_be_downloaded(): void
    {
        Storage::fake('local');
        $actor = $this->authorized_user(['create report', 'read report']);
        $run = app(RequestReport::class)->request('class-list')->fresh();

        $actor->get("/dashboard/reports/$run->id/download")
            ->assertSuccessful()
            ->assertHeader('content-type', 'text/csv; charset=utf-8');
    }

    public function test_another_school_cannot_download_the_report(): void
    {
        Storage::fake('local');
        $this->authorized_user(['create report', 'read report']);
        $run = app(RequestReport::class)->request('class-list')->fresh();

        $this->authorized_user(['read report'], School::factory()->create())
            ->get("/dashboard/reports/$run->id/download")
            ->assertForbidden();
    }

    public function test_a_download_is_written_to_the_audit_log(): void
    {
        Storage::fake('local');
        $actor = $this->authorized_user(['create report', 'read report']);
        $run = app(RequestReport::class)->request('class-list')->fresh();

        $actor->get("/dashboard/reports/$run->id/download");

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::ReportDownloaded)->forSubject($run)->first());
    }

    public function test_a_request_is_written_to_the_audit_log(): void
    {
        Queue::fake();
        $this->authorized_user(['create report']);

        $run = app(RequestReport::class)->request('class-list');

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::ReportRequested)->forSubject($run)->first());
    }
}
