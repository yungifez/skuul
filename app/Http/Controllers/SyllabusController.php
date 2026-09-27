<?php

namespace App\Http\Controllers;

use App\Actions\Syllabus\PublishSyllabus;
use App\Actions\Syllabus\ReviseSyllabus;
use App\Enums\SyllabusStatus;
use App\Http\Requests\PublishSyllabusRequest;
use App\Http\Requests\ReviseSyllabusRequest;
use App\Http\Requests\StoreSyllabusRequest;
use App\Http\Requests\UpdateSyllabusRequest;
use App\Models\Syllabus;
use App\Services\Syllabus\SyllabusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SyllabusController extends Controller
{
    public function __construct(private SyllabusService $syllabus, private PublishSyllabus $publishSyllabus, private ReviseSyllabus $reviseSyllabus)
    {
        $this->authorizeResource(Syllabus::class, 'syllabus');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.syllabus.index');
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

        return redirect()->route('syllabi.edit', $syllabus)->with('success', 'Syllabus draft created. Add the weekly topics, then publish it.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Syllabus $syllabus): View
    {
        $syllabus->load('courseOffering.subject', 'courseOffering.academicPeriod', 'courseOffering.academicLevel', 'topics', 'revisionOf', 'publishedBy');

        return view('pages.syllabus.show', compact('syllabus'));
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

    public function revise(ReviseSyllabusRequest $request, Syllabus $syllabus): RedirectResponse
    {
        $revision = $this->reviseSyllabus->revise($syllabus, ['change_note' => $request->validated('change_note')], $request->user());

        return redirect()->route('syllabi.edit', $revision)->with('success', 'A revised draft was created. Make the changes, then publish it.');
    }

    public function publish(PublishSyllabusRequest $request, Syllabus $syllabus): RedirectResponse
    {
        $this->publishSyllabus->publish($syllabus, $request->user());

        return redirect()->route('syllabi.show', $syllabus)->with('success', 'Syllabus published.');
    }
}
