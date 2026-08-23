<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class ShowFeeInvoice extends Component
{
    public $feeInvoice;

    public function mount(): void
    {
        $this->feeInvoice->loadMissing([
            'user.studentRecord.academicCycleSection.academicLevel',
            'feeInvoiceRecords.fee',
            'feeInvoiceRecords.allocations',
            'payments',
        ]);
    }

    public function render(): View
    {
        return view('livewire.show-fee-invoice');
    }
}
