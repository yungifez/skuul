<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGradeSystemRequest;
use App\Http\Requests\UpdateGradeSystemRequest;
use App\Models\GradeSystem;
use App\Services\GradeSystem\GradeSystemService;

class GradeSystemController extends Controller
{
    public $gradeSystem;

    public function __construct(GradeSystemService $gradeSystem)
    {
        $this->gradeSystem = $gradeSystem;
        $this->authorizeResource(GradeSystem::class, 'grade_system');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.grade-system.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.grade-system.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreGradeSystemRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGradeSystemRequest $request)
    {
        $data = $request->except('_token');
        $this->gradeSystem->createGradeSystem($data);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\GradeSystem $gradeSystem
     *
     * @return \Illuminate\Http\Response
     */
    public function show(GradeSystem $gradeSystem)
    {
        return view('pages.grade-system.show', compact('gradeSystem'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\GradeSystem $gradeSystem
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(GradeSystem $gradeSystem)
    {
        return view('pages.grade-system.edit', compact('gradeSystem'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateGradeSystemRequest $request
     * @param \App\Models\GradeSystem                     $gradeSystem
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGradeSystemRequest $request, GradeSystem $gradeSystem)
    {
        $data = $request->except('_token');
        $this->gradeSystem->updateGradeSystem($gradeSystem, $data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\GradeSystem $gradeSystem
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(GradeSystem $gradeSystem)
    {
        $this->gradeSystem->deleteGradeSystem($gradeSystem);

        return back();
    }
}
