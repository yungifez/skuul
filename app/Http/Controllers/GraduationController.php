<?php

namespace App\Http\Controllers;

use App\Models\Graduation;
use Illuminate\Contracts\View\View;

class GraduationController extends Controller
{
    public function index(): View
    {
        $this->authorize('ViewAny', Graduation::class);

        return view('pages.student.graduation.index');
    }

    /**
     * Graduate view.
     */
    public function graduateView(): View
    {
        $this->authorize('graduate', Graduation::class);

        return view('pages.student.graduation.graduate');
    }
}
