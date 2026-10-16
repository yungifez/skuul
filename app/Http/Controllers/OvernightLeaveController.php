<?php

namespace App\Http\Controllers;

use App\Models\OvernightLeave;
use Illuminate\View\View;

/**
 * Nights a boarder spends away from the house.
 */
class OvernightLeaveController extends Controller
{
    /**
     * Show who is out, who is overdue, and the requests waiting.
     */
    public function index(): View
    {
        $this->authorize('viewAny', OvernightLeave::class);

        return view('pages.boarding.leave.index');
    }
}
