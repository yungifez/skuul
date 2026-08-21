<?php

namespace App\Livewire;

use App\Models\AccountInvitation;
use App\Models\SchoolMembership;
use App\Models\User;
use Livewire\Component;

class ShowAdminProfile extends Component
{
    public User $admin;

    public ?SchoolMembership $membership = null;

    public ?AccountInvitation $pendingInvitation = null;

    public array $roles = [];

    public function mount(): void
    {
        $this->admin->load('roles');
        $this->membership = $this->admin->schoolMemberships()
            ->where('school_id', current_school_id())
            ->with('school')
            ->first();
        $this->pendingInvitation = $this->admin->pendingAccountInvitation();
        $this->roles = $this->admin->roles->pluck('name')->map(fn (string $role): string => str($role)->headline()->toString())->all();
    }

    public function render()
    {
        return view('livewire.show-admin-profile');
    }
}
