<?php

namespace App\Livewire;

use App\Enums\PlatformPermission;
use App\Models\Organization;
use Livewire\Component;

class CreateSchoolForm extends Component
{
    public $organizations;

    public function mount(): void
    {
        $user = auth()->user();

        $this->organizations = $user->can(PlatformPermission::AccessAllOrganizations)
            ? Organization::query()->orderBy('name')->get()
            : $user->organizations()->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.create-school-form');
    }
}
