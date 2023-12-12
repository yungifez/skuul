<?php

namespace App\Livewire;

use App\Services\School\SchoolService;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class RegistrationForm extends Component
{
    public $roles;

    public $schools;

    public function mount(SchoolService $schoolService)
    {
        $this->schools = $schoolService->getAllSchools();
        $this->roles = Role::whereIn('name', ['teacher', 'student', 'parent'])->get();
    }

    public function render()
    {
        return view('livewire.registration-form');
    }
}
