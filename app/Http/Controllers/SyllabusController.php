<?php

namespace App\Http\Controllers;

use App\Actions\Syllabus\PublishSyllabus;
use App\Actions\Syllabus\ReviseSyllabus;
use App\Http\Requests\PublishSyllabusRequest;
use App\Http\Requests\StoreSyllabusRequest;
use App\Http\Requests\UpdateSyllabusRequest;
use App\Models\Syllabus;
use App\Services\Syllabus\SyllabusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SyllabusController extends Controller
{
    public function __construct(private SyllabusService $syllabus, private PublishSyllabus $publishSyllabus, private ReviseSyllabus $reviseSyllabus)
    {
        $this->syllabus = $syllabus;
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
        $this->publishSyllabus->publish($syllabus, $request->user());

        return redirect()->route('syllabi.index')->with('success', 'Syllabus created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Syllabus $syllabus): View
    {
        $syllabus->load('courseOffering.subject', 'courseOffering.academicPeriod', 'courseOffering.academicLevel');

        return view('pages.syllabus.show', compact('syllabus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Syllabus $syllabus): Response
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSyllabusRequest $request, Syllabus $syllabus): Response
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Syllabus $syllabus): RedirectResponse
    {
        $this->syllabus->deleteSyllabus($syllabus);

        return redirect()->route('syllabi.index')->with('success', 'Syllabus deleted.');
    }

    public function revise(Syllabus $syllabus): RedirectResponse
    {
        $this->authorize('update', $syllabus);
        $revision = $this->reviseSyllabus->revise($syllabus, actor: request()->user());

        return redirect()->route('syllabi.show', $revision)->with('success', 'A new syllabus draft was created. Review it, then publish it.');
    }

    public function publish(PublishSyllabusRequest $request, Syllabus $syllabus): RedirectResponse
    {
        $this->publishSyllabus->publish($syllabus, $request->user());

        return redirect()->route('syllabi.show', $syllabus)->with('success', 'Syllabus published.');
    }
}
