<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSlot;
use App\Services\Exam\ExamSlotService;
use Illuminate\Http\Response;
use Illuminate\View\View;

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
     */
    public function create(Exam $exam): View
    {
        $this->authorize('createForExam', [ExamSlot::class, $exam]);

        return view('pages.exam.exam-slot.create', compact('exam'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam, ExamSlot $examSlot): Response
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam, ExamSlot $examSlot): View
    {
        abort_unless($examSlot->exam_id === $exam->id, 404);

        return view('pages.exam.exam-slot.edit', compact('examSlot', 'exam'));
    }
}
