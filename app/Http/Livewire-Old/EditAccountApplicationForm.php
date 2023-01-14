<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Services\School\SchoolService;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class EditAccountApplicationForm extends Component
{
    public User $applicant;
    public $roles;

    public function mount(SchoolService $schoolService)
    {
        $this->roles = Role::whereIn('name', ['teacher', 'student', 'parent'])->get();
    }

    public function render()
    {
        return view('livewire.edit-account-application-form');
    }
}
