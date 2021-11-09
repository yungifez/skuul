<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\School\SchoolService;
use App\Http\Requests\SchoolStoreRequest;

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
        $data = $request->only('name', 'address', 'initials');
        $data['code'] = $this->school->generateSchoolCode();
        $this->school->createSchool($data);

        return back()->with('success', __('School created successfully'));
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

    public function edit($id)
    {
        return view('pages.school.edit');
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
    public function destroy($id)
    {
        //
    }
}
