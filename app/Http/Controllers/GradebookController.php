<?php

namespace App\Http\Controllers;

use App\Actions\Gradebook\PublishResult;
use App\Actions\Gradebook\RecordGrade;
use App\Enums\GradeEntryState;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\PublishGradebookResultRequest;
use App\Http\Requests\StoreGradebookEntryRequest;
use App\Http\Requests\StoreGradebookItemRequest;
use App\Models\CourseOffering;
use App\Models\GradeItem;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Services\Gradebook\CourseOfferingRoster;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GradebookController extends Controller
{
    public function __construct(
        private CourseOfferingRoster $roster,
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
                'entries' => fn ($query) => $query->whereIn('student_record_id', $studentIds),
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

        return view('pages.course-offering.gradebook', compact('courseOffering', 'gradeItems', 'publishedResults', 'students'));
    }

    /**
     * Add an assessment item to the offering's gradebook.
     */
    public function storeItem(StoreGradebookItemRequest $request, CourseOffering $courseOffering): RedirectResponse
    {
        $this->authorize('manageGradebook', $courseOffering);

        GradeItem::create($request->validated() + [
            'school_id' => $courseOffering->school_id,
            'course_offering_id' => $courseOffering->id,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Assessment added to the gradebook.');
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
                $data['scale_value'] ?? null,
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
