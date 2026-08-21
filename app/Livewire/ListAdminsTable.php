<?php

namespace App\Livewire;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\User;
use Livewire\Component;

class ListAdminsTable extends Component
{
    public int $totalAdmins = 0;

    public int $activeAdmins = 0;

    public int $invitedAdmins = 0;

    public int $suspendedAdmins = 0;

    public int $archivedAdmins = 0;

    public function mount(): void
    {
        $admins = User::query()->role(Role::Admin->value)->ofSchool();

        $this->totalAdmins = (clone $admins)->count();
        $this->activeAdmins = (clone $admins)->where('account_status', AccountStatus::Active)->count();
        $this->invitedAdmins = (clone $admins)->where('account_status', AccountStatus::Invited)->count();
        $this->suspendedAdmins = (clone $admins)->where('account_status', AccountStatus::Suspended)->count();
        $this->archivedAdmins = (clone $admins)->where('account_status', AccountStatus::Archived)->count();
    }

    public function render()
    {
        return view('livewire.list-admins-table');
    }
}
