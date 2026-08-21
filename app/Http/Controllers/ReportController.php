<?php

namespace App\Http\Controllers;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Report\RequestReport;
use App\Enums\AuditAction;
use App\Http\Requests\StoreReportRunRequest;
use App\Models\ReportRun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Ask for reports and collect the files.
 */
class ReportController extends Controller
{
    public function __construct(
        private RequestReport $requestReport,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Ask for a report.
     */
    public function store(StoreReportRunRequest $request): RedirectResponse
    {
        $this->authorize('create', ReportRun::class);

        $run = $this->requestReport->request(
            type: $request->string('type')->toString(),
            parameters: $request->input('parameters', []),
            actor: $request->user(),
        );

        return back()->with('success', "The report is being built. It is number $run->id.");
    }

    /**
     * Download the file of a finished report.
     */
    public function download(ReportRun $reportRun): StreamedResponse
    {
        $this->authorize('download', $reportRun);

        abort_unless($reportRun->isReady(), 404, 'This report is not ready yet.');

        $this->auditor->record(AuditAction::ReportDownloaded, $reportRun, ['type' => $reportRun->type]);

        return response()->streamDownload(
            fn () => print (string) Storage::disk('local')->get($reportRun->file_path),
            "$reportRun->type.csv",
            ['Content-Type' => 'text/csv'],
        );
    }
}
