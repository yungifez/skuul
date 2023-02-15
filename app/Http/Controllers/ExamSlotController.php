<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSlot;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use App\Services\Exam\ExamSlotService;
use App\Http\Requests\StoreExamSlotRequest;
use App\Http\Requests\UpdateExamSlotRequest;

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
     */
    public function index(Exam $exam): View
    {
        return view('pages.exam.exam-slot.index', compact('exam'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(Exam $exam): View
    {
        return view('pages.exam.exam-slot.create', compact('exam'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreExamSlotRequest $request
     *
     */
    public function store(StoreExamSlotRequest $request, Exam $exam): RedirectResponse
    {
        $data = $request->except('_token');
        $this->examSlot->createExamSlot($exam, $data);

        return back()->with('success', 'Exam Slot Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ExamSlot $examSlot
     *
     */
    public function show(Exam $exam, ExamSlot $examSlot): Response
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ExamSlot $examSlot
     *
     */
    public function edit(Exam $exam, ExamSlot $examSlot): View
    {
        return view('pages.exam.exam-slot.edit', compact('examSlot', 'exam'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateExamSlotRequest $request
     * @param \App\Models\ExamSlot                     $examSlot
     *
     */
    public function update(UpdateExamSlotRequest $request, Exam $exam, ExamSlot $examSlot): RedirectResponse
    {
        $data = $request->except('_token', '_method');
        $this->examSlot->updateExamSlot($examSlot, $data);

        return back()->with('success', 'Exam Slot Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ExamSlot $examSlot
     *
     */
    public function destroy(Exam $exam, ExamSlot $examSlot): RedirectResponse
    {
        $this->examSlot->deleteExamSlot($examSlot);

        return back()->with('success', 'Exam Slot Deleted Successfully');
    }
}
