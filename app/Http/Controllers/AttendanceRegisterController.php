<?php

namespace App\Http\Controllers;

use App\Actions\Attendance\RecordAttendance;
use App\Enums\AttendanceStatus;
use App\Http\Requests\StoreAttendanceRegisterRequest;
use App\Models\AcademicCycleSection;
use App\Models\AttendanceRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceRegisterController extends Controller
{
    public function __construct(private RecordAttendance $recordAttendance)
    {
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('read attendance'), 403);
        $sectionId = $request->integer('academic_cycle_section_id');
        // The second argument of date() is the format, not a fallback, so a
        // request without a day has to choose today for itself.
        $date = $request->date('attended_on') ?? now();
        $sections = AcademicCycleSection::query()->inSchool()->with('academicLevel:id,name,label')->orderBy('name')->get();
        $section = $sectionId === 0 ? null : $sections->firstWhere('id', $sectionId);
        $students = $section === null ? collect() : $section->currentEnrollments()->attending()->with('user:id,name')->orderBy('admission_number')->get();
        $records = $students->isEmpty() ? collect() : AttendanceRecord::query()->onDate($date)->whereIn('student_record_id', $students->pluck('id')->all())->get()->keyBy('student_record_id');

        return view('pages.attendance.register', compact('sections', 'section', 'students', 'records', 'date'));
    }

    public function store(StoreAttendanceRegisterRequest $request): RedirectResponse
    {
        $section = AcademicCycleSection::query()->inSchool()->findOrFail($request->integer('academic_cycle_section_id'));
        $students = $section->currentEnrollments()->attending()->get();
        $statuses = $request->validated('statuses');
        $this->recordAttendance->recordMany($students->map(fn ($student): array => ['enrollment' => $student, 'status' => AttendanceStatus::from($statuses[$student->id])])->all(), $request->date('attended_on'), actor: $request->user());

        return redirect()->route('attendance.register', ['academic_cycle_section_id' => $section->id, 'attended_on' => $request->input('attended_on')])->with('success', 'Attendance register saved.');
    }
}
