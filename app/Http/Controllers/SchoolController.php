<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\School\SchoolService;
use App\Http\Requests\SchoolSetRequest;
use App\Http\Requests\SchoolStoreRequest;
use App\Http\Requests\SchoolUpdateRequest;

class SchoolController extends Controller
{
    public $school;

    public function __construct(SchoolService $school)
    {
        $this->school = $school;
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

    public function show($id)
    {
        return view('pages.school.show');
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function edit($school)
    {
        $data['school'] = $this->school->getSchoolByIdOrFail($school);

        return view('pages.school.edit', $data);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(SchoolUpdateRequest $request, $id)
    {
        $data = $request->except('_token', '_method');
        $this->school->updateSchool($id, $data);

        return back();
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
       //
    }

    public function settings()
    {
        return redirect()->route('schools.edit', ['school'=> auth()->user()->school_id]);
    }

    public function setSchool(SchoolSetRequest $request)
    {
        $data = $request->only('school_id');
        $this->school->setSchool($data['school_id']);

        return back();
    }
}
