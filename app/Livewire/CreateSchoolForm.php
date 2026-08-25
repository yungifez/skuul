<?php

namespace App\Livewire;

use App\Enums\PlatformPermission;
use App\Models\Organization;
use Livewire\Component;
use Nnjeim\World\Models\Country;

class CreateSchoolForm extends Component
{
    public $organizations;

    public $countries;

    public function mount(): void
    {
        $user = auth()->user();

        $this->organizations = $user->can(PlatformPermission::AccessAllOrganizations)
            ? Organization::query()->orderBy('name')->get()
            : $user->organizations()->orderBy('name')->get();
        $this->countries = Country::query()->orderBy('name')->get(['name']);
    }

    public function render()
    {
        return view('livewire.create-school-form');
    }
}
