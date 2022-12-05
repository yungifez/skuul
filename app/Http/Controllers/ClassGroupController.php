<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Services\MyClass\MyClassService;
use App\Http\Requests\ClassGroupStoreRequest;
use App\Http\Requests\UpdateClassGroupRequest;
use App\Exceptions\ClassGroupNotEmptyException;

class ClassGroupController extends Controller
{
    /**
     * Class service class instance.
     *
     * @var MyClassService
     */
    public MyClassService $myClassService;

    //construct method
    public function __construct(MyClassService $myClassService)
    {
        $this->myClassService = $myClassService;
        $this->authorizeResource(ClassGroup::class, 'class_group');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.class-group.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.class-group.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ClassGroupStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ClassGroupStoreRequest $request)
    {
        $data = $request->except('_token');
        $this->myClassService->createClassGroup($data);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param ClassGroup $classGroup
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ClassGroup $classGroup)
    {
        $data['classGroup'] = $classGroup;

        return view('pages.class-group.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ClassGroup $classGroup
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ClassGroup $classGroup)
    {
        $data['classGroup'] = $classGroup;

        return view('pages.class-group.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClassGroupRequest $request
     * @param ClassGroup             $classGroup
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClassGroupRequest $request, ClassGroup $classGroup)
    {
        $data = $request->except('_token', '_method', 'school_id');
        $this->myClassService->updateClassGroup($classGroup, $data);

        return back()->with('success', __('Class group updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ClassGroup $classGroup
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassGroup $classGroup)
    {
        $this->myClassService->deleteClassGroup($classGroup);

        return back()->with('success', __('Class group deleted successfully'));
    }
}
