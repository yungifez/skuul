<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Student\StudentService;
use App\Http\Requests\StudentGraduateRequest;

class GraduationController extends Controller
{
    public $student;
    public function __construct(StudentService $student)
    {
        $this->student = $student;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // atuhorization is done in the controller
    public function index(){
        if (!auth()->user()->can('view graduations')) {
            return abort(403, 'Unauthorized action.');
        }
        return view('pages.student.graduation.index');
    }

    // graduate view method

    public function graduateView()
    {
        if (!auth()->user()->can('graduate student')) {
            return abort(403, 'Unauthorized action.');
        }
        return view('pages.student.graduation.graduate');
    }

    // graduate method

    public function graduate(StudentGraduateRequest $request)
    {
        if (!auth()->user()->can('graduate student')) {
            return abort(403, 'Unauthorized action.');
        }
        $data = collect($request->except('_token'));
        $this->student->graduateStudents($data);

        return back();
    }

    //reset graduation method

    public function resetGraduation(User $student)
    {
        if (!auth()->user()->can('reset graduation')) {
            return abort(403, 'Unauthorized action.');
        }
        $this->student->user->verifyUserIsOfRoleElseNotFound($student,'student');
        $this->student->resetGraduation($student);

        return back();
    }
}
