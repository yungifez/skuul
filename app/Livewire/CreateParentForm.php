<?php

namespace App\Livewire;

use App\Livewire\Concerns\EditsPersonDetails;
use App\Models\User;
use App\Services\Parent\ParentService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Add a parent to the school and invite them to set a password.
 */
class CreateParentForm extends Component
{
    use EditsPersonDetails;
    use WithFileUploads;

    public function mount(): void
    {
        Gate::authorize('create', [User::class, 'parent']);
    }

    public function save(ParentService $parentService): void
    {
        Gate::authorize('create', [User::class, 'parent']);

        $this->trimPersonDetails();
        $this->validate($this->personDetailRules(), [], $this->personDetailAttributes());

        try {
            $parent = $parentService->createParent($this->personDetails());
        } catch (ValidationException $exception) {
            $this->showPersonDetailErrors($exception);

            return;
        }

        session()->flash('success', $parent->isAwaitingInvitationAcceptance()
            ? "{$parent->name} was added. We emailed them a link to set a password."
            : "{$parent->name} was added and can sign in with their existing password.");

        if (Gate::allows('view', [$parent, 'parent'])) {
            $this->redirectRoute('parents.show', $parent);

            return;
        }

        $this->redirectRoute('parents.create');
    }

    public function render(): View
    {
        return view('livewire.create-parent-form');
    }
}
