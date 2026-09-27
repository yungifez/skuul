<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\View\View;

class PromotionController extends Controller
{
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
     * Display the specified resource.
     *
     *
     * @throws AuthorizationException
     */
    public function show(Promotion $promotion): View
    {
        $this->authorize('view', $promotion);

        return view('pages.student.promotion.show', compact('promotion'));
    }
}
