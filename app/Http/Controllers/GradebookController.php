<?php

namespace App\Http\Controllers;

use App\Actions\Gradebook\ApplyAssessmentTemplate;
use App\Actions\Gradebook\ApproveResult;
use App\Actions\Gradebook\CreateAssessmentTemplateFromGradebook;
use App\Actions\Gradebook\PublishResult;
use App\Actions\Gradebook\RecordGrade;
use App\Actions\Gradebook\RejectResult;
use App\Enums\GradeEntryState;
use App\Enums\GradeItemType;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\ApplyAssessmentTemplateRequest;
use App\Http\Requests\ApproveGradebookResultRequest;
use App\Http\Requests\PublishGradebookResultRequest;
use App\Http\Requests\RejectGradebookResultRequest;
use App\Http\Requests\StoreAssessmentTemplateRequest;
use App\Http\Requests\StoreGradebookCategoryRequest;
use App\Http\Requests\StoreGradebookEntryRequest;
use App\Http\Requests\StoreGradebookItemRequest;
use App\Http\Requests\UpdateGradebookItemRequest;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AssessmentTemplate;
use App\Models\CourseOffering;
use App\Models\GradeCategory;
use App\Models\GradeItem;
use App\Models\GradingScale;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Gradebook\CourseOfferingRoster;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradebookController extends Controller
{
    public function __construct(
        private CourseOfferingRoster $roster,
        private ApplyAssessmentTemplate $applyAssessmentTemplate,
        private CreateAssessmentTemplateFromGradebook $createAssessmentTemplate,
        private RecordGrade $recordGrade,
        private PublishResult $publishResult,
        private ApproveResult $approveResult,
        private RejectResult $rejectResult,
    ) {}

    /**
     * Show gradebooks for the selected year and period.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAnyGradebooks', CourseOffering::class);

        $academicYears = AcademicYear::inSchool()
            ->with('topLevelPeriods')
            ->orderByDesc('start_year')
            ->orderByDesc('id')
            ->get();
        $requestedAcademicYearId = $request->integer('academic_year_id');
        $selectedAcademicYearId = $requestedAcademicYearId > 0
            ? $requestedAcademicYearId
            : current_academic_year_id();
        $academicYear = $academicYears->firstWhere('id', $selectedAcademicYearId);
        abort_unless($academicYear instanceof AcademicYear, 404);

        $requestedAcademicPeriodId = $request->integer('academic_period_id');
        $hasAcademicPeriodFilter = $request->has('academic_period_id');
        $academicPeriod = $academicYear->topLevelPeriods->firstWhere('id', $requestedAcademicPeriodId);

        if ($requestedAcademicPeriodId > 0) {
            abort_unless($academicPeriod instanceof AcademicPeriod, 404);
        } elseif (!$hasAcademicPeriodFilter && $academicYear->id === current_academic_year_id()) {
            $academicPeriod = current_academic_period();
        }

        $user = $request->user();
        abort_unless($user instanceof User, 403);
        $courseOfferings = CourseOffering::query()
            ->inSchool()
            ->where('academic_year_id', $academicYear->id)
            ->when(
                $academicPeriod instanceof AcademicPeriod,
                fn (Builder $query): Builder => $query->where('academic_period_id', $academicPeriod->id),
            )
            ->when(
                !$user->can('update subject'),
                fn (Builder $query): Builder => $query->whereHas(
                    'teachingAssignments',
                    fn (Builder $assignments): Builder => $assignments->where('user_id', $user->id),
                ),
            )
            ->with([
                'academicLevel:id,name',
                'academicPeriod:id,name,label,status',
                'cycleSections:id,name,label',
                'subject:id,name,short_name',
            ])
            ->orderBy('academic_level_id')
            ->orderBy('subject_id')
            ->paginate(25);

        $selectedAcademicPeriodId = $academicPeriod?->id;

        return view('pages.course-offering.gradebooks', compact('academicPeriod', 'academicYear', 'academicYears', 'courseOfferings', 'selectedAcademicPeriodId', 'selectedAcademicYearId'));
    }

    /**
     * Show the one-screen gradebook for an exact course offering.
     */
    public function show(CourseOffering $courseOffering): View
    {
        $this->authorize('viewGradebook', $courseOffering);

        $courseOffering->load([
            'academicLevel:id,name',
            'academicPeriod:id,name,label,status',
            'academicYear:id,start_year,stop_year',
            'subject:id,name,short_name',
            'gradeCategories:id,course_offering_id,name,aggregation,weight,position',
        ]);
        $students = $this->roster->students($courseOffering);
        $studentIds = $students->pluck('id')->all();
        $gradeItems = $courseOffering->gradeItems()
            ->with([
                'category:id,name',
                'gradingScale:id,name',
                'gradingScale.options:id,grading_scale_id,label,points,position',
                'entries' => fn ($query) => $query->whereIn('student_record_id', $studentIds),
                'entries.gradingScaleOption:id,label,points',
            ])
            ->orderBy('position')
            ->orderBy('id')
            ->get();
        $publishedResults = ResultSnapshot::query()
            ->whereBelongsTo($courseOffering)
            ->whereIn('student_record_id', $studentIds)
            ->approved()
            ->latestRevision()
            ->get()
            ->unique('student_record_id')
            ->keyBy('student_record_id');
        $submittedResults = ResultSnapshot::query()
            ->whereBelongsTo($courseOffering)
            ->whereIn('student_record_id', $studentIds)
            ->latestRevision()
            ->get()
            ->unique('student_record_id')
            ->keyBy('student_record_id');

        $gradingScales = GradingScale::query()
            ->inSchool()
            ->where('is_active', true)
            ->with('options')
            ->orderBy('name')
            ->get();
        $assessmentTemplates = AssessmentTemplate::query()
            ->inSchool()
            ->where('is_active', true)
            ->withCount(['categories', 'items'])
            ->orderBy('name')
            ->get();
        $gradeCategories = $courseOffering->gradeCategories;

        return view('pages.course-offering.gradebook', compact('assessmentTemplates', 'courseOffering', 'gradeCategories', 'gradeItems', 'gradingScales', 'publishedResults', 'students', 'submittedResults'));
    }

    /**
     * Add an assessment item to the offering's gradebook.
     */
    public function storeItem(StoreGradebookItemRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $this->authorize('manageGradebook', $courseOffering);

        try {
            $this->ensureGradebookAcceptsNewWork($courseOffering);
            $attributes = $request->validated();

            if ($attributes['type'] === GradeItemType::Scale->value) {
                $scale = GradingScale::query()
                    ->inSchool()
                    ->findOrFail($attributes['grading_scale_id']);
                $maximumPoints = $scale->options()->max('points');
                $attributes['max_points'] = $maximumPoints === null ? null : (float) $maximumPoints;
            }

            if ($attributes['type'] === GradeItemType::Text->value) {
                $attributes['max_points'] = null;
                $attributes['grading_scale_id'] = null;
            }

            if ($attributes['type'] === GradeItemType::Numeric->value) {
                $attributes['grading_scale_id'] = null;
            }

            GradeItem::create($attributes + [
                'school_id' => $courseOffering->school_id,
                'course_offering_id' => $courseOffering->id,
                'created_by' => $request->user()->id,
            ]);
        } catch (ClosedPeriodException $exception) {
            return back()->withErrors(['gradebook' => $exception->getMessage()]);
        }

        return back()->with('success', 'Assessment added to the gradebook.');
    }

    /**
     * Add a category that groups assessments in this offering.
     */
    public function storeCategory(StoreGradebookCategoryRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        try {
            $this->ensureGradebookAcceptsNewWork($courseOffering);
            GradeCategory::create($request->validated() + [
                'school_id' => $courseOffering->school_id,
                'course_offering_id' => $courseOffering->id,
                'position' => (int) $courseOffering->gradeCategories()->max('position') + 1,
            ]);
        } catch (ClosedPeriodException $exception) {
            return back()->withErrors(['gradebook' => $exception->getMessage()]);
        }

        return back()->with('success', 'Assessment category added.');
    }

    /**
     * Update the structure of one assessment without changing its marks.
     */
    public function updateItem(UpdateGradebookItemRequest $request, CourseOffering $courseOffering, GradeItem $gradeItem): RedirectResponse
    {
        $this->authorize('manageGradebook', $courseOffering);
        $item = $courseOffering->gradeItems()->findOrFail($gradeItem->id);

        try {
            $this->ensureGradebookAcceptsNewWork($courseOffering);
            $item->update($request->validated());
        } catch (ClosedPeriodException $exception) {
            return back()->withErrors(['gradebook' => $exception->getMessage()]);
        }

        return back()->with('success', 'Assessment updated.');
    }

    /**
     * Remove an assessment that has not received learner marks.
     */
    public function destroyItem(CourseOffering $courseOffering, GradeItem $gradeItem): RedirectResponse
    {
        $this->authorize('manageGradebook', $courseOffering);
        $item = $courseOffering->gradeItems()->findOrFail($gradeItem->id);

        try {
            $this->ensureGradebookAcceptsNewWork($courseOffering);

            if ($item->entries()->exists()) {
                return back()->withErrors(['gradebook' => 'An assessment with learner marks cannot be deleted.']);
            }

            $item->delete();
        } catch (ClosedPeriodException $exception) {
            return back()->withErrors(['gradebook' => $exception->getMessage()]);
        }

        return back()->with('success', 'Assessment deleted.');
    }

    /** Save this offering's configured structure as a reusable school template. */
    public function storeAssessmentTemplate(StoreAssessmentTemplateRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        try {
            $this->createAssessmentTemplate->create($courseOffering, $request->validated('template_name'), $request->validated('description'), $request->user());
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['gradebook' => $exception->getMessage()]);
        }

        return back()->with('success', 'Assessment template saved for your school.');
    }

    /** Apply a school template before this offering receives working marks. */
    public function applyAssessmentTemplate(ApplyAssessmentTemplateRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $template = AssessmentTemplate::query()->inSchool()->findOrFail($request->validated('assessment_template_id'));

        try {
            $this->applyAssessmentTemplate->apply($template, $courseOffering, $request->user());
        } catch (ClosedPeriodException|InvalidValueException $exception) {
            return back()->withErrors(['gradebook' => $exception->getMessage()]);
        }

        return back()->with('success', 'Assessment template applied.');
    }

    /**
     * Record one learner's grade from the shared gradebook screen.
     */
    public function storeEntry(StoreGradebookEntryRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $this->authorize('manageGradebook', $courseOffering);
        $data = $request->validated();
        $item = $courseOffering->gradeItems()->findOrFail($data['grade_item_id']);
        $enrollment = StudentRecord::inSchool()->findOrFail($data['student_record_id']);

        try {
            $this->recordGrade->record(
                $item,
                $enrollment,
                GradeEntryState::from($data['state']),
                isset($data['points']) ? (float) $data['points'] : null,
                $data['grading_scale_option_id'] ?? null,
                $data['comment'] ?? null,
                $request->user(),
            );
        } catch (ClosedPeriodException|InvalidValueException $exception) {
            return back()->withErrors(['gradebook' => $exception->getMessage()]);
        }

        return back()->with('success', 'Grade saved.');
    }

    /**
     * Publish one learner's current gradebook calculation.
     */
    public function publish(PublishGradebookResultRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $this->authorize('publishResult', $courseOffering);
        $data = $request->validated();
        $enrollment = StudentRecord::inSchool()->findOrFail($data['student_record_id']);

        try {
            $this->publishResult->publish($courseOffering, $enrollment, $request->user(), $data['reason'] ?? null);
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['gradebook' => $exception->getMessage()]);
        }

        return back()->with('success', 'Result submitted for approval.');
    }

    /**
     * Approve one submitted result from the gradebook workspace.
     */
    public function approve(ApproveGradebookResultRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $result = $courseOffering->resultSnapshots()->findOrFail($request->integer('result_snapshot_id'));

        try {
            $this->approveResult->approve($result, $request->user(), $request->validated('reason'));
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['gradebook' => $exception->getMessage()]);
        }

        return back()->with('success', 'Result approved and made official.');
    }

    /**
     * Reject one submitted result from the gradebook workspace.
     */
    public function reject(RejectGradebookResultRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $result = $courseOffering->resultSnapshots()->findOrFail($request->integer('result_snapshot_id'));

        try {
            $this->rejectResult->reject($result, $request->user(), $request->string('reason')->toString());
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['gradebook' => $exception->getMessage()]);
        }

        return back()->with('success', 'Result rejected. The teacher can submit a new revision.');
    }

    /**
     * Ensure gradebook structure can still be changed for this period.
     *
     * @throws ClosedPeriodException
     */
    private function ensureGradebookAcceptsNewWork(CourseOffering $courseOffering): void
    {
        $courseOffering->loadMissing(['academicPeriod', 'academicYear']);
        $period = $courseOffering->academicPeriod ?? $courseOffering->academicYear;

        if ($period !== null && !$period->status->acceptsNewWork()) {
            throw new ClosedPeriodException('You cannot change gradebook structure while the academic period is closing or closed.');
        }
    }
}
