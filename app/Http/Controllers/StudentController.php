<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentStoreRequest;
use App\Models\User;
use App\Services\Student\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public $student;

    //construct method which assigns studentService to student variable
    public function __construct(StudentService $student)
    {
        $this->student = $student;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', [User::class, 'student']);

        return view('pages.student.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', [User::class, 'student']);

        return view('pages.student.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StudentStoreRequest $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StudentStoreRequest $request)
    {
        $this->authorize('create', [User::class, 'student']);
        $this->student->createStudent($request);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param User $student
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $student)
    {
        $this->student->user->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('view', [$student, 'student']);
        $data['student'] = $student;

        return view('pages.student.show', $data);
    }

    /**
     * Print student Profile.
     */
    public function printProfile(User $student)
    {
        $this->student->user->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('view', [$student, 'student']);
        $data['student'] = $student;

        return $this->student->printProfile($data['student']->name, 'pages.student.print-student-profile', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $student
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $student)
    {
        $this->student->user->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('update', [$student, 'student']);
        $data['student'] = $student;

        return view('pages.student.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User                     $student
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $student)
    {
        $this->student->user->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('update', [$student, 'student']);
        $data = $request->except('_token', '_method');
        $this->student->updateStudent($student, $data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $student
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $student)
    {
        $this->student->user->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('delete', [$student, 'student']);
        $this->student->deleteStudent($student);

        return back();
    }
}
