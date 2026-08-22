<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectStoreRequest;
use App\Models\Subject;
use App\Services\Subject\SubjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public $subject;

    public function __construct(SubjectService $subject)
    {
        $this->subject = $subject;
        $this->authorizeResource(Subject::class, 'subject');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.subject.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.subject.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubjectStoreRequest $request): RedirectResponse
    {
        $this->subject->createSubject($request->validated());

        return back()->with('success', 'Subject created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject): Response
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject): View
    {
        $data['subject'] = $subject;

        return view('pages.subject.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubjectStoreRequest $request, Subject $subject): RedirectResponse
    {
        $this->subject->updateSubject($subject, $request->validated());

        return back()->with('success', 'Subject updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        $this->subject->deleteSubject($subject);

        return back()->with('success', 'Subject deleted successfully');
    }
}
