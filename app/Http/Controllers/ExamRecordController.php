<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRecordRequest;
use App\Http\Requests\UpdateExamRecordRequest;
use App\Models\ExamRecord;
use App\Services\Exam\ExamRecordService;

class ExamRecordController extends Controller
{
    public $examRecord;

    public function __construct(ExamRecordService $examRecord)
    {
        $this->examRecord = $examRecord;

        $this->authorizeResource(ExamRecord::class, 'exam_record');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.exam.exam-record.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.exam.exam-record.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreExamRecordRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExamRecordRequest $request)
    {
        $data = $request->except('_token');

        $this->examRecord->createExamRecord($data);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ExamRecord $examRecord
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ExamRecord $examRecord)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ExamRecord $examRecord
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamRecord $examRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateExamRecordRequest $request
     * @param \App\Models\ExamRecord                     $examRecord
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExamRecordRequest $request, ExamRecord $examRecord)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ExamRecord $examRecord
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExamRecord $examRecord)
    {
        //
    }
}
