<?php

namespace App\Livewire;

use App\Actions\Finance\RecordCashDeposit;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\RecordsOnce;
use App\Services\Finance\ChartOfAccounts;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Record cash that has been taken from the cash box to the bank.
 *
 * The form shows what the cash box holds in the books. A deposit above that
 * is usually a typing mistake, so it needs a second, explicit yes. It is not
 * refused, because a school may hold cash it never entered as an opening
 * balance.
 */
class RecordCashDepositForm extends Component
{
    use RecordsOnce;

    public string $amount = '';

    public string $depositDate = '';

    public string $bankReference = '';

    public string $note = '';

    public bool $confirmsMoreThanCashBox = false;

    public bool $isAboveCashBox = false;

    public function mount(): void
    {
        $this->ensureAllowed();

        $this->depositDate = now()->toDateString();
    }

    public function updatedAmount(): void
    {
        $this->isAboveCashBox = false;
        $this->confirmsMoreThanCashBox = false;
    }

    public function save(RecordCashDeposit $recordCashDeposit, ChartOfAccounts $chart): void
    {
        $this->ensureAllowed();

        $this->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'max:1000000000', 'decimal:0,2'],
            'depositDate' => ['required', 'date', 'before_or_equal:today'],
            'bankReference' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:2000'],
        ], [
            'depositDate.before_or_equal' => 'A deposit cannot be dated in the future.',
        ], [
            'depositDate' => 'date',
            'bankReference' => 'bank reference',
        ]);

        $amount = round((float) $this->amount, 2);
        $cashBox = $chart->account('cash')->balance();

        if ($amount > $cashBox && !$this->confirmsMoreThanCashBox) {
            $this->isAboveCashBox = true;
            $this->addError('amount', 'The cash box holds '.money_text($cashBox).' in the books. Check the amount, or confirm below.');

            return;
        }

        try {
            // A save sent again writes nothing and goes back to the list.
            $this->recordOnce('cash-deposit', fn () => $recordCashDeposit->record(
                amount: $amount,
                date: Carbon::parse($this->depositDate),
                bankReference: trim($this->bankReference) === '' ? null : trim($this->bankReference),
                note: trim($this->note) === '' ? null : trim($this->note),
            ));
        } catch (InvalidValueException $exception) {
            $this->addError('depositDate', $exception->getMessage());

            return;
        }

        session()->flash('success', 'Cash deposit recorded.');
        $this->redirectRoute('cash-deposits.index');
    }

    public function render(ChartOfAccounts $chart): View
    {
        return view('livewire.record-cash-deposit-form', [
            'cashBox' => $chart->account('cash')->balance(),
        ]);
    }

    private function ensureAllowed(): void
    {
        abort_unless(auth()->user()?->can('create cash deposit') === true, 403);
    }
}
