<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRecordRequest;
use App\Http\Requests\UpdateExamRecordRequest;
use App\Models\ExamRecord;
use App\Services\Exam\ExamRecordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

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
     */
    public function index(): View
    {
        return view('pages.exam.exam-record.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.exam.exam-record.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExamRecordRequest $request): RedirectResponse
    {
        $data = $request->except('_token');

        $this->examRecord->createExamRecord($data);

        return back()->with('success', 'Exam Records Created/updated Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamRecord $examRecord): Response
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamRecord $examRecord): Response
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamRecordRequest $request, ExamRecord $examRecord): RedirectResponse
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamRecord $examRecord): Response
    {
        abort(404);
    }
}
