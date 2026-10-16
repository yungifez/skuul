<?php

namespace App\Http\Controllers;

use App\Models\LibraryCopy;
use App\Models\LibraryLoan;
use Illuminate\View\View;

/**
 * What the campus owns, and where each copy is.
 */
class LibraryCopyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(LibraryCopy::class, 'library_copy');
    }

    /**
     * Show the shelf, with what is out and who has it.
     */
    public function index(): View
    {
        return view('pages.library.index', [
            'onShelf' => LibraryCopy::inSchool()->available()->count(),
            'out' => LibraryLoan::inSchool()->open()->count(),
            'overdue' => LibraryLoan::inSchool()->overdue()->count(),
            'canManage' => auth()->user()?->can('manage library') === true,
        ]);
    }
}
