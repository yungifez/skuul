<?php

namespace App\Http\Controllers;

use App\Http\Requests\SemesterStoreRequest;
use App\Http\Requests\SetSemesterRequest;
use App\Models\Semester;
use App\Services\Semester\SemesterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SemesterController extends Controller
{
    public $semester;

    public function __construct(SemesterService $semester)
    {
        $this->semester = $semester;
        $this->authorizeResource(Semester::class, 'semester');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.semester.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.semester.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SemesterStoreRequest $request): RedirectResponse
    {
        $data = $request->except(['_token']);
        $this->semester->createSemester($data);

        return back()->with('success', 'Successfully created semester');
    }

    /**
     * Display the specified resource.
     */
    public function show(Semester $semester): Response
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Semester $semester): View
    {
        return view('pages.semester.edit', compact('semester'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SemesterStoreRequest $request, Semester $semester): RedirectResponse
    {
        $data = $request->except('_token', '_method');
        $this->semester->updateSemester($semester, $data);

        return back()->with('success', 'Successfully updated semester');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semester $semester): RedirectResponse
    {
        $this->semester->deleteSemester($semester);

        return back()->with('success', 'Successfully deleted semester');
    }

    /**
     * Set school semester.
     */
    public function setSemester(SetSemesterRequest $request): RedirectResponse
    {
        $this->authorize('setSemester', Semester::class);
        $semester = Semester::findOrFail($request->semester_id);
        $this->semester->setSemester($semester);

        return back()->with('success', 'Successfully set current semester');
    }
}
