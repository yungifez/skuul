<?php

namespace App\Http\Controllers;

use App\Models\LibraryReservation;
use Illuminate\View\View;

/**
 * The queue for titles everybody wants.
 */
class LibraryReservationController extends Controller
{
    /**
     * Show who is waiting and what is behind the desk.
     */
    public function index(): View
    {
        $this->authorize('viewAny', LibraryReservation::class);

        return view('pages.library.reservations');
    }
}
