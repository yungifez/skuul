<?php

namespace App\Livewire;

use App\Livewire\Concerns\EditsPersonDetails;
use App\Models\User;
use App\Services\Admin\AdminService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Add an administrator to the school and invite them to set a password.
 */
class CreateAdminForm extends Component
{
    use EditsPersonDetails;
    use WithFileUploads;

    public function mount(): void
    {
        Gate::authorize('create', [User::class, 'admin']);
    }

    public function save(AdminService $adminService): void
    {
        Gate::authorize('create', [User::class, 'admin']);

        $this->trimPersonDetails();
        $this->validate($this->personDetailRules(), [], $this->personDetailAttributes());

        try {
            $admin = $adminService->createAdmin($this->personDetails());
        } catch (ValidationException $exception) {
            $this->showPersonDetailErrors($exception);

            return;
        }

        session()->flash('success', $admin->isAwaitingInvitationAcceptance()
            ? "{$admin->name} was added. We emailed them a link to set a password."
            : "{$admin->name} was added and can sign in with their existing password.");

        if (Gate::allows('view', [$admin, 'admin'])) {
            $this->redirectRoute('admins.show', $admin);

            return;
        }

        $this->redirectRoute('admins.create');
    }

    public function render(): View
    {
        return view('livewire.create-admin-form');
    }
}
