<?php

namespace App\Http\Controllers;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Report\RequestReport;
use App\Enums\AuditAction;
use App\Http\Requests\StoreReportRunRequest;
use App\Models\FinancialPeriod;
use App\Models\ReportRun;
use App\Services\Report\ExportFormatRegistry;
use App\Services\Report\ReportRegistry;
use Illuminate\Contracts\View\View;
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
        private ExportFormatRegistry $formats,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * List what has been asked for, and offer to ask for more.
     */
    public function index(ReportRegistry $reports): View
    {
        $this->authorize('viewAny', ReportRun::class);

        $runs = ReportRun::query()
            ->inSchool()
            ->with('requestedBy')
            ->latest('id')
            ->paginate(20);

        return view('pages.report.index', [
            'runs' => $runs,
            'reports' => $reports->all(),
            'formats' => $this->formats->all(),
            'canRequest' => auth()->user()->can('create', ReportRun::class),
            'financialPeriods' => FinancialPeriod::query()->inSchool()->orderByDesc('starts_on')->get(),
        ]);
    }

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
            format: $request->filled('format') ? $request->string('format')->toString() : 'csv',
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

        $this->auditor->record(AuditAction::ReportDownloaded, $reportRun, ['type' => $reportRun->type, 'format' => $reportRun->format]);

        $format = $this->formats->get($reportRun->format);

        return response()->streamDownload(
            fn () => print (string) Storage::disk('local')->get($reportRun->file_path),
            $reportRun->type.'.'.$format->extension(),
            ['Content-Type' => $format->mimeType()],
        );
    }
}
