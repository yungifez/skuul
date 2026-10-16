<?php

namespace App\Http\Controllers;

use App\Models\LibraryCopy;
use Illuminate\View\View;

/**
 * How long this campus lends for, and to how many people.
 */
class LibraryLendingRulesController extends Controller
{
    /**
     * Show the rules the campus lends by.
     */
    public function edit(): View
    {
        $this->authorize('create', LibraryCopy::class);

        return view('pages.library.rules');
    }
}
