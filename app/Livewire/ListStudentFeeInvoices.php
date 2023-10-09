<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ListStudentFeeInvoices extends Component
{
    public User $student;
    public $feeInvoices;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());

        $feeInvoices = $this->student->feeInvoices;
    }

    public function render()
    {
        return view('livewire.list-student-fee-invoices');
    }
}
