<?php

namespace App\Http\Controllers;

use App\Http\Requests\SemesterStoreRequest;
use App\Http\Requests\SetSemesterRequest;
use App\Models\Semester;
use App\Services\Semester\SemesterService;

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
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.semester.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.semester.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SemesterStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SemesterStoreRequest $request)
    {
        $data = $request->except(['_token']);
        $this->semester->createSemester($data);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Semester $semester
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Semester $semester)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Semester $semester
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Semester $semester)
    {
        return view('pages.semester.edit', compact('semester'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SemesterStoreRequest $request
     * @param \App\Models\Semester $semester
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SemesterStoreRequest $request, Semester $semester)
    {
        $data = $request->except('_token', '_method');
        $this->semester->updateSemester($semester, $data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Semester $semester
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Semester $semester)
    {
        $this->semester->deleteSemester($semester);

        return back();
    }

    /**
     * Set school semester.
     *
     * @return \Illuminate\Http\Response
     */
    public function setSemester(SetSemesterRequest $request)
    {
        $this->authorize('setSemester', Semester::class);
        $this->semester->setSemester($request->semester_id);

        return back();
    }
}
