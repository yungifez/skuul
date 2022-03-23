<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Requests\AcademicYearStoreRequest;
use App\Services\AcademicYear\AcademicYearService;

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
     * @param  \Illuminate\Http\Request  $request
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicYear $academicYear)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
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
