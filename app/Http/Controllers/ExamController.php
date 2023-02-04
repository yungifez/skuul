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
    public ExamService $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
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
        $this->examService->createExam($data);

        return back()->with('success', 'Exam created successfully');
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
        $this->examService->updateExam($exam, $data);

        return back()->with('success', 'Exam updated successfully');
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
        $this->examService->deleteExam($exam);

        return back()->with('success', 'Exam deleted successfully');
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
     * Tabulation for semester results.
     */
    public function semesterResultTabulation()
    {
        $this->authorize('viewAny', Exam::class);

        return view('pages.exam.semester-result-tabulation');
    }

     /**
     * Tabulation for academic year results.
     */
    public function academicYearResultTabulation()
    {
        $this->authorize('viewAny', Exam::class);

        return view('pages.exam.academic-year-result-tabulation');
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
    public function setExamActiveStatus(Exam $exam, UpdateExamStatusRequest $request)
    {
        $this->authorize('update', $exam);
        //get status from request
        $status = $request->status;
        $this->examService->setExamActiveStatus($exam, $status);

        return back()->with('success', 'Exam status updated successfully');
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
        $this->examService->setPublishResultStatus($exam, $status);

        return back()->with('success', 'Result published status updated successfully');
    }
}
