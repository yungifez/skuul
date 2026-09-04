<?php

namespace App\Http\Controllers;

use App\Actions\Report\PublishReportCard;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreReportCardRequest;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\ReportCardSnapshot;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function __construct(private PublishReportCard $publishReportCard) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', ReportCardSnapshot::class);

        $selectedStudent = $request->integer('student_record_id') ?: null;
        $selectedAcademicYear = $request->integer('academic_year_id') ?: null;
        $selectedPeriod = $request->integer('academic_period_id') ?: null;

        $academicYear = $selectedAcademicYear === null
            ? null
            : AcademicYear::query()->inSchool()->findOrFail($selectedAcademicYear);
        $academicPeriod = $selectedPeriod === null
            ? null
            : AcademicPeriod::query()->inSchool()->findOrFail($selectedPeriod);

        if ($academicYear !== null && $academicPeriod !== null && $academicPeriod->academic_year_id !== $academicYear->id) {
            abort(404);
        }

        $reportCards = ReportCardSnapshot::query()
            ->inSchool()
            ->with([
                'studentRecord.user:id,name',
                'academicYear:id,start_year,stop_year',
                'academicPeriod:id,name,label',
            ])
            ->when($selectedStudent !== null, function (Builder $query) use ($selectedStudent): void {
                $query->where('student_record_id', $selectedStudent);
            })
            ->when($academicYear !== null, function (Builder $query) use ($academicYear): void {
                $query->where('academic_year_id', $academicYear->id);
            })
            ->when($selectedPeriod !== null, function (Builder $query) use ($selectedPeriod): void {
                $query->where('academic_period_id', $selectedPeriod);
            })
            ->latest('published_at')
            ->paginate(20)
            ->withQueryString();

        return view('pages.report-card.index', [
            'reportCards' => $reportCards,
            'students' => StudentRecord::query()->inSchool()->with('user:id,name')->orderBy('admission_number')->get(['id', 'user_id', 'admission_number']),
            'academicYears' => AcademicYear::query()->inSchool()->orderByDesc('start_year')->orderByDesc('id')->get(['id', 'start_year', 'stop_year']),
            'periods' => AcademicPeriod::query()->inSchool()->with('academicYear:id,start_year,stop_year')->ordered()->get(['id', 'name', 'label', 'academic_year_id']),
            'selectedStudent' => $selectedStudent,
            'selectedAcademicYear' => $selectedAcademicYear,
            'selectedPeriod' => $selectedPeriod,
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
