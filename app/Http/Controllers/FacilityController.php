<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\View\View;

/**
 * The halls, laboratories, vehicles, and kit a campus shares.
 */
class FacilityController extends Controller
{
    /**
     * Show what the campus shares and what is booked next.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Facility::class);

        return view('pages.facility.index');
    }
}
