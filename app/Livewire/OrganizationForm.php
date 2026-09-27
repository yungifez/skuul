<?php

namespace App\Livewire;

use App\Actions\Organization\CreateOrganization;
use App\Actions\Organization\UpdateOrganization;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Start an organization, or change what it is called and how to reach it.
 */
class OrganizationForm extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public ?Organization $organization = null;

    public string $name = '';

    public string $code = '';

    public string $address = '';

    public string $email = '';

    public string $phone = '';

    public function mount(?Organization $organization = null): void
    {
        if ($organization?->exists !== true) {
            Gate::authorize('create', Organization::class);

            return;
        }

        Gate::authorize('update', $organization);

        $this->organization = $organization;
        $this->name = $organization->name;
        $this->code = (string) $organization->code;
        $this->address = (string) $organization->address;
        $this->email = (string) $organization->email;
        $this->phone = (string) $organization->phone;
    }

    public function save(CreateOrganization $createOrganization, UpdateOrganization $updateOrganization): void
    {
        $organization = $this->organization;

        Gate::authorize(...($organization === null ? ['create', Organization::class] : ['update', $organization]));

        foreach (['name', 'code', 'address', 'email', 'phone'] as $field) {
            $this->{$field} = trim($this->{$field});
        }

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [Rule::requiredIf($organization !== null), 'nullable', 'string', 'max:50', 'alpha_dash', Rule::unique('organizations', 'code')->ignore($organization?->id)],
            'address' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ], [
            'code.unique' => 'Another organization already uses this code.',
        ]);

        $attributes = [
            'name' => $this->name,
            'address' => $this->address === '' ? null : $this->address,
            'email' => $this->email === '' ? null : $this->email,
            'phone' => $this->phone === '' ? null : $this->phone,
        ];

        if ($this->code !== '') {
            $attributes['code'] = $this->code;
        }

        if ($organization === null) {
            $organization = $createOrganization->create($attributes, auth()->user());
            session()->flash('success', "{$organization->name} is ready. Add its first campus.");
            $this->redirectRoute('organizations.show', $organization);

            return;
        }

        $updateOrganization->update($organization, $attributes, auth()->user());
        $this->notify('Saved.');
    }

    public function render(): View
    {
        return view('livewire.organization-form');
    }
}
