<?php

namespace App\Livewire;

use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\LibraryCopy;
use App\Models\LibraryLendingRules;
use Brick\Money\Money as BrickMoney;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * How long this campus lends for, and to how many people.
 *
 * The rules start at sensible values, so nobody has to fill this in before
 * lending a first book.
 */
class LibraryLendingRulesForm extends Component
{
    use DispatchesStatusNotifications;

    public string $loanDays = '';

    public string $renewalsAllowed = '';

    public string $holdDays = '';

    public string $learnerLimit = '';

    public string $staffLimit = '';

    public string $finePerDay = '';

    public function mount(): void
    {
        Gate::authorize('create', LibraryCopy::class);

        $rules = LibraryLendingRules::forSchool();

        $this->loanDays = (string) $rules->loan_days;
        $this->renewalsAllowed = (string) $rules->renewals_allowed;
        $this->holdDays = (string) $rules->hold_days;
        $this->learnerLimit = (string) $rules->learner_limit;
        $this->staffLimit = (string) $rules->staff_limit;
        $this->finePerDay = (string) $rules->dailyFine()->getAmount();
    }

    public function save(): void
    {
        Gate::authorize('create', LibraryCopy::class);

        $this->validate([
            'loanDays' => ['required', 'integer', 'min:1', 'max:365'],
            'renewalsAllowed' => ['required', 'integer', 'min:0', 'max:10'],
            'holdDays' => ['required', 'integer', 'min:1', 'max:30'],
            'learnerLimit' => ['required', 'integer', 'min:1', 'max:100'],
            'staffLimit' => ['required', 'integer', 'min:1', 'max:200'],
            'finePerDay' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:100000'],
        ], [], [
            'loanDays' => 'days a loan lasts',
            'renewalsAllowed' => 'renewals allowed',
            'holdDays' => 'days to collect a hold',
            'learnerLimit' => 'items a learner may hold',
            'staffLimit' => 'items a member of staff may hold',
            'finePerDay' => 'cost of a late day',
        ]);

        // One row per campus. Two first saves at once must not collide on it.
        LibraryLendingRules::query()->upsert([[
            'school_id' => current_school_id(),
            'loan_days' => (int) $this->loanDays,
            'renewals_allowed' => (int) $this->renewalsAllowed,
            'hold_days' => (int) $this->holdDays,
            'learner_limit' => (int) $this->learnerLimit,
            'staff_limit' => (int) $this->staffLimit,
            'fine_per_day' => BrickMoney::of($this->finePerDay, config('app.currency'))->getMinorAmount()->toInt(),
            'updated_by' => auth()->id(),
        ]], ['school_id'], ['loan_days', 'renewals_allowed', 'hold_days', 'learner_limit', 'staff_limit', 'fine_per_day', 'updated_by']);

        $this->notify('The lending rules were saved.');
    }

    public function render(): View
    {
        return view('livewire.library-lending-rules-form');
    }
}
