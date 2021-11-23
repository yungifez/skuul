<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MyClass\MyClassService;
use App\Http\Requests\ClassGroupStoreRequest;

class ClassGroupController extends Controller
{
    //create public properties
    public $myClass;


    //construct method
    public function __construct(MyClassService $myClass)
    {
        $this->myClass = $myClass;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('pages.class-groups.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        return view('pages.class-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(ClassGroupStoreRequest $request)
    {
        $data = $request->except('_token');
        $this->myClass->createClassGroup($data);
         
        return redirect()->back();
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

    public function edit($id){
        $data['classGroup'] = $this->myClass->getClassGroupById($id);

        return view('pages.class-groups.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(ClassGroupStoreRequest $request, $id){
        $data = $request->except('_token', '_method', 'school_id');
        $this->myClass->updateClassGroup($id, $data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id){
        //
    }

}
