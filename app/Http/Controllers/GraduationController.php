<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentGraduateRequest;
use App\Models\Graduation;
use App\Models\User;
use App\Services\Student\StudentService;
use App\Services\User\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class GraduationController extends Controller
{
    public $userService;

    public $studentService;

    public function __construct(StudentService $studentService, UserService $userService)
    {
        $this->studentService = $studentService;
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('ViewAny', Graduation::class);

        return view('pages.student.graduation.index');
    }

    /**
     * Graduate view.
     */
    public function graduateView(): View
    {
        $this->authorize('graduate', Graduation::class);

        return view('pages.student.graduation.graduate');
    }

    /**
     * Graduate student.
     */
    public function graduate(StudentGraduateRequest $request): RedirectResponse
    {
        $this->authorize('graduate', Graduation::class);
        $this->studentService->graduateStudents($request->except('_token'));

        return back()->with('success', 'Students graduated Successfully');
    }

    /**
     * Reset user graduation.
     */
    public function resetGraduation(User $student): RedirectResponse
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->studentService->resetGraduation($student);

        return back()->with('success', 'Graduation Reset Successfully');
    }
}
