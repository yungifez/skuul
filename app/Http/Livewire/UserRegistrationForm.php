<?php

namespace App\Http\Livewire;

use App\Services\School\SchoolService;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserRegistrationForm extends Component
{
    public $roles;
    public $role;
    public $roleName;
    public $schools;
    public $school;
    protected $queryString = ['school', 'role'];

    public function mount(SchoolService $schoolService)
    {
        $this->schools = $schoolService->getAllSchools();
        $this->school = $this->school ?? $this->schools->first()->id;
        $this->roles = Role::whereIn('name', ['teacher', 'student', 'parent'])->get();
        $this->role = $this->role ?? $this->roles->first()->id;
        $this->updatedRole();
    }

    public function updatedRole()
    {
        $role = $this->roles->find($this->role);
        $this->roleName = $role->name;
    }

    public function updatedSchool()
    {
    }

    public function render()
    {
        return view('livewire.user-registration-form');
    }
}
