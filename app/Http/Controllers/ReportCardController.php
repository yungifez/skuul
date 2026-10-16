<?php

namespace App\Http\Controllers;

use App\Models\ReportCardSnapshot;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', ReportCardSnapshot::class);

        return view('pages.report-card.index');
    }

    public function show(ReportCardSnapshot $reportCardSnapshot): View
    {
        $this->authorize('view', $reportCardSnapshot);
        $reportCardSnapshot->load(['studentRecord.user:id,name', 'academicYear:id,start_year,stop_year', 'academicPeriod:id,name,label', 'publishedBy:id,name']);

        // Every revision of one card stays readable, so the reader can see what
        // changed between the version they hold and the current one.
        $revisions = ReportCardSnapshot::query()
            ->inSchool()
            ->with('publishedBy:id,name')
            ->where('student_record_id', $reportCardSnapshot->student_record_id)
            ->where('academic_period_id', $reportCardSnapshot->academic_period_id)
            ->orderByDesc('revision')
            ->get();

        return view('pages.report-card.show', compact('reportCardSnapshot', 'revisions'));
    }
}
