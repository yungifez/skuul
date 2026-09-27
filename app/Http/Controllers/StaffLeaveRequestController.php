<?php

namespace App\Http\Controllers;

use App\Models\StaffLeaveRequest;
use Illuminate\Contracts\View\View;

/**
 * The page where days away are asked for and answered.
 */
class StaffLeaveRequestController extends Controller
{
    /**
     * Show the leave the school has been asked for.
     */
    public function index(): View
    {
        $this->authorize('viewAny', StaffLeaveRequest::class);

        return view('pages.staff-leave.index');
    }
}
