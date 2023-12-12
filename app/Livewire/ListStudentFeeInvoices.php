<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ListStudentFeeInvoices extends Component
{
    public User $student;
    public $feeInvoices;

    public function render()
    {
        return view('livewire.list-student-fee-invoices');
    }
}
