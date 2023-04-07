<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Teacher\TeacherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public TeacherService $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('viewAny', [User::class, 'teacher']);

        return view('pages.teacher.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', [User::class, 'teacher']);

        return view('pages.teacher.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', [User::class, 'teacher']);
        $this->teacherService->createTeacher($request->except('_token'));

        return back()->with('success', 'Teacher Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(User $teacher): View
    {
        $this->authorize('view', [$teacher, 'teacher']);

        return view('pages.teacher.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $teacher): View
    {
        $this->authorize('update', [$teacher, 'teacher']);

        return view('pages.teacher.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $teacher): RedirectResponse
    {
        $this->authorize('update', [$teacher, 'teacher']);
        $this->teacherService->updateTeacher($teacher, $request->except('_token', '_method'));

        return back()->with('success', 'Teacher Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $teacher): RedirectResponse
    {
        $this->authorize('delete', [$teacher, 'teacher']);
        $this->teacherService->deleteTeacher($teacher);

        return back()->with('success', 'Teacher Deleted Successfully');
    }
}
