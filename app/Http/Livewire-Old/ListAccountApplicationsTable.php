<?php

namespace App\Http\Livewire;

use App\Services\AccountApplication\AccountApplicationService;
use Livewire\Component;

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
