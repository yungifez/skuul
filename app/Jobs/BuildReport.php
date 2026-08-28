<?php

namespace App\Jobs;

use App\Enums\ReportStatus;
use App\Models\ReportRun;
use App\Services\Report\ExportFormatRegistry;
use App\Services\Report\ReportRegistry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Build one requested report and store the file.
 *
 * A report over a whole school can take longer than a request should, so it
 * is built by a worker and the requester collects the file afterwards.
 */
class BuildReport implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    public function __construct(private int $reportRunId) {}

    /**
     * Build the report.
     */
    public function handle(ReportRegistry $registry, ExportFormatRegistry $formats): void
    {
        $run = ReportRun::find($this->reportRunId);

        if ($run === null || $run->status === ReportStatus::Ready) {
            return;
        }

        $run->status = ReportStatus::Running;
        $run->started_at = now();
        $run->save();

        try {
            $report = $registry->get($run->type);
            $format = $formats->get($run->format);
            $rows = $report->rows(array_merge($run->parameters ?? [], [
                'school_id' => $run->school_id,
                'academic_year_id' => $run->academic_year_id,
                'academic_period_id' => $run->academic_period_id,
                'financial_period_id' => $run->financial_period_id,
            ]));

            $path = "reports/$run->school_id/$run->id-$run->type.".$format->extension();
            Storage::disk('local')->put($path, $format->render($report->title(), $report->columns(), $rows));

            $run->file_path = $path;
            $run->row_count = $rows->count();
            $run->status = ReportStatus::Ready;
            $run->completed_at = now();
            $run->save();
        } catch (Throwable $exception) {
            $run->status = ReportStatus::Failed;
            $run->error = $exception->getMessage();
            $run->completed_at = now();
            $run->save();

            throw $exception;
        }
    }
}
