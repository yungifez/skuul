<?php

namespace App\Http\Controllers;

use App\Actions\Report\PublishReportCard;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreReportCardRequest;
use App\Models\AcademicPeriod;
use App\Models\ReportCardSnapshot;
use App\Models\StudentRecord;
use App\Services\Report\ReportCardDirectory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function __construct(
        private PublishReportCard $publishReportCard,
        private ReportCardDirectory $reportCardDirectory,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', ReportCardSnapshot::class);

        return view('pages.report-card.index', [
            'students' => $this->reportCardDirectory->students(),
            'periods' => $this->reportCardDirectory->periods(),
        ]);
    }

    public function store(StoreReportCardRequest $request): RedirectResponse
    {
        $student = StudentRecord::query()->inSchool()->findOrFail($request->integer('student_record_id'));
        $period = AcademicPeriod::query()->inSchool()->findOrFail($request->integer('academic_period_id'));

        try {
            $this->publishReportCard->publish($student, $period, $request->user(), $request->string('reason')->toString() ?: null);
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['report_card' => $exception->getMessage()]);
        }

        return back()->with('success', 'Report card published.');
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
