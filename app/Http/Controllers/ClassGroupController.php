<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassGroupStoreRequest;
use App\Http\Requests\UpdateClassGroupRequest;
use App\Models\ClassGroup;
use App\Services\MyClass\MyClassService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassGroupController extends Controller
{
    /**
     * Class service class instance.
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
     */
    public function store(ClassGroupStoreRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        $this->myClassService->createClassGroup($data);

        return redirect()->back()->with('success', 'Class Group Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassGroup $classGroup): View
    {
        return view('pages.class-group.show', compact('classGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassGroup $classGroup): View
    {
        return view('pages.class-group.edit', compact('classGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassGroupRequest $request, ClassGroup $classGroup): RedirectResponse
    {
        $this->myClassService->updateClassGroup($classGroup, $request->validated());

        return back()->with('success', __('Class group updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassGroup $classGroup): RedirectResponse
    {
        $this->myClassService->deleteClassGroup($classGroup);

        return back()->with('success', __('Class group deleted successfully'));
    }
}
