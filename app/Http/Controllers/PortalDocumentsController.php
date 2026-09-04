<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\ReportCardSnapshot;
use App\Models\StudentRecord;
use App\Models\TranscriptSnapshot;
use App\Services\Portal\PortalAccess;
use App\Services\Portal\PortalSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Show and download official documents in the family portal.
 */
class PortalDocumentsController extends Controller
{
    public function __construct(private PortalAccess $access, private PortalSummary $summary) {}

    /**
     * Show the latest official documents for one enrollment.
     */
    public function index(Request $request, StudentRecord $studentRecord): View
    {
        $this->ensurePortalAccess($request, $studentRecord);

        $documents = $this->summary->documents($studentRecord);
        abort_if($documents === null, 404);

        return view('pages.portal.documents', [
            'studentRecord' => $studentRecord->load('user:id,name', 'school:id,name'),
            'reportCards' => $documents['reportCards'],
            'transcript' => $documents['transcript'],
        ]);
    }

    /**
     * Download one report card as a standalone HTML document.
     */
    public function downloadReportCard(
        Request $request,
        StudentRecord $studentRecord,
        ReportCardSnapshot $reportCardSnapshot,
    ): StreamedResponse {
        $this->ensurePortalAccess($request, $studentRecord);
        abort_unless($this->snapshotBelongsTo($reportCardSnapshot, $studentRecord), 404);

        return response()->streamDownload(
            fn (): int => print view('pages.portal.document', [
                'title' => 'Report card',
                'studentRecord' => $studentRecord->loadMissing('user:id,name', 'school:id,name'),
                'document' => $reportCardSnapshot->loadMissing('academicYear:id,start_year,stop_year', 'academicPeriod:id,name,label'),
                'type' => 'report-card',
            ])->render(),
            "report-card-{$reportCardSnapshot->id}.html",
            ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    }

    /**
     * Download the latest transcript as a standalone HTML document.
     */
    public function downloadTranscript(
        Request $request,
        StudentRecord $studentRecord,
        TranscriptSnapshot $transcriptSnapshot,
    ): StreamedResponse {
        $this->ensurePortalAccess($request, $studentRecord);
        abort_unless($this->snapshotBelongsTo($transcriptSnapshot, $studentRecord), 404);

        return response()->streamDownload(
            fn (): int => print view('pages.portal.document', [
                'title' => 'Transcript',
                'studentRecord' => $studentRecord->loadMissing('user:id,name', 'school:id,name'),
                'document' => $transcriptSnapshot,
                'type' => 'transcript',
            ])->render(),
            "transcript-{$transcriptSnapshot->id}.html",
            ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    }

    /**
     * Confirm the person and the campus may be used in this portal request.
     */
    private function ensurePortalAccess(Request $request, StudentRecord $studentRecord): void
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Documents, $studentRecord->school_id), 404);
    }

    /**
     * Confirm a snapshot belongs to exactly the enrollment in the URL.
     */
    private function snapshotBelongsTo(
        ReportCardSnapshot|TranscriptSnapshot $snapshot,
        StudentRecord $studentRecord,
    ): bool {
        return $snapshot->school_id === $studentRecord->school_id
            && $snapshot->student_record_id === $studentRecord->id;
    }
}
