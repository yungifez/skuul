<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Teacher\TeacherService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * TeacherService variable.
     *
     * @var \App\Services\Teacher\TeacherService
     */
    public TeacherService $teacher;

    public function __construct(TeacherService $teacher)
    {
        $this->teacher = $teacher;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', [User::class, 'teacher']);

        return view('pages.teacher.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', [User::class, 'teacher']);

        return view('pages.teacher.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', [User::class, 'teacher']);
        $this->teacher->createTeacher($request);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param User $teacher
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $teacher)
    {
        $this->authorize('view', [$teacher, 'teacher']);

        return view('pages.teacher.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $teacher
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $teacher)
    {
        $this->authorize('update', [$teacher, 'teacher']);

        return view('pages.teacher.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User                     $teacher
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $teacher)
    {
        $this->authorize('update', [$teacher, 'teacher']);
        $this->teacher->updateTeacher($teacher, $request->except('_token', '_method'));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $teacher
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $teacher)
    {
        $this->authorize('delete', [$teacher, 'teacher']);
        $this->teacher->deleteTeacher($teacher);

        return back();
    }
}
