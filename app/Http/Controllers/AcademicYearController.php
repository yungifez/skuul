<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicYearStoreRequest;
use App\Models\AcademicYear;
use App\Services\AcademicYear\AcademicYearService;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public $academicYear;

    public function __construct(AcademicYearService $academicYear)
    {
        $this->academicYear = $academicYear;
        $this->authorizeResource(AcademicYear::class, 'academic_year');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.academic-year.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.academic-year.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AcademicYearStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicYearStoreRequest $request)
    {
        $data = $request->except('_token');
        try {
            $this->academicYear->createAcademicYear($data);
        } catch (\Throwable $th) {
            return back()->with('danger', "Academic year could not be created successfully");
        }
        
       
        return back()->with('success', 'Academic year created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param AcademicYear $academicYear
     *
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicYear $academicYear)
    {
        return view('pages.academic-year.show', compact('academicYear'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AcademicYear $academicYear
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('pages.academic-year.edit', compact('academicYear'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AcademicYearStoreRequest $request
     * @param AcademicYear             $academicYear
     *
     * @return \Illuminate\Http\Response
     */
    public function update(AcademicYearStoreRequest $request, AcademicYear $academicYear)
    {
        $data = $request->except('_token', '_method');
        try {
            $this->academicYear->updateAcademicYear($academicYear, $data);  
        } catch (\Throwable $th) {
            return back()->with('danger', 'Academic year could not be updated');
        }

        return back()->with('success', 'Academic year updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AcademicYear $academicYear
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicYear $academicYear)
    {
        try {
            $this->academicYear->deleteAcademicYear($academicYear);
        } catch (\Throwable $th) {
            return back()->with('danger', 'Academic year could not be deleted');
        }
        

        return back()->with('success', 'Academic year deleted successfully');
    }

    public function setAcademicYear(request $request)
    {
        $this->authorize('setAcademicYear', AcademicYear::class);
        $academicYear = $request->academic_year_id;
        try {
            $this->academicYear->setAcademicYear($academicYear);
        } catch (\Throwable $th) {
            return back()->with('danger', "Academic year could not be set for ".auth()->user()->school->name);
        }
        

        return back()->with('success', "Academic year set for ".auth()->user()->school->name." successfully");
    }
}
