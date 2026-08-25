<?php

namespace App\Http\Controllers;

use App\Actions\Library\IssueLoan;
use App\Actions\Library\IssueTitleToSection;
use App\Actions\Library\RenewLoan;
use App\Actions\Library\ReturnLoan;
use App\Http\Requests\StoreLibraryLoanRequest;
use App\Http\Requests\StoreLibrarySectionLoanRequest;
use App\Models\AcademicCycleSection;
use App\Models\LibraryCopy;
use App\Models\LibraryLendingRules;
use App\Models\LibraryLoan;
use App\Models\LibraryTitle;
use App\Models\User;
use App\Traits\ListsSchoolPeople;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * The lending desk: what is out, what is late, and what comes back.
 */
class LibraryLoanController extends Controller
{
    use ListsSchoolPeople;

    /**
     * Show what is out and what is overdue.
     */
    public function index(): View
    {
        $this->authorize('viewAny', LibraryLoan::class);

        return view('pages.library.loans', [
            'open' => LibraryLoan::inSchool()->open()->with(['copy.title', 'borrower'])->orderBy('due_on')->get(),
            'overdue' => LibraryLoan::inSchool()->overdue()->count(),
            'returned' => LibraryLoan::inSchool()
                ->whereNotNull('returned_on')
                ->with(['copy.title', 'borrower'])
                ->orderByDesc('id')
                ->limit(25)
                ->get(),
            'rules' => LibraryLendingRules::forSchool(),
            'borrowers' => $this->borrowers(),
            'sections' => AcademicCycleSection::inSchool()
                ->with(['academicLevel', 'academicYear'])
                ->orderBy('academic_year_id')
                ->orderBy('academic_level_id')
                ->orderBy('position')
                ->get(),
            'titles' => LibraryTitle::forSchool()
                ->whereHas('copies', fn ($query) => $query->where('school_id', current_school_id()))
                ->orderBy('title')
                ->get(),
            'canLend' => auth()->user()?->can('lend library item') === true,
        ]);
    }

    /**
     * Hand a copy to somebody.
     */
    public function store(StoreLibraryLoanRequest $request, IssueLoan $issue): RedirectResponse
    {
        $this->authorize('create', LibraryLoan::class);

        $copy = LibraryCopy::inSchool()->where('barcode', $request->validated('barcode'))->firstOrFail();
        $borrower = User::findOrFail($request->validated('user_id'));

        $issue->issue($copy, $borrower);

        return back()->with('success', 'The copy is out.');
    }

    /**
     * Lend one title to every attending learner in a section.
     */
    public function storeForSection(
        StoreLibrarySectionLoanRequest $request,
        IssueTitleToSection $issue,
    ): RedirectResponse {
        $this->authorize('create', LibraryLoan::class);

        $section = AcademicCycleSection::inSchool()->findOrFail(
            $request->integer('academic_cycle_section_id'),
        );
        $title = LibraryTitle::forSchool()
            ->whereHas('copies', fn ($query) => $query->where('school_id', current_school_id()))
            ->findOrFail($request->integer('library_title_id'));

        $loans = $issue->issue($section, $title, $request->user());

        return back()->with('success', "{$loans->count()} copies are out to the section.");
    }

    /**
     * Take a copy back, or give the borrower more time.
     */
    public function update(LibraryLoan $libraryLoan, Request $request, ReturnLoan $return, RenewLoan $renew): RedirectResponse
    {
        $this->authorize('update', $libraryLoan);

        $validated = $request->validate(['do' => 'required|in:return,renew']);

        if ($validated['do'] === 'renew') {
            $renew->renew($libraryLoan);

            return back()->with('success', 'The borrower has more time.');
        }

        $loan = $return->receive($libraryLoan);

        $message = $loan->fine_charged > 0
            ? 'The copy is back. A fine of '.$loan->fine()->formatToLocale(app()->getLocale()).' went on the account.'
            : 'The copy is back.';

        return back()->with('success', $message);
    }

    /**
     * Get everybody this campus may lend to.
     *
     * @return Collection<int, User>
     */
    private function borrowers(): Collection
    {
        return User::query()
            ->ofSchool()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toBase();
    }
}
