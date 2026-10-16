<?php

namespace App\Livewire;

use App\Livewire\Concerns\EditsPersonDetails;
use App\Models\User;
use App\Services\Admin\AdminService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Change the identity and contact details of an administrator.
 */
class EditAdminForm extends Component
{
    use EditsPersonDetails;
    use WithFileUploads;

    #[Locked]
    public User $admin;

    public function mount(): void
    {
        Gate::authorize('update', [$this->admin, 'admin']);

        $this->fillPersonDetails($this->admin);
    }

    public function save(AdminService $adminService): void
    {
        Gate::authorize('update', [$this->admin, 'admin']);

        $this->trimPersonDetails();
        $rules = $this->personDetailRules();
        $rules['email'][] = Rule::unique('users', 'email')->ignore($this->admin->id);
        $this->validate($rules, [], $this->personDetailAttributes());

        try {
            $adminService->updateAdmin($this->admin, $this->personDetails());
        } catch (ValidationException $exception) {
            $this->showPersonDetailErrors($exception);

            return;
        }

        session()->flash('success', 'The changes were saved.');
        $this->redirectRoute('admins.show', $this->admin);
    }

    public function render(): View
    {
        return view('livewire.edit-admin-form');
    }
}
