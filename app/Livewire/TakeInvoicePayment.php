<?php

namespace App\Livewire;

use App\Actions\Finance\ReceivePayment;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\RecordsOnce;
use App\Models\FeeInvoice;
use App\Services\Finance\PaymentChannelRegistry;
use Brick\Money\Money as BrickMoney;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Take money at the counter against one invoice.
 *
 * The money clears the oldest fees first unless the office splits it across
 * fees itself. Anything above what the invoice owes is held as credit.
 */
class TakeInvoicePayment extends Component
{
    use RecordsOnce;

    public FeeInvoice $feeInvoice;

    public string $amount = '';

    public string $receivedOn = '';

    public string $method = 'cash';

    public string $reference = '';

    public string $note = '';

    public bool $splitByFee = false;

    /** @var array<int|string, string|null> */
    public array $lines = [];

    public function mount(): void
    {
        Gate::authorize('update', $this->feeInvoice);

        $this->receivedOn = school_today()->toDateString();
    }

    public function save(ReceivePayment $receive, PaymentChannelRegistry $channels): void
    {
        Gate::authorize('update', $this->feeInvoice);

        $this->validate([
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:100000000'],
            'method' => ['required', 'string', Rule::in($channels->keys())],
            'reference' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
            'receivedOn' => ['nullable', 'date', 'before_or_equal:'.school_today()->toDateString()],
            'lines' => ['array'],
            'lines.*' => ['nullable', 'numeric', 'decimal:0,2', 'min:0'],
        ], [
            'amount.required' => 'Say how much money arrived.',
            'method.in' => 'This school does not take money that way.',
        ], ['receivedOn' => 'date received', 'lines.*' => 'amount for this fee']);

        $enrollment = $this->feeInvoice->studentRecord;

        if ($enrollment === null) {
            $this->addError('amount', 'This invoice does not belong to an enrolled student.');

            return;
        }

        try {
            $payment = $this->recordOnce('invoice-payment', fn () => $receive->receive(
                enrollment: $enrollment,
                amount: $this->minor($this->amount),
                method: $this->method,
                allocations: $this->allocationPlan(),
                onlyInvoice: $this->feeInvoice->id,
                reference: $this->reference === '' ? null : $this->reference,
                note: $this->note === '' ? null : $this->note,
                receivedOn: $this->receivedOn === '' ? null : now()->parse($this->receivedOn),
                source: $this->feeInvoice,
                schoolId: $this->feeInvoice->school_id,
            ));
        } catch (InvalidValueException $exception) {
            $this->addError($this->splitByFee ? 'lines' : 'amount', $exception->getMessage());

            return;
        }

        // A save sent again finds the payment taken and goes to the invoice.
        if ($payment === null) {
            $this->redirectRoute('fee-invoices.show', $this->feeInvoice);

            return;
        }

        $credit = $payment->unallocated();

        session()->flash('success', $credit->isPositive()
            ? 'Payment recorded. '.$credit->formatToLocale(app()->getLocale()).' is held as credit.'
            : 'Payment recorded.');

        $this->redirectRoute('fee-invoices.show', $this->feeInvoice);
    }

    public function render(PaymentChannelRegistry $channels): View
    {
        $this->feeInvoice->loadMissing(['user', 'studentRecord', 'feeInvoiceRecords.fee', 'feeInvoiceRecords.allocations']);

        return view('livewire.take-invoice-payment', [
            'channels' => $channels->all(),
            'openLines' => $this->feeInvoice->feeInvoiceRecords->filter(fn ($line) => $line->outstanding->isPositive())->values(),
        ]);
    }

    /**
     * Get the amount to write against each fee, in minor units.
     *
     * Nothing is returned when the money should clear the oldest fees first.
     *
     * @return array<int, int>|null
     */
    private function allocationPlan(): ?array
    {
        if (!$this->splitByFee) {
            return null;
        }

        $plan = [];

        foreach ($this->lines as $lineId => $share) {
            if ($share === null || $share === '' || (float) $share <= 0) {
                continue;
            }

            $plan[(int) $lineId] = $this->minor($share);
        }

        return $plan;
    }

    private function minor(string $amount): int
    {
        return BrickMoney::of($amount, config('app.currency'))->getMinorAmount()->toInt();
    }
}
