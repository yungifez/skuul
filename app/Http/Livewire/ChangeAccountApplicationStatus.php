<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class ChangeAccountApplicationStatus extends Component
{
    public User $applicant;

    public $statuses;

    public $status;

    public bool $studentRecordFields = false;

    public function mount(User $applicant)
    {
        $this->applicant = $applicant;

        //return null if no applicant record
        if (is_null($this->applicant->accountApplication)) {
            return;
        }
        $this->statuses = $this->applicant->accountApplication->statuses()->get();
        $this->status != null && $this->status = $this->statuses[0];
        $this->updatedStatus();
    }

    public function updatedStatus()
    {
        if ($this->status == 'approved' && $this->applicant->accountApplication->role->name == 'student') {
            $this->studentRecordFields = true;
        } else {
            $this->studentRecordFields = false;
        }
    }

    public function render()
    {
        return view('livewire.change-account-application-status');
    }
}
