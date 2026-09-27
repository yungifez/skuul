<?php

namespace App\Livewire;

use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceRecord;
use App\Services\Fee\FeeInvoiceRecordService;
use App\Services\Fee\FeeInvoiceService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Change an invoice's due date and note, and its fees while it is unposted.
 *
 * A posted invoice is in the books, so its fees are shown but locked. A
 * mistake on it is fixed with a correcting invoice.
 */
class EditFeeInvoiceForm extends Component
{
    use DispatchesStatusNotifications;

    public FeeInvoice $feeInvoice;

    public string $dueDate = '';

    public string $note = '';

    public ?int $editingLineId = null;

    public ?int $lineAmount = null;

    public ?int $lineWaiver = null;

    public ?int $lineFine = null;

    public bool $isAdding = false;

    public ?int $feeCategoryId = null;

    public ?int $feeId = null;

    public ?int $newAmount = null;

    public ?int $newWaiver = null;

    public ?int $newFine = null;

    public function mount(): void
    {
        Gate::authorize('update', $this->feeInvoice);

        $this->dueDate = $this->feeInvoice->due_date->format('Y-m-d');
        $this->note = (string) $this->feeInvoice->note;
    }

    public function saveDetails(FeeInvoiceService $service): void
    {
        Gate::authorize('update', $this->feeInvoice);

        $this->validate([
            'dueDate' => ['required', 'date', 'after_or_equal:'.$this->feeInvoice->issue_date->format('Y-m-d')],
            'note' => ['nullable', 'string', 'max:10000'],
        ], ['dueDate.after_or_equal' => 'The due date cannot be before the invoice was issued.'], ['dueDate' => 'due date']);

        $service->updateFeeInvoice($this->feeInvoice, [
            'issue_date' => $this->feeInvoice->issue_date->format('Y-m-d'),
            'due_date' => $this->dueDate,
            'note' => $this->note === '' ? null : $this->note,
        ]);

        $this->notify('Invoice saved.');
    }

    public function startEditingLine(int $lineId): void
    {
        $line = $this->line($lineId);
        Gate::authorize('update', $line);

        $this->resetErrorBag();
        $this->editingLineId = $line->id;
        $this->lineAmount = $line->amount->getAmount()->toInt();
        $this->lineWaiver = $line->waiver->getAmount()->toInt();
        $this->lineFine = $line->fine->getAmount()->toInt();
    }

    public function saveLine(FeeInvoiceRecordService $service): void
    {
        $line = $this->line((int) $this->editingLineId);
        Gate::authorize('update', $line);

        $this->validate([
            'lineAmount' => ['required', 'integer', 'min:1'],
            'lineWaiver' => ['nullable', 'integer', 'min:0', 'lte:lineAmount'],
            'lineFine' => ['nullable', 'integer', 'min:0'],
        ], attributes: ['lineAmount' => 'amount', 'lineWaiver' => 'waiver', 'lineFine' => 'fine']);

        try {
            $service->updateFeeInvoiceRecord($line, ['amount' => $this->lineAmount, 'waiver' => $this->lineWaiver, 'fine' => $this->lineFine]);
        } catch (InvalidValueException $exception) {
            $this->addError('lineAmount', $exception->getMessage());

            return;
        }

        $this->cancel();
        $this->notify("{$line->fee?->name} updated.");
    }

    public function removeLine(int $lineId, FeeInvoiceRecordService $service): void
    {
        $line = $this->line($lineId);
        Gate::authorize('delete', $line);

        try {
            $service->deleteFeeInvoiceRecord($line);
        } catch (InvalidValueException $exception) {
            $this->addError('lines', $exception->getMessage());

            return;
        }

        $this->notify("{$line->fee?->name} removed.");
    }

    public function addLine(FeeInvoiceRecordService $service): void
    {
        Gate::authorize('create', FeeInvoiceRecord::class);

        $this->validate([
            'feeId' => ['required', 'integer', Rule::exists('fees', 'id')],
            'newAmount' => ['required', 'integer', 'min:1'],
            'newWaiver' => ['nullable', 'integer', 'min:0', 'lte:newAmount'],
            'newFine' => ['nullable', 'integer', 'min:0'],
        ], ['feeId.required' => 'Choose a fee.'], ['newAmount' => 'amount', 'newWaiver' => 'waiver', 'newFine' => 'fine']);

        try {
            $service->storeFeeInvoiceRecord([
                'fee_invoice_id' => $this->feeInvoice->id,
                'fee_id' => $this->feeId,
                'amount' => $this->newAmount,
                'waiver' => $this->newWaiver,
                'fine' => $this->newFine,
            ]);
        } catch (InvalidValueException $exception) {
            $this->addError('feeId', $exception->getMessage());

            return;
        }

        $this->reset('isAdding', 'feeId', 'newAmount', 'newWaiver', 'newFine');
        $this->notify('Fee added.');
    }

    public function updatedFeeCategoryId(): void
    {
        $this->feeId = null;
    }

    public function cancel(): void
    {
        $this->reset('editingLineId', 'lineAmount', 'lineWaiver', 'lineFine', 'isAdding', 'feeId', 'newAmount', 'newWaiver', 'newFine');
        $this->resetErrorBag();
    }

    public function render(): View
    {
        $this->feeInvoice->unsetRelation('feeInvoiceRecords');
        $this->feeInvoice->loadMissing(['user', 'studentRecord.academicCycleSection.academicLevel', 'feeInvoiceRecords.fee', 'feeInvoiceRecords.allocations']);

        $categories = FeeCategory::inSchool()->orderBy('name')->get();
        $this->feeCategoryId ??= $categories->first()?->id;

        return view('livewire.edit-fee-invoice-form', [
            'isPosted' => $this->feeInvoice->ledger_transaction_id !== null,
            'categories' => $categories,
            'fees' => $this->feeCategoryId === null ? collect() : Fee::query()
                ->where('fee_category_id', $this->feeCategoryId)
                ->whereRelation('feeCategory', 'school_id', current_school_id())
                ->whereNotIn('id', $this->feeInvoice->feeInvoiceRecords->pluck('fee_id'))
                ->orderBy('name')
                ->get(),
            'canAddLines' => Gate::allows('create', FeeInvoiceRecord::class),
        ]);
    }

    private function line(int $lineId): FeeInvoiceRecord
    {
        return FeeInvoiceRecord::query()->where('fee_invoice_id', $this->feeInvoice->id)->findOrFail($lineId);
    }
}
