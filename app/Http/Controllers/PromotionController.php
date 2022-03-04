<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
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
        return view('pages.student.promotion.index');
    }

    /**
     * promote view 
     */
    public function promoteView()
    {
    return view('pages.student.promotion.promote');
    }

    /**
    * promote student
    */
    public function promote(StudentPromoteRequest $request)
    {
        $data = collect($request->except('_token'));
        $this->student->promoteStudents($data);

        return back();
    }

    /**
     * reset promotion
     */
    public function resetPromotion(Promotion $promotion){
        $this->student->resetPromotion($promotion);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Promotion $promotion)
    {
        return view('pages.student.promotion.show', compact('promotion'));
    }
}
