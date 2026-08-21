<?php

namespace App\Jobs;

use App\Enums\ReportStatus;
use App\Models\ReportRun;
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
    public function handle(ReportRegistry $registry): void
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
            $rows = $report->rows(($run->parameters ?? []) + ['school_id' => $run->school_id]);

            $path = "reports/$run->school_id/$run->id-$run->type.csv";
            Storage::disk('local')->put($path, $this->toCsv($report->columns(), $rows->all()));

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

    /**
     * Turn the columns and rows into one CSV file.
     *
     * @param  array<int, string>  $columns
     * @param  array<int, array<int, string|int|float|null>>  $rows
     */
    private function toCsv(array $columns, array $rows): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $columns);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
