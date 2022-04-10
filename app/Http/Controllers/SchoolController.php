<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolSetRequest;
use App\Http\Requests\SchoolStoreRequest;
use App\Http\Requests\SchoolUpdateRequest;
use App\Models\School;
use App\Services\School\SchoolService;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public $school;

    public function __construct(SchoolService $school)
    {
        $this->school = $school;
        $this->authorizeResource(School::class, 'school');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.school.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.school.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SchoolStoreRequest $request)
    {
        $data = $request->except('_token');
        $this->school->createSchool($data);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(School $school)
    {
        $data['school'] = $school;
        return view('pages.school.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(School $school)
    {
        $data['school'] = $school;

        return view('pages.school.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SchoolUpdateRequest $request, School $school)
    {
        $data = $request->except('_token', '_method');
        $this->school->updateSchool($school, $data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(School $school)
    {
        $this->school->deleteSchool($school);

        return back();
    }

    public function settings()
    {
        return redirect()->route('schools.edit', ['school'=> auth()->user()->school_id]);
    }

    public function setSchool(SchoolSetRequest $request)
    {
        $this->authorize('setSchool', School::class);
        $data = $request->only('school_id');
        $this->school->setSchool($data['school_id']);

        return back();
    }
}
