<?php

namespace App\Http\Controllers;

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

    public function index(){
        return view('pages.student.graduation.index');
    }

    // graduate view method

    public function graduateView()
    {
        return view('pages.student.graduation.graduate');
    }

    // graduate method

    public function graduate(StudentGraduateRequest $request)
    {
        $data = collect($request->except('_token'));
        $this->student->graduateStudents($data);

        return back();
    }

    //reset graduation method

    public function resetGraduation($student)
    {
        $this->student->resetGraduation($student);

        return back();
    }
}
