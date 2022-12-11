<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentGraduateRequest;
use App\Models\User;
use App\Services\Student\StudentService;

class GraduationController extends Controller
{
    public $Service;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('view graduations')) {
            return abort(403, 'Unauthorized action.');
        }

        return view('pages.student.graduation.index');
    }

    /**
     * Graduate view
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * 
     * @return \Illuminate\Http\Response
     */
    public function graduateView()
    {
        if (!auth()->user()->can('graduate student')) {
            return abort(403, 'Unauthorized action.');
        }

        return view('pages.student.graduation.graduate');
    }

    /**
     * Graduate student
     *
     * @param StudentGraduateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function graduate(StudentGraduateRequest $request)
    {
        if (!auth()->user()->can('graduate student')) {
            return abort(403, 'Unauthorized action.');
        }
        $data = collect($request->except('_token'));
        $this->studentService->graduateStudents($data);

        return back()->with('success', 'Students graduated Successfully');
    }

    /**
     * Reset user graduation
     *
     * @param User $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetGraduation(User $student)
    {
        if (!auth()->user()->can('reset graduation')) {
            return abort(403, 'Unauthorized action.');
        }
        $this->studentService->user->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->studentService->resetGraduation($student);

        return back()->with('success', 'Graduation Reset Successfully');
    }
}
