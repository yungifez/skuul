<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\AccountApplication\AccountApplicationService;

class ListAccountApplicationsTable extends Component
{
    public $accountApplications;

    public function mount(AccountApplicationService $accountApplicationService)
    {
        $this->applicants = $accountApplicationService->getAllOpenApplicantsAndApplicationRecords()->load('accountApplication.role');
        
    }
    public function render()
    {
        return view('livewire.list-account-applications-table');
    }
}
