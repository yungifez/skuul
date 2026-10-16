<?php

namespace App\Livewire;

use App\Livewire\Concerns\EditsPersonDetails;
use App\Models\User;
use App\Services\Parent\ParentService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Change the identity and contact details of a parent.
 */
class EditParentForm extends Component
{
    use EditsPersonDetails;
    use WithFileUploads;

    #[Locked]
    public User $parent;

    public function mount(): void
    {
        Gate::authorize('update', [$this->parent, 'parent']);

        $this->fillPersonDetails($this->parent);
    }

    public function save(ParentService $parentService): void
    {
        Gate::authorize('update', [$this->parent, 'parent']);

        $this->trimPersonDetails();
        $rules = $this->personDetailRules();
        $rules['email'][] = Rule::unique('users', 'email')->ignore($this->parent->id);
        $this->validate($rules, [], $this->personDetailAttributes());

        try {
            $parentService->updateParent($this->parent, $this->personDetails());
        } catch (ValidationException $exception) {
            $this->showPersonDetailErrors($exception);

            return;
        }

        session()->flash('success', 'The changes were saved.');
        $this->redirectRoute('parents.show', $this->parent);
    }

    public function render(): View
    {
        return view('livewire.edit-parent-form');
    }
}
