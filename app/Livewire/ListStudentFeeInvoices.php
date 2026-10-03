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
     * List the bills this campus holds for the learner.
     *
     * A learner who moved to a campus that bills separately leaves their
     * unpaid bills behind. The old campus must still see them, so the list
     * covers every enrollment of the learner, not only the one here.
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
