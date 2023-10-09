<?php

namespace App\Livewire;

use Livewire\Component;

class PayInvoiceForm extends Component
{
    public $feeInvoice;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());

        $this->feeInvoice->loadMissing('feeInvoiceRecords', 'feeInvoiceRecords.fee');
    }

    public function render()
    {
        return view('livewire.pay-invoice-form');
    }
}
