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
        $this->academicYear->createAcademicYear($data);

        return back();
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
        $this->academicYear->updateAcademicYear($academicYear, $data);

        return back();
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
        $this->academicYear->deleteAcademicYear($academicYear);

        return back();
    }

    public function setAcademicYear(request $request)
    {
        $this->authorize('setAcademicYear', AcademicYear::class);
        $academicYear = $request->academic_year_id;
        $this->academicYear->setAcademicYear($academicYear);

        return back();
    }
}
