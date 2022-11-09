<?php

namespace App\Http\Livewire;

use App\Services\AccountApplication\AccountApplicationService;
use Livewire\Component;

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
