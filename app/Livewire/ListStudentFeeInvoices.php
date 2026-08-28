<?php

namespace App\Livewire;

use App\Models\FeeInvoice;
use App\Models\User;
use Livewire\Component;

class ListStudentFeeInvoices extends Component
{
    public User $student;

    public $feeInvoices;

    public function mount(): void
    {
        $this->student->load('studentRecord');
        $studentRecord = $this->student->studentRecord;

        $this->feeInvoices = $studentRecord === null
            ? collect()
            : FeeInvoice::query()
                ->ofSchool($studentRecord->school_id)
                ->where('student_record_id', $studentRecord->id)
                ->with(['feeInvoiceRecords', 'allocations'])
                ->orderByDesc('due_date')
                ->get();
    }

    public function render()
    {
        return view('livewire.list-student-fee-invoices');
    }
}
