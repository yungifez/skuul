<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Teacher\TeacherService;
use Illuminate\Auth\Access\AuthorizationException;
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
     * Display the specified resource.
     *
     *
     * @throws AuthorizationException
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
     * @throws AuthorizationException
     */
    public function edit(User $teacher): View
    {
        $this->authorize('update', [$teacher, 'teacher']);

        return view('pages.teacher.edit', compact('teacher'));
    }
}
