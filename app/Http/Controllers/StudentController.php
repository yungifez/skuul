<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Student\StudentService;
use App\Services\User\UserService;
use Illuminate\Auth\Access\AuthorizationException;
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

    public function __construct(StudentService $student, UserService $userService)
    {
        $this->student = $student;
        $this->userService = $userService;
    }

    public function index(): View
    {
        $this->authorize('viewAny', [User::class, 'student']);

        return view('pages.student.index');
    }

    public function create(): View
    {
        $this->authorize('create', [User::class, 'student']);

        return view('pages.student.create');
    }

    public function show(User $student): View|Response
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('view', [$student, 'student']);

        if (auth()->user()->readsLearnersOnlyAsGuardian() && $student->parents()->where('parent_records.user_id', auth()->user()->id)->count() <= 0) {
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

        if (auth()->user()->readsLearnersOnlyAsGuardian() && $student->parents()->where('parent_records.user_id', auth()->user()->id)->count() <= 0) {
            abort(404);
        }

        $data['studentRecord'] = $student->studentRecord()
            ->with([
                'academicCycleSection.academicLevel',
                'statusChanges.changedBy:id,name',
                'placements.academicYear',
                'placements.academicPeriod',
                'placements.academicCycleSection.academicLevel',
            ])
            ->first();
        $data['guardians'] = $student->parents()->with('user:id,name,email,phone')->get();

        return $this->student->printProfile($data['student']->name, 'pages.student.print-student-profile', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @throws AuthorizationException
     */
    public function edit(User $student): View
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($student, 'student');
        $this->authorize('update', [$student, 'student']);
        $data['student'] = $student;

        return view('pages.student.edit', $data);
    }
}
