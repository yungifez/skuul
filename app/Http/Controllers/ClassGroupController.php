<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\ClassGroup;
use Illuminate\Http\RedirectResponse;
use App\Services\MyClass\MyClassService;
use App\Http\Requests\ClassGroupStoreRequest;
use App\Http\Requests\UpdateClassGroupRequest;

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
     */
    public function index(): View
    {
        return view('pages.class-group.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.class-group.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ClassGroupStoreRequest $request
     */
    public function store(ClassGroupStoreRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        $this->myClassService->createClassGroup($data);

        return redirect()->back()->with('success', 'Class Group Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param ClassGroup $classGroup
     * 
     */
    public function show(ClassGroup $classGroup): View
    {
        $data['classGroup'] = $classGroup;

        return view('pages.class-group.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ClassGroup $classGroup
     * 
     */
    public function edit(ClassGroup $classGroup): View
    {
        $data['classGroup'] = $classGroup;

        return view('pages.class-group.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClassGroupRequest $request
     * @param ClassGroup              $classGroup
     */
    public function update(UpdateClassGroupRequest $request, ClassGroup $classGroup): RedirectResponse
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
     */
    public function destroy(ClassGroup $classGroup): RedirectResponse
    {
        $this->myClassService->deleteClassGroup($classGroup);

        return back()->with('success', __('Class group deleted successfully'));
    }
}
