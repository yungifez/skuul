<?php

namespace App\Http\Controllers;

use App\Http\Requests\MyClassStoreRequest;
use App\Http\Requests\MyClassUpdateRequest;
use App\Models\MyClass;
use App\Services\MyClass\MyClassService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MyClassController extends Controller
{
    /**
     * Class service instance.
     */
    public MyClassService $myClassService;

    //construct method
    public function __construct(MyClassService $myClassService)
    {
        $this->myClassService = $myClassService;
        $this->authorizeResource(MyClass::class, 'class');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.class.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.class.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MyClassStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->myClassService->createClass($data);

        return back()->with('success', __('Class created successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(MyClass $class): View
    {
        $data['class'] = $class;

        return view('pages.class.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MyClass $class): View
    {
        $data['myClass'] = $class;

        return view('pages.class.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MyClassUpdateRequest $request
     */
    public function update(MyClassUpdateRequest $request, MyClass $class): RedirectResponse
    {
        $data = $request->validated();
        $this->myClassService->updateClass($class, $data);

        return back()->with('success', __('Class updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MyClass $class): RedirectResponse
    {
        $this->myClassService->deleteClass($class);

        return back()->with('success', __('Class deleted successfully'));
    }
}
