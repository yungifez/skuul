<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentStoreRequest;
use App\Models\User;
use App\Services\Student\StudentService;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class StudentController extends Controller
{
    public $student;

    /**
     * Instance of user service class.
     *
     * @var UserService
     */
    public $userService;

    //construct method which assigns studentService to student variable
    public function __construct(StudentService $student, UserService $userService)
    {
        $this->student = $student;
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('viewAny', [User::class, 'student']);

        return view('pages.student.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', [User::class, 'student']);

        return view('pages.student.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StudentStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', [User::class, 'student']);
        $this->student->createStudent($request);

        return back()->with('success', 'Student Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $student): View|Response
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('view', [$student, 'student']);

        //restrict parents from seeing other students profiles
        if (auth()->user()->hasRole('parent') && $student->parents()->where('parent_records.user_id', auth()->user()->id)->count() <= 0) {
            abort(404);
        }

        return view('pages.student.show', compact('student'));
    }

    /**
     * Print student Profile.
     */
    public function printProfile(User $student): Response
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('view', [$student, 'student']);
        $data['student'] = $student;

        //restrict parents from seeing other students profiles
        if (auth()->user()->hasRole('parent') && $student->parents()->where('parent_records.user_id', auth()->user()->id)->count() <= 0) {
            abort(404);
        }

        return $this->student->printProfile($data['student']->name, 'pages.student.print-student-profile', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $student): View
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('update', [$student, 'student']);
        $data['student'] = $student;

        return view('pages.student.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $student): RedirectResponse
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('update', [$student, 'student']);
        $data = $request->except('_token', '_method');
        $this->student->updateStudent($student, $data);

        return back()->with('success', 'Student Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $student): RedirectResponse
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('delete', [$student, 'student']);
        $this->student->deleteStudent($student);

        return back()->with('success', 'Student Deleted Successfully');
    }
}
