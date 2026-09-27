<?php

namespace App\Livewire;

use App\Models\FeeInvoice;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Show one invoice: who owes it, what is on it and what has been paid.
 *
 * A settled invoice prints as a receipt, so the page offers that instead of
 * taking another payment.
 */
class ShowFeeInvoice extends Component
{
    public FeeInvoice $feeInvoice;

    public function mount(): void
    {
        Gate::authorize('view', $this->feeInvoice);
    }

    public function render(): View
    {
        $this->feeInvoice->loadMissing([
            'user',
            'studentRecord.academicCycleSection.academicLevel',
            'feeInvoiceRecords.fee',
            'feeInvoiceRecords.allocations',
            'allocations.studentPayment',
        ]);

        $hasLines = $this->feeInvoice->feeInvoiceRecords->isNotEmpty();
        $isSettled = $hasLines && $this->feeInvoice->isSettled();

        return view('livewire.show-fee-invoice', [
            'hasLines' => $hasLines,
            'isSettled' => $isSettled,
            'isOverdue' => $hasLines && !$isSettled && $this->feeInvoice->due_date->lt(today()),
            'payments' => $this->feeInvoice->allocations
                ->groupBy('student_payment_id')
                ->map(fn ($allocations) => [
                    'payment' => $allocations->first()->studentPayment,
                    'amount' => $allocations->reduce(fn ($sum, $allocation) => $sum === null ? $allocation->amount : $sum->plus($allocation->amount)),
                ])
                ->filter(fn (array $row): bool => $row['payment'] !== null)
                ->sortByDesc(fn (array $row) => $row['payment']->received_on)
                ->values(),
            'canTakeMoney' => Gate::allows('update', $this->feeInvoice),
            'canViewAccount' => auth()->user()?->can('read fee invoice') === true,
        ]);
    }
}
