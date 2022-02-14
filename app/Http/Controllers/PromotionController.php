<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Student\StudentService;
use App\Http\Requests\StudentPromoteRequest;

class PromotionController extends Controller
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
    public function index()
    {
        return view('pages.student.promotions');
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
