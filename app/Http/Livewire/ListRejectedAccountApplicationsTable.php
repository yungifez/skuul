<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\AccountApplication\AccountApplicationService;

class ListRejectedAccountApplicationsTable extends Component
{
    public $accountApplications;

    public function mount(AccountApplicationService $accountApplicationService)
    {
        $this->applicants = $accountApplicationService->getAllRejectedApplicantsAndApplicationRecords()->load('accountApplication.role');
        
    }
    public function render()
    {
        return view('livewire.list-rejected-account-applications-table');
    }
}
