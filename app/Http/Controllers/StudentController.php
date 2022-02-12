<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Student\StudentService;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\StudentPromoteRequest;

class StudentController extends Controller
{
    public $student;
    //construct method which assigns studentservice to student variable

    public function __construct(StudentService $student)
    {
        $this->student = $student;

        //resource controller cant be used because of the role dependance of the user policy which needs be passed as a third parameter
        // $this->authorizeResource(User::class, 'student');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny',[ User::class, 'student']);
        return view('pages.student.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create',[ User::class, 'student']);
        return view('pages.student.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StudentStoreRequest $request)
    {
        $this->authorize('create',[ User::class, 'student']);
        $this->student->createStudent($request);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  User  $student
     * @return \Illuminate\Http\Response
     */
    public function show(User $student)
    {
        $this->authorize('view',[ $student, 'student']);
        $data['student'] = $student;

        return view('pages.student.show', $data);
    }

    /**
     * Print student Profile
     */
    public function printProfile(User $student)
    {
        $data['student'] = $student;

        return $this->student->createPdfFromView($data['student']->name, 'pages.student.print-student-profile', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $student)
    {
        $this->authorize('update',[ $student, 'student']);
        $data['student'] = $student;

        return view('pages.student.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,User $student)
    {
        $this->authorize('update',[ $student, 'student']);
        $data = $request->except('_token', '_method');
        $this->student->updateStudent($student, $data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('destroy',[ $student, 'student']);
    }

    /**
     * promote view 
     */
    public function promoteView()
    {
    return view('pages.student.promote');
    }

    /**
    * promote student
    */
    public function promote(StudentPromoteRequest $request)
    {
        $data = collect($request->except('_token'));
        $this->student->promoteStudent($data);

        return back();
    }
}
