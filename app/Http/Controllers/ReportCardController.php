<?php

namespace App\Http\Controllers;

use App\Actions\Report\PublishReportCard;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreReportCardRequest;
use App\Models\AcademicPeriod;
use App\Models\ReportCardSnapshot;
use App\Models\StudentRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function __construct(private PublishReportCard $publishReportCard) {}

    public function index(): View
    {
        $this->authorize('viewAny', ReportCardSnapshot::class);

        return view('pages.report-card.index', [
            'reportCards' => ReportCardSnapshot::query()->inSchool()->with(['studentRecord.user:id,name', 'academicPeriod:id,name,label'])->latest('published_at')->paginate(20),
            'students' => StudentRecord::query()->inSchool()->with('user:id,name')->orderBy('admission_number')->get(['id', 'user_id', 'admission_number']),
            'periods' => AcademicPeriod::query()->inSchool()->ordered()->get(['id', 'name', 'label']),
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
        $reportCardSnapshot->load(['studentRecord.user:id,name', 'academicPeriod:id,name,label']);

        return view('pages.report-card.show', compact('reportCardSnapshot'));
    }
}
