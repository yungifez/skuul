<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentPromoteRequest;
use App\Models\Promotion;
use App\Services\Student\StudentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public $student;

    public function __construct(StudentService $student)
    {
        $this->student = $student;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Promotion::class);

        return view('pages.student.promotion.index');
    }

    /**
     * promote view.
     */
    public function promoteView(): View
    {
        $this->authorize('promote', Promotion::class);

        return view('pages.student.promotion.promote');
    }

    /**
     * Promote student.
     */
    public function promote(StudentPromoteRequest $request): RedirectResponse
    {
        $this->authorize('promote', Promotion::class);
        $data = collect($request->except('_token'));
        $this->student->promoteStudents($data);

        return back()->with('success', 'Students Promoted Successfully');
    }

    /**
     * Reset promotion.
     */
    public function resetPromotion(Promotion $promotion): RedirectResponse
    {
        $this->authorize('reset', Promotion::class);
        $this->student->resetPromotion($promotion);

        return back()->with('success', 'Promotion Reset Successfully');
    }

    /**
     * Display the specified resource.
     *
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Promotion $promotion): View
    {
        $this->authorize('view', $promotion);

        return view('pages.student.promotion.show', compact('promotion'));
    }
}
