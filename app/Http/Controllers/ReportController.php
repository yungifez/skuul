<?php

namespace App\Http\Controllers;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\ReportRun;
use App\Services\Report\ExportFormatRegistry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Ask for reports and collect the files.
 */
class ReportController extends Controller
{
    public function __construct(
        private ExportFormatRegistry $formats,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * List what has been asked for, and offer to ask for more.
     */
    public function index(): View
    {
        $this->authorize('viewAny', ReportRun::class);

        return view('pages.report.index');
    }

    /**
     * Download the file of a finished report.
     */
    public function download(ReportRun $reportRun): StreamedResponse
    {
        $this->authorize('download', $reportRun);

        abort_unless($reportRun->isReady(), 404, 'This report is not ready yet.');

        $this->auditor->record(AuditAction::ReportDownloaded, $reportRun, ['type' => $reportRun->type, 'format' => $reportRun->format]);

        $format = $this->formats->get($reportRun->format);

        return response()->streamDownload(
            fn () => print (string) Storage::disk('local')->get($reportRun->file_path),
            $reportRun->type.'.'.$format->extension(),
            ['Content-Type' => $format->mimeType()],
        );
    }
}
