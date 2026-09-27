<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLibraryCopyRequest;
use App\Models\LibraryCopy;
use App\Models\LibraryLoan;
use App\Models\LibraryTitle;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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
            'titles' => LibraryTitle::forSchool()->orderBy('title')->limit(200)->get(),
            'onShelf' => LibraryCopy::inSchool()->available()->count(),
            'out' => LibraryLoan::inSchool()->open()->count(),
            'overdue' => LibraryLoan::inSchool()->overdue()->count(),
            'canManage' => auth()->user()?->can('manage library') === true,
        ]);
    }

    /**
     * Put one or more copies of a book on the shelf.
     */
    public function store(StoreLibraryCopyRequest $request): RedirectResponse
    {
        $made = DB::transaction(function () use ($request): int {
            $title = $request->validated('library_title_id') === null
                ? LibraryTitle::create([
                    'organization_id' => School::find(current_school_id())?->organization_id,
                    'title' => $request->validated('title'),
                    'authors' => $request->validated('authors'),
                    'isbn' => $request->validated('isbn'),
                    'category' => $request->validated('category'),
                    'published_year' => $request->validated('published_year'),
                ])
                : LibraryTitle::forSchool()->findOrFail($request->validated('library_title_id'));

            $wanted = (int) ($request->validated('copies') ?? 1);
            $barcode = $request->validated('barcode');

            for ($number = 0; $number < $wanted; $number++) {
                LibraryCopy::create([
                    'school_id' => current_school_id(),
                    'library_title_id' => $title->id,

                    // Asking for several copies numbers them from the barcode
                    // that was typed, so nobody types twenty of them.
                    'barcode' => $number === 0 ? $barcode : "$barcode-$number",
                    'shelf_mark' => $request->validated('shelf_mark'),
                ]);
            }

            return $wanted;
        });

        return back()->with('success', $made === 1 ? 'The copy is on the shelf.' : "$made copies are on the shelf.");
    }
}
