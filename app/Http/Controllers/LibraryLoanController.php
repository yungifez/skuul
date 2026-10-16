<?php

namespace App\Http\Controllers;

use App\Models\LibraryLoan;
use Illuminate\View\View;

/**
 * The lending desk of one campus.
 */
class LibraryLoanController extends Controller
{
    /**
     * Show the desk: what to scan, what is out, and what came back.
     */
    public function index(): View
    {
        $this->authorize('viewAny', LibraryLoan::class);

        return view('pages.library.loans');
    }
}
