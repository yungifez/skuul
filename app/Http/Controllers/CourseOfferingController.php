<?php

namespace App\Http\Controllers;

use App\Actions\Curriculum\AssignTeacher;
use App\Actions\Curriculum\ChangeCourseOfferingStatus;
use App\Actions\Curriculum\CreateCourseOffering;
use App\Enums\AcademicStructureStatus;
use App\Enums\CourseOfferingStatus;
use App\Enums\Role;
use App\Enums\RosterMode;
use App\Enums\TeachingRole;
use App\Http\Requests\AssignTeacherToCourseOfferingRequest;
use App\Http\Requests\ChangeCourseOfferingStatusRequest;
use App\Http\Requests\StoreCourseOfferingRequest;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseOfferingController extends Controller
{
    public function __construct(
        private CreateCourseOffering $createCourseOffering,
        private ChangeCourseOfferingStatus $changeCourseOfferingStatus,
        private AssignTeacher $assignTeacher,
    ) {
        $this->authorizeResource(CourseOffering::class, 'courseOffering');
    }

    public function index(): View
    {
        $courseOfferings = CourseOffering::inSchool()
            ->with([
                'academicLevel:id,name',
                'academicPeriod:id,name,label',
                'academicYear:id,start_year,stop_year',
                'cycleSections:id,name,label',
                'studentRecords.user:id,name',
                'subject:id,name,short_name',
                'teachingAssignments.teacher:id,name',
            ])
            ->latest()
            ->paginate(25);
        $teachers = User::ofSchool()->role(Role::Teacher->value)->get(['users.id', 'users.name']);

        return view('pages.course-offering.index', compact('courseOfferings', 'teachers'));
    }

    public function create(): View
    {
        $academicYears = AcademicYear::inSchool()->with('academicPeriods')->orderByDesc('start_year')->get();
        $academicLevels = AcademicLevel::inSchool()->orderBy('position')->orderBy('name')->get();
        $academicCycleSections = AcademicCycleSection::inSchool()
            ->with(['academicLevel:id,name', 'academicYear:id,start_year,stop_year'])
            ->where('status', '!=', AcademicStructureStatus::Archived)
            ->orderByDesc('academic_year_id')
            ->orderBy('academic_level_id')
            ->orderBy('position')
            ->orderBy('name')
            ->get();
        $subjects = Subject::inSchool()->orderBy('name')->get();
        $studentRecords = StudentRecord::inSchool()
            ->attending()
            ->with(['academicCycleSection.academicLevel:id,name', 'user:id,name'])
            ->orderBy('admission_number')
            ->get();

        return view('pages.course-offering.create', compact('academicCycleSections', 'academicLevels', 'academicYears', 'studentRecords', 'subjects'));
    }

    public function store(StoreCourseOfferingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $academicYear = AcademicYear::inSchool()->findOrFail($data['academic_year_id']);
        $academicPeriod = AcademicPeriod::inSchool()->findOrFail($data['academic_period_id']);
        $subject = Subject::inSchool()->findOrFail($data['subject_id']);
        $academicLevel = AcademicLevel::inSchool()->findOrFail($data['academic_level_id']);

        $this->createCourseOffering->create(
            $subject,
            $academicYear,
            $academicPeriod,
            $academicLevel,
            $data['academic_cycle_section_ids'] ?? [],
            RosterMode::from($data['roster_mode']),
            $data['student_record_ids'] ?? [],
            $data['planned_periods_per_week'] ?? null,
            $data['capacity'] ?? null,
            $request->user(),
        );

        return redirect()->route('course-offerings.index')->with('success', 'Course offering created as a draft. Activate it when the academic period opens.');
    }

    public function activate(ChangeCourseOfferingStatusRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $this->changeCourseOfferingStatus->change($courseOffering, CourseOfferingStatus::Active, $request->user());

        return back()->with('success', 'Course offering activated.');
    }

    public function assignTeacher(AssignTeacherToCourseOfferingRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $data = $request->validated();
        $teacher = User::ofSchool()->findOrFail($data['teacher_id']);

        $this->assignTeacher->assign(
            $courseOffering,
            $teacher,
            TeachingRole::from($data['role']),
            actor: $request->user(),
        );

        return back()->with('success', 'Teacher assigned to the course offering.');
    }
}
