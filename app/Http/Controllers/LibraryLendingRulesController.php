<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLibraryLendingRulesRequest;
use App\Models\LibraryCopy;
use App\Models\LibraryLendingRules;
use Brick\Money\Money as BrickMoney;
use Illuminate\Http\RedirectResponse;
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

        return view('pages.library.rules', [
            'rules' => LibraryLendingRules::forSchool(),
        ]);
    }

    /**
     * Save the rules.
     */
    public function update(UpdateLibraryLendingRulesRequest $request): RedirectResponse
    {
        $this->authorize('create', LibraryCopy::class);

        $rules = LibraryLendingRules::forSchool();

        $rules->fill([
            'school_id' => current_school_id(),
            'loan_days' => (int) $request->validated('loan_days'),
            'learner_limit' => (int) $request->validated('learner_limit'),
            'staff_limit' => (int) $request->validated('staff_limit'),
            'renewals_allowed' => (int) $request->validated('renewals_allowed'),
            'fine_per_day' => BrickMoney::of((string) $request->validated('fine_per_day'), config('app.currency'))
                ->getMinorAmount()
                ->toInt(),
            'updated_by' => auth()->id(),
        ])->save();

        return back()->with('success', 'The lending rules were saved.');
    }
}
