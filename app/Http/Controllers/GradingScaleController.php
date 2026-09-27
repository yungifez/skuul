<?php

namespace App\Http\Controllers;

use App\Models\GradingScale;
use Illuminate\View\View;

class GradingScaleController extends Controller
{
    /**
     * Display the school's reusable grading scales.
     */
    public function index(): View
    {
        $this->authorize('viewAny', GradingScale::class);

        return view('pages.grading-scale.index');
    }
}
