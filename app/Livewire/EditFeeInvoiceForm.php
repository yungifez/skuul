<?php

namespace App\Livewire;

use App\Models\FeeCategory;
use Livewire\Component;

class EditFeeInvoiceForm extends Component
{
    public $feeInvoice;
    public $feeCategories;
    public $feeCategory;
    public $fees;
    public $fee;
    public $errors;

    protected $rules = [
        'feeCategory' => 'integer|exists:fee_categories,id',
        'fee'         => 'nullable|integer',
    ];

    public function mount()
    {
        $this->feeInvoice->loadMissing('feeInvoiceRecords', 'feeInvoiceRecords.fee');
        $this->feeCategories = FeeCategory::inSchool()->get();
        if ($this->feeCategories != null && $this->feeCategories->isNotEmpty()) {
            $this->feeCategory = $this->feeCategories->first();
            $this->updatedFeeCategory();
        }
    }

    public function updatedFeeCategory()
    {
        $feeCategory = $this->feeCategories->find($this->feeCategory);

        if ($feeCategory == null || !$feeCategory->exists()) {
            return abort(404);
        }

        $this->fees = $feeCategory->fees()->whereNotIn('id', $this->feeInvoice->feeInvoiceRecords->pluck('fee_id'))->get();

        if ($this->fees != null && !$this->fees->isEmpty()) {
            $this->fee = $this->fees->first();
        }
    }

    public function render()
    {
        return view('livewire.edit-fee-invoice-form');
    }
}
