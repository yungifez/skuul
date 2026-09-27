<?php

namespace App\Http\Controllers;

use App\Actions\Curriculum\RollForwardCourseOfferings;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\RollForwardCourseOfferingsRequest;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseOfferingController extends Controller
{
    public function __construct(
        private RollForwardCourseOfferings $rollForwardCourseOfferings,
    ) {
        $this->authorizeResource(CourseOffering::class, 'courseOffering');
    }

    public function index(): View
    {
        return view('pages.course-offering.index');
    }

    public function create(): View
    {
        return view('pages.course-offering.create');
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

        $academicYears = AcademicYear::inSchool()->orderByDesc('start_year')->get();
        $selectedAcademicYear = $academicYears->firstWhere('id', request()->integer('academic_year_id'))
            ?? $academicYears->firstWhere('id', current_academic_year_id())
            ?? $academicYears->first();

        abort_unless($selectedAcademicYear instanceof AcademicYear, 404);

        return view('pages.course-offering.bulk-create-form', compact('selectedAcademicYear'));
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
        return view('pages.course-offering.edit', compact('courseOffering'));
    }
}
