<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Requests\UpdateExamStatusRequest;
use App\Models\Exam;
use App\Services\Exam\ExamService;

class ExamController extends Controller
{
    /**
     * @var ExamService
     */
    public ExamService $exam;

    public function __construct(ExamService $exam)
    {
        $this->exam = $exam;
        $this->authorizeResource(Exam::class, 'exam');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.exam.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.exam.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreExamRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExamRequest $request)
    {
        $data = $request->except('_token');
        $this->exam->createExam($data);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Exam $exam
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Exam $exam)
    {
        // return view('pages.exam.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Exam $exam
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Exam $exam)
    {
        return view('pages.exam.edit', compact('exam'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateExamRequest $request
     * @param \App\Models\Exam                     $exam
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        $data = $request->except(['_method', '_token']);
        $this->exam->updateExam($exam, $data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Exam $exam
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exam $exam)
    {
        $this->exam->deleteExam($exam);

        return back();
    }

    /**
     * Tabulation for exams.
     */
    public function examTabulation()
    {
        $this->authorize('viewAny', Exam::class);

        return view('pages.exam.tabulation');
    }

    /**
     * Tabulation for results.
     */
    public function resultTabulation()
    {
        $this->authorize('viewAny', Exam::class);

        return view('pages.exam.result-tabulation');
    }

    /**
     * Result checker.
     */
    public function resultChecker()
    {
        $this->authorize('checkResult', Exam::class);

        return view('pages.exam.result-checker');
    }

    /**
     * Set exam status.
     */
    public function setExamStatus(Exam $exam, UpdateExamStatusRequest $request)
    {
        $this->authorize('update', $exam);
        //get status from request
        $status = $request->status;
        $this->exam->setExamStatus($exam, $status);

        return back();
    }

    /**
     * Set publish result status.
     *
     * @param Exam                             $exam
     * @param UpdatePublishResultStatusRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function setPublishResultStatus(Exam $exam, UpdateExamStatusRequest $request)
    {
        $this->authorize('update', $exam);
        //get status from request
        $status = $request->status;
        $this->exam->setPublishResultStatus($exam, $status);

        return back();
    }
}
