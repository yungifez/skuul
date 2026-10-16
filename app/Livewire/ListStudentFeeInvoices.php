<?php

namespace App\Livewire;

use App\Models\FeeInvoice;
use App\Models\User;
use Livewire\Component;

class ListStudentFeeInvoices extends Component
{
    public User $student;

    public $feeInvoices;

    /**
     * Read this campus's invoices across the learner's enrollments.
     * Separate billing preserves debts at the former campus after a move.
     */
    public function mount(): void
    {
        $this->feeInvoices = FeeInvoice::query()
            ->ofSchool()
            ->whereHas('studentRecord', fn ($enrollment) => $enrollment->where('user_id', $this->student->id))
            ->with(['feeInvoiceRecords', 'allocations'])
            ->orderByDesc('due_date')
            ->get();
    }

    public function render()
    {
        return view('livewire.list-student-fee-invoices');
    }
}
