<?php

namespace App\Http\Controllers;

use App\Actions\Curriculum\AssignTeacher;
use App\Actions\Curriculum\ChangeCourseOfferingStatus;
use App\Actions\Curriculum\CreateCourseOffering;
use App\Actions\Curriculum\CreateCourseOfferingsForLevels;
use App\Actions\Curriculum\CreateCourseOfferingsForSections;
use App\Actions\Curriculum\RollForwardCourseOfferings;
use App\Actions\Curriculum\UpdateCourseOfferingRoster;
use App\Enums\AcademicStructureStatus;
use App\Enums\CourseOfferingStatus;
use App\Enums\Role;
use App\Enums\RosterMode;
use App\Enums\TeachingRole;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\AssignTeacherToCourseOfferingRequest;
use App\Http\Requests\ChangeCourseOfferingStatusRequest;
use App\Http\Requests\RollForwardCourseOfferingsRequest;
use App\Http\Requests\StoreCourseOfferingRequest;
use App\Http\Requests\StoreCourseOfferingsForLevelsRequest;
use App\Http\Requests\UpdateCourseOfferingRosterRequest;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseOfferingController extends Controller
{
    public function __construct(
        private CreateCourseOffering $createCourseOffering,
        private CreateCourseOfferingsForLevels $createCourseOfferingsForLevels,
        private CreateCourseOfferingsForSections $createCourseOfferingsForSections,
        private ChangeCourseOfferingStatus $changeCourseOfferingStatus,
        private AssignTeacher $assignTeacher,
        private RollForwardCourseOfferings $rollForwardCourseOfferings,
        private UpdateCourseOfferingRoster $updateCourseOfferingRoster,
    ) {
        $this->authorizeResource(CourseOffering::class, 'courseOffering');
    }

    public function index(): View
    {
        $academicYearId = request()->integer('academic_year_id');
        $subjectId = request()->integer('subject_id');
        $courseOfferingsQuery = CourseOffering::inSchool()
            ->with([
                'academicLevel:id,name',
                'academicPeriod:id,name,label',
                'academicYear:id,start_year,stop_year',
                'cycleSections:id,name,label',
                'studentRecords.user:id,name',
                'subject:id,name,short_name',
                'teachingAssignments.teacher:id,name',
            ])
            ->when($academicYearId > 0, fn (Builder $query): Builder => $query->where('academic_year_id', $academicYearId))
            ->when($subjectId > 0, fn (Builder $query): Builder => $query->where('subject_id', $subjectId))
            ->latest();
        $courseOfferings = $courseOfferingsQuery->paginate(25)->withQueryString();
        $teachers = User::ofSchool()->role(Role::Teacher->value)->get(['users.id', 'users.name']);
        $selectedAcademicYear = AcademicYear::inSchool()->find($academicYearId);
        $selectedSubject = Subject::inSchool()->find($subjectId);

        return view('pages.course-offering.index', compact('courseOfferings', 'selectedAcademicYear', 'selectedSubject', 'teachers'));
    }

    public function create(): View
    {
        $academicYears = AcademicYear::inSchool()->with('topLevelPeriods')->orderByDesc('start_year')->get();
        $academicLevels = AcademicLevel::inSchool()->orderBy('position')->orderBy('name')->get();
        $selectedAcademicYearId = request()->integer('academic_year_id');
        $academicCycleSectionsQuery = AcademicCycleSection::inSchool()
            ->with(['academicLevel:id,name', 'academicYear:id,start_year,stop_year'])
            ->where('status', '!=', AcademicStructureStatus::Archived)
            ->orderByDesc('academic_year_id')
            ->orderBy('academic_level_id')
            ->orderBy('position')
            ->orderBy('name');

        if ($selectedAcademicYearId > 0) {
            $academicCycleSectionsQuery->where('academic_year_id', $selectedAcademicYearId);
        }

        $academicCycleSections = $academicCycleSectionsQuery->get();
        $subjects = Subject::inSchool()->orderBy('name')->get();
        $studentRecordsQuery = StudentRecord::inSchool()
            ->attending()
            ->with(['academicCycleSection.academicLevel:id,name', 'user:id,name'])
            ->orderBy('admission_number');

        if ($selectedAcademicYearId > 0) {
            $studentRecordsQuery->whereHas('academicCycleSection', function (Builder $query) use ($selectedAcademicYearId): void {
                $query->where('academic_year_id', $selectedAcademicYearId);
            });
        }

        $studentRecords = $studentRecordsQuery->get();
        $selectedAcademicYear = $academicYears->firstWhere('id', $selectedAcademicYearId ?: current_academic_year_id());
        $rosterModes = $selectedAcademicYear instanceof AcademicYear
            ? instructional_model($selectedAcademicYear)->rosterModes()
            : RosterMode::cases();

        return view('pages.course-offering.create', compact('academicCycleSections', 'academicLevels', 'academicYears', 'rosterModes', 'studentRecords', 'subjects'));
    }

    public function bulkCreate(): View
    {
        $this->authorize('viewAny', CourseOffering::class);

        $academicYears = AcademicYear::inSchool()->with('topLevelPeriods')->orderByDesc('start_year')->get();
        $selectedAcademicYear = $academicYears->firstWhere('id', request()->integer('academic_year_id'))
            ?? $academicYears->firstWhere('id', current_academic_year_id())
            ?? $academicYears->first();

        abort_unless($selectedAcademicYear instanceof AcademicYear, 404);

        return view('pages.course-offering.bulk-create', compact('selectedAcademicYear'));
    }

    public function bulkCreateForm(): View
    {
        $this->authorize('create', CourseOffering::class);

        $academicYears = AcademicYear::inSchool()->with('topLevelPeriods')->orderByDesc('start_year')->get();
        $selectedAcademicYear = $academicYears->firstWhere('id', request()->integer('academic_year_id'))
            ?? $academicYears->firstWhere('id', current_academic_year_id())
            ?? $academicYears->first();

        abort_unless($selectedAcademicYear instanceof AcademicYear, 404);

        $academicLevels = AcademicLevel::inSchool()
            ->orderBy('position')
            ->orderBy('name')
            ->get();
        $academicCycleSections = AcademicCycleSection::inSchool()
            ->with('academicLevel:id,name')
            ->where('academic_year_id', $selectedAcademicYear->id)
            ->where('status', '!=', AcademicStructureStatus::Archived)
            ->orderBy('academic_level_id')
            ->orderBy('position')
            ->orderBy('name')
            ->get();
        $subjects = Subject::inSchool()->orderBy('name')->get();
        $rosterModes = instructional_model($selectedAcademicYear)->rosterModes();

        return view('pages.course-offering.bulk-create-form', compact('academicCycleSections', 'academicLevels', 'academicYears', 'rosterModes', 'selectedAcademicYear', 'subjects'));
    }

    public function bulkStore(StoreCourseOfferingsForLevelsRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $academicYear = AcademicYear::inSchool()->findOrFail($data['academic_year_id']);
        $subject = Subject::inSchool()->findOrFail($data['subject_id']);
        $created = $this->createCourseOfferingsForLevels->create(
            $subject,
            $academicYear,
            $data['academic_period_id'],
            $data['configurations'],
            $request->user(),
        );
        $message = $created->count().' level-specific subject '.($created->count() === 1 ? 'offering was' : 'offerings were').' created as drafts.';

        if ($request->boolean('setup')) {
            return to_route('course-offerings.bulk-create', ['academic_year_id' => $academicYear->id, 'setup' => 1])->with('success', $message);
        }

        return to_route('course-offerings.index')->with('success', $message);
    }

    public function rollForwardForm(Request $request): View
    {
        $this->authorize('create', CourseOffering::class);

        $academicYears = AcademicYear::inSchool()->orderByDesc('start_year')->orderByDesc('id')->get();
        $target = AcademicYear::inSchool()->find($request->integer('target_academic_year_id') ?: current_academic_year_id());
        $source = AcademicYear::inSchool()->find($request->integer('source_academic_year_id'));

        if ($source === null && $target !== null) {
            $source = AcademicYear::inSchool()
                ->where('start_year', '<', $target->start_year)
                ->orderByDesc('start_year')
                ->orderByDesc('id')
                ->first();
        }

        $preview = null;
        $problem = null;

        if ($source !== null && $target !== null) {
            try {
                $preview = $this->rollForwardCourseOfferings->preview($source, $target);
            } catch (InvalidValueException $exception) {
                $problem = $exception->getMessage();
            }
        }

        return view('pages.course-offering.roll-forward', compact('academicYears', 'preview', 'problem', 'source', 'target'));
    }

    public function rollForward(RollForwardCourseOfferingsRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $source = AcademicYear::inSchool()->findOrFail($data['source_academic_year_id']);
        $target = AcademicYear::inSchool()->findOrFail($data['target_academic_year_id']);
        $created = $this->rollForwardCourseOfferings->rollForward($source, $target, $request->user());
        $message = $created->count().' level-specific subject '.($created->count() === 1 ? 'offering was' : 'offerings were').' rolled into '.$target->name.' as drafts.';

        if ($request->boolean('setup')) {
            return to_route('academic-years.setup', [$target, 'subjects'])->with('success', $message);
        }

        return to_route('course-offerings.index')->with('success', $message);
    }

    public function edit(CourseOffering $courseOffering): View
    {
        $courseOffering->load(['academicLevel', 'academicPeriod', 'academicYear', 'cycleSections', 'studentRecords']);
        $teachingScopeIds = $courseOffering->academicLevel->teachingScopeIds();
        $academicCycleSections = AcademicCycleSection::inSchool()
            ->with(['academicLevel:id,name', 'academicYear:id,start_year,stop_year'])
            ->where('academic_year_id', $courseOffering->academic_year_id)
            ->whereIn('academic_level_id', $teachingScopeIds)
            ->where('status', '!=', AcademicStructureStatus::Archived)
            ->orderBy('position')
            ->orderBy('name')
            ->get();
        $studentRecords = StudentRecord::inSchool()
            ->attending()
            ->with(['academicCycleSection.academicLevel:id,name', 'user:id,name'])
            ->whereHas('academicCycleSection', function (Builder $query) use ($courseOffering, $teachingScopeIds): void {
                $query->where('academic_year_id', $courseOffering->academic_year_id)
                    ->whereIn('academic_level_id', $teachingScopeIds);
            })
            ->orderBy('admission_number')
            ->get();
        $rosterModes = instructional_model($courseOffering->academicYear)->rosterModes();

        if (!in_array($courseOffering->roster_mode, $rosterModes, true)) {
            $rosterModes[] = $courseOffering->roster_mode;
        }

        if ($courseOffering->academicLevel->is_group) {
            $rosterModes = [RosterMode::AcademicLevel];
        }

        return view('pages.course-offering.edit', compact('academicCycleSections', 'courseOffering', 'rosterModes', 'studentRecords'));
    }

    public function store(StoreCourseOfferingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $academicYear = AcademicYear::inSchool()->findOrFail($data['academic_year_id']);
        $subject = Subject::inSchool()->findOrFail($data['subject_id']);
        $academicLevel = AcademicLevel::inSchool()->findOrFail($data['academic_level_id']);
        $rosterMode = RosterMode::from($data['roster_mode']);
        $academicCycleSectionIds = in_array($rosterMode, [RosterMode::HomeSection, RosterMode::CombinedHomeSections], true)
            ? ($data['academic_cycle_section_ids'] ?? [])
            : [];
        $studentRecordIds = $rosterMode === RosterMode::IndividualRoster
            ? ($data['student_record_ids'] ?? [])
            : [];
        $plannedPeriodsPerWeek = $data['planned_periods_per_week'] ?? null;
        $capacity = $data['capacity'] ?? null;

        if ($rosterMode === RosterMode::HomeSection && count($academicCycleSectionIds) > 1) {
            $courseOfferings = $this->createCourseOfferingsForSections->create(
                $subject,
                $academicYear,
                $data['academic_period_id'],
                $academicLevel,
                $academicCycleSectionIds,
                $plannedPeriodsPerWeek,
                $capacity,
                $request->user(),
            );
        } elseif ($data['academic_period_id'] === 'all') {
            $courseOfferings = $this->createCourseOffering->createForAcademicYear(
                $subject,
                $academicYear,
                $academicLevel,
                $academicCycleSectionIds,
                $rosterMode,
                $studentRecordIds,
                $plannedPeriodsPerWeek,
                $capacity,
                $request->user(),
            );
        } else {
            $courseOffering = $this->createCourseOffering->create(
                $subject,
                $academicYear,
                AcademicPeriod::inSchool()->findOrFail((int) $data['academic_period_id']),
                $academicLevel,
                $academicCycleSectionIds,
                $rosterMode,
                $studentRecordIds,
                $plannedPeriodsPerWeek,
                $capacity,
                $request->user(),
            );
            $courseOfferings = collect([$courseOffering]);
        }

        $message = $courseOfferings->count() === 1
            ? 'Subject added to the school year for review. Activate it when the academic period opens.'
            : $courseOfferings->count().' subject entries added to the school year for review.';

        if ($request->boolean('setup')) {
            return to_route('academic-years.setup', [$academicYear, 'subjects'])
                ->with('success', $message);
        }

        return redirect()->route('course-offerings.index')->with('success', $message);
    }

    public function update(UpdateCourseOfferingRosterRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $data = $request->validated();
        $rosterMode = RosterMode::from($data['roster_mode']);
        $academicCycleSectionIds = $rosterMode->usesHomeSections()
            ? ($data['academic_cycle_section_ids'] ?? [])
            : [];
        $studentRecordIds = $rosterMode === RosterMode::IndividualRoster
            ? ($data['student_record_ids'] ?? [])
            : [];

        $this->updateCourseOfferingRoster->update(
            $courseOffering,
            $rosterMode,
            $academicCycleSectionIds,
            $studentRecordIds,
            $data['academic_level_id'] ?? null,
            $request->user(),
        );

        if ($request->boolean('setup')) {
            return to_route('academic-years.setup', [$courseOffering->academicYear, 'subjects'])
                ->with('success', 'Roster updated.');
        }

        return to_route('course-offerings.index')->with('success', 'Roster updated.');
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
