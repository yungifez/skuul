<?php

namespace App\Http\Controllers;

use App\Enums\LessonNoteStatus;
use App\Enums\SyllabusStatus;
use App\Http\Requests\StoreSyllabusRequest;
use App\Http\Requests\UpdateSyllabusRequest;
use App\Models\CurriculumOutline;
use App\Models\LessonNote;
use App\Models\Syllabus;
use App\Services\Print\PrintService;
use App\Services\Syllabus\SyllabusService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SyllabusController extends Controller
{
    public function __construct(private SyllabusService $syllabus)
    {
        $this->authorizeResource(Syllabus::class, 'syllabus');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $isReviewer = auth()->user()?->can('approve syllabus') ?? false;
        $awaitingReview = $isReviewer
            ? Syllabus::query()
                ->inSchool()
                ->where('status', SyllabusStatus::Submitted)
                ->with(['courseOffering.subject:id,name', 'courseOffering.academicLevel:id,name', 'submittedBy:id,name'])
                ->oldest('submitted_at')
                ->get()
            : collect();
        $lessonNotesAwaitingReview = $isReviewer ? $this->lessonNotesAwaitingReview() : collect();

        return view('pages.syllabus.index', compact('awaitingReview', 'lessonNotesAwaitingReview'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.syllabus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSyllabusRequest $request): RedirectResponse
    {
        $syllabus = $this->syllabus->createSyllabus($request->validated());

        return redirect()->route('syllabi.edit', $syllabus)->with('success', 'Syllabus draft created. Add the weekly topics, then send it for review.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Syllabus $syllabus): View
    {
        $syllabus->load('courseOffering.subject', 'courseOffering.academicPeriod', 'courseOffering.academicLevel', 'topics', 'revisionOf', 'publishedBy', 'submittedBy');

        return view('pages.syllabus.show', compact('syllabus'));
    }

    /**
     * Show the syllabus as a scheme of work to print.
     */
    public function print(Syllabus $syllabus): Response
    {
        $this->authorize('view', $syllabus);
        $syllabus->load('courseOffering.subject', 'courseOffering.academicPeriod.academicYear', 'courseOffering.academicLevel', 'topics', 'publishedBy');

        return PrintService::page('pages.syllabus.print', compact('syllabus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Syllabus $syllabus): View|RedirectResponse
    {
        if ($syllabus->status !== SyllabusStatus::Draft) {
            return redirect()->route('syllabi.show', $syllabus)->with('info', 'Only a draft can be edited. Create a revised draft to change this syllabus.');
        }

        $syllabus->load('courseOffering.subject', 'courseOffering.academicPeriod', 'courseOffering.academicLevel', 'revisionOf');

        return view('pages.syllabus.edit', compact('syllabus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSyllabusRequest $request, Syllabus $syllabus): RedirectResponse
    {
        $this->syllabus->updateDraft($syllabus, [
            ...$request->safe()->only(['name', 'description', 'file']),
            'remove_file' => $request->boolean('remove_file'),
        ]);

        return redirect()->route('syllabi.edit', $syllabus)->with('success', 'Syllabus details saved.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Syllabus $syllabus): RedirectResponse
    {
        $this->syllabus->deleteSyllabus($syllabus);

        return redirect()->route('syllabi.index')->with('success', 'Syllabus deleted.');
    }

    /**
     * Show every class's progress through its syllabus.
     */
    public function coverage(): View
    {
        $this->authorize('viewCoverage', Syllabus::class);

        return view('pages.syllabus.coverage');
    }

    /**
     * Show the school's library of reusable schemes of work.
     */
    public function library(): View
    {
        $this->authorize('viewAny', CurriculumOutline::class);

        return view('pages.syllabus.library');
    }

    /**
     * Show the weekly lesson notes written against a published syllabus.
     */
    public function lessonNotes(Syllabus $syllabus): View
    {
        $this->authorize('viewAny', [LessonNote::class, $syllabus]);

        return view('pages.syllabus.lesson-notes', compact('syllabus'));
    }

    /**
     * Count the lesson notes waiting for review, by the published syllabus they follow.
     *
     * @return Collection<int, Syllabus>
     */
    private function lessonNotesAwaitingReview(): Collection
    {
        $pendingByOffering = LessonNote::query()
            ->inSchool()
            ->where('status', LessonNoteStatus::Submitted)
            ->where('user_id', '!=', auth()->id())
            ->selectRaw('course_offering_id, count(*) as pending')
            ->groupBy('course_offering_id')
            ->pluck('pending', 'course_offering_id');

        return Syllabus::query()
            ->whereIn('course_offering_id', $pendingByOffering->keys())
            ->where('status', SyllabusStatus::Published)
            ->with(['courseOffering.subject:id,name', 'courseOffering.academicLevel:id,name'])
            ->get()
            ->each(fn (Syllabus $syllabus) => $syllabus->setAttribute('pending_lesson_notes', (int) $pendingByOffering[$syllabus->course_offering_id]));
    }
}
