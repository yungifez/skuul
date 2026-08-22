<?php

namespace App\Http\Controllers;

use App\Actions\Gradebook\ApplyAssessmentTemplate;
use App\Actions\Gradebook\CreateAssessmentTemplateFromGradebook;
use App\Actions\Gradebook\PublishResult;
use App\Actions\Gradebook\RecordGrade;
use App\Enums\GradeEntryState;
use App\Enums\GradeItemType;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\ApplyAssessmentTemplateRequest;
use App\Http\Requests\PublishGradebookResultRequest;
use App\Http\Requests\StoreAssessmentTemplateRequest;
use App\Http\Requests\StoreGradebookEntryRequest;
use App\Http\Requests\StoreGradebookItemRequest;
use App\Models\AssessmentTemplate;
use App\Models\CourseOffering;
use App\Models\GradeItem;
use App\Models\GradingScale;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Services\Gradebook\CourseOfferingRoster;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GradebookController extends Controller
{
    public function __construct(
        private CourseOfferingRoster $roster,
        private ApplyAssessmentTemplate $applyAssessmentTemplate,
        private CreateAssessmentTemplateFromGradebook $createAssessmentTemplate,
        private RecordGrade $recordGrade,
        private PublishResult $publishResult,
    ) {}

    /**
     * Show the one-screen gradebook for an exact course offering.
     */
    public function show(CourseOffering $courseOffering): View
    {
        $this->authorize('viewGradebook', $courseOffering);

        $courseOffering->load([
            'academicLevel:id,name,label',
            'academicPeriod:id,name,label',
            'academicYear:id,start_year,stop_year',
            'subject:id,name,short_name',
            'gradeCategories:id,course_offering_id,name,position',
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

        return view('pages.course-offering.gradebook', compact('assessmentTemplates', 'courseOffering', 'gradeItems', 'gradingScales', 'publishedResults', 'students'));
    }

    /**
     * Add an assessment item to the offering's gradebook.
     */
    public function storeItem(StoreGradebookItemRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $this->authorize('manageGradebook', $courseOffering);

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

        return back()->with('success', 'Assessment added to the gradebook.');
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
                $data['points'] === null ? null : (float) $data['points'],
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

        return back()->with('success', 'Official result published.');
    }
}
