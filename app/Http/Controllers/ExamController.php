<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateExamStatusRequest;
use App\Models\Exam;
use App\Services\Exam\ExamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExamController extends Controller
{
    public ExamService $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
        $this->authorizeResource(Exam::class, 'exam');
    }

    public function index(): View
    {
        return view('pages.exam.index');
    }

    public function create(): View
    {
        return view('pages.exam.create');
    }

    public function edit(Exam $exam): View
    {
        return view('pages.exam.edit', compact('exam'));
    }

    /**
     * Set exam status.
     */
    public function setExamActiveStatus(Exam $exam, UpdateExamStatusRequest $request): RedirectResponse
    {
        $this->authorize('update', $exam);
        $status = $request->status;
        $this->examService->setExamActiveStatus($exam, $status);

        return back()->with('success', 'Exam status updated successfully');
    }
}
