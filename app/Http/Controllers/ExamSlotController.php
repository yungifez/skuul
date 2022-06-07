<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamSlotRequest;
use App\Http\Requests\UpdateExamSlotRequest;
use App\Models\Exam;
use App\Models\ExamSlot;
use App\Services\Exam\ExamSlotService;

class ExamSlotController extends Controller
{
    public ExamSlotService $examSlot;

    public function __construct(ExamSlotService $examSlot)
    {
        $this->examSlot = $examSlot;
        $this->authorizeResource(ExamSlot::class, 'exam_slot');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Exam $exam)
    {
        return view('pages.exam.exam-slot.index', compact('exam'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Exam $exam)
    {
        return view('pages.exam.exam-slot.create', compact('exam'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreExamSlotRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExamSlotRequest $request, Exam $exam)
    {
        $data = $request->except('_token');
        $this->examSlot->createExamSlot($exam, $data);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ExamSlot $examSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Exam $exam, ExamSlot $examSlot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ExamSlot $examSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Exam $exam, ExamSlot $examSlot)
    {
        return view('pages.exam.exam-slot.edit', compact('examSlot', 'exam'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateExamSlotRequest $request
     * @param \App\Models\ExamSlot                     $examSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExamSlotRequest $request, Exam $exam, ExamSlot $examSlot)
    {
        $data = $request->except('_token', '_method');
        $this->examSlot->updateExamSlot($examSlot, $data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ExamSlot $examSlot
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exam $exam, ExamSlot $examSlot)
    {
        $this->examSlot->deleteExamSlot($examSlot);

        return back();
    }
}
