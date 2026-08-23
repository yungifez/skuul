<?php

namespace App\Http\Controllers;

use App\Enums\LibraryCopyStatus;
use App\Http\Requests\StoreLibraryCopyRequest;
use App\Models\LibraryCopy;
use App\Models\LibraryLoan;
use App\Models\LibraryTitle;
use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
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
        $search = trim((string) request()->string('search'));

        $copies = LibraryCopy::inSchool()
            ->with(['title', 'loans' => fn ($loan) => $loan->whereNull('returned_on')->with('borrower')])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $match) use ($search): void {
                    $match->where('barcode', 'like', "%$search%")
                        ->orWhereHas('title', function (Builder $title) use ($search): void {
                            $title->where(function (Builder $named) use ($search): void {
                                $named->orWhere('title', 'like', "%$search%")
                                    ->orWhere('authors', 'like', "%$search%")
                                    ->orWhere('isbn', 'like', "%$search%");
                            });
                        });
                });
            })
            ->orderBy('barcode')
            ->paginate(20)
            ->withQueryString();

        return view('pages.library.index', [
            'copies' => $copies,
            'search' => $search,
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

    /**
     * Take a copy out of the library for good.
     */
    public function destroy(LibraryCopy $libraryCopy): RedirectResponse
    {
        if ($libraryCopy->isOut()) {
            return back()->with('danger', 'Somebody has this copy. Take it back first.');
        }

        // The copy is kept, because its loans are the library's history. It
        // simply stops being something anybody can borrow.
        $libraryCopy->status = LibraryCopyStatus::Withdrawn;
        $libraryCopy->save();

        return back()->with('success', 'The copy was taken out of the library.');
    }
}
