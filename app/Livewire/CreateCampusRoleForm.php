<?php

namespace App\Livewire;

use App\Actions\Authorization\WriteCampusRole;
use App\Exceptions\InvalidValueException;
use App\Models\CampusRole;
use App\Services\Authorization\RoleAuthority;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Write a role: a name and the permissions it hands out.
 *
 * Only what the author holds at this campus is on offer, so a role never
 * carries more authority than the person who wrote it.
 */
class CreateCampusRoleForm extends Component
{
    public string $name = '';

    public string $description = '';

    /**
     * The ticked permissions, as the browser sends them back.
     *
     * @var array<int, string>
     */
    public array $permissions = [];

    public function mount(): void
    {
        Gate::authorize('create', CampusRole::class);
    }

    public function save(WriteCampusRole $writeCampusRole, RoleAuthority $roleAuthority): void
    {
        Gate::authorize('create', CampusRole::class);

        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::in($roleAuthority->grantableBy(auth()->user(), current_school())->all())],
        ], [
            'permissions.*.in' => 'You can only put in a role what you can do yourself here.',
        ]);

        try {
            $role = $writeCampusRole->create(
                school: current_school(),
                name: $this->name,
                permissions: array_values($this->permissions),
                description: $this->description === '' ? null : $this->description,
                actor: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        session()->flash('success', "$role->name is ready. Give it to somebody below.");
        $this->redirectRoute('roles.edit', $role->id);
    }

    public function render(RoleAuthority $roleAuthority): View
    {
        return view('livewire.create-campus-role-form', [
            'grantable' => $roleAuthority->grantableBy(auth()->user(), current_school()),
        ]);
    }
}
