<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGradeSystemRequest;
use App\Http\Requests\UpdateGradeSystemRequest;
use App\Models\GradeSystem;
use App\Services\GradeSystem\GradeSystemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

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
     */
    public function index(): View
    {
        return view('pages.grade-system.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.grade-system.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGradeSystemRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        $this->gradeSystem->createGradeSystem($data);

        return back()->with('success', 'Grade range created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(GradeSystem $gradeSystem): Response
    {
        abort(404);

        // return view('pages.grade-system.show', compact('gradeSystem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GradeSystem $gradeSystem): View
    {
        return view('pages.grade-system.edit', compact('gradeSystem'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGradeSystemRequest $request, GradeSystem $gradeSystem): RedirectResponse
    {
        $data = $request->except('_token');
        $this->gradeSystem->updateGradeSystem($gradeSystem, $data);

        return back()->with('success', 'Grade range updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GradeSystem $gradeSystem): RedirectResponse
    {
        $this->gradeSystem->deleteGradeSystem($gradeSystem);

        return back()->with('success', 'successfully deleted grade');
    }
}
