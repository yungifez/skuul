<?php

namespace App\Http\Livewire;

use App\Services\Admin\AdminService;
use Livewire\Component;

class ListAdminsTable extends Component
{
    public $admins;

    public function mount(AdminService $adminService)
    {
        $this->admins = $adminService->getAllAdmins();
    }

    public function render()
    {
        return view('livewire.list-admins-table');
    }
}
