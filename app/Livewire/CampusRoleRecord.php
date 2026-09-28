<?php

namespace App\Livewire;

use App\Actions\Authorization\AssignCampusRole;
use App\Actions\Authorization\WriteCampusRole;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\CampusRole;
use App\Models\User;
use App\Services\Authorization\RoleAuthority;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * One role at this campus: what it hands out and who holds it here.
 *
 * A shared role is one row for every campus. Changing one here changes this
 * campus's own copy, and only the people who hold it at this campus are shown.
 */
class CampusRoleRecord extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public CampusRole $role;

    public bool $isEditing = false;

    public string $description = '';

    /**
     * The ticked permissions, as the browser sends them back.
     *
     * @var array<int, string>
     */
    public array $permissions = [];

    public string $personId = '';

    public string $copyName = '';

    public function mount(CampusRole $role): void
    {
        Gate::authorize('assign', $role);

        $this->role = $role;
    }

    public function startEditing(): void
    {
        Gate::authorize('update', $this->role);

        $role = $this->role->fresh('permissions') ?? $this->role;
        $this->isEditing = true;
        $this->description = (string) $role->description;
        $this->permissions = $role->permissions->pluck('name')->values()->all();
    }

    public function stopEditing(): void
    {
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function save(WriteCampusRole $writeCampusRole, RoleAuthority $roleAuthority): void
    {
        Gate::authorize('update', $this->role);

        $this->description = trim($this->description);

        $this->validate([
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::in($roleAuthority->grantableBy(auth()->user(), current_school())->all())],
        ], [
            'permissions.*.in' => 'You can only put in a role what you can do yourself here.',
        ]);

        try {
            $changed = $writeCampusRole->update(
                role: $this->role,
                school: current_school(),
                permissions: array_values($this->permissions),
                description: $this->description === '' ? null : $this->description,
                actor: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('permissions', $exception->getMessage());

            return;
        }

        $this->isEditing = false;
        $this->followCopy($changed, "$changed->name was changed.");
    }

    public function archive(WriteCampusRole $writeCampusRole): void
    {
        Gate::authorize('archive', $this->role);

        try {
            $changed = $writeCampusRole->archive($this->role, current_school(), auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->followCopy($changed, "$changed->name is no longer offered. The people holding it keep it.");
    }

    public function restore(WriteCampusRole $writeCampusRole): void
    {
        Gate::authorize('archive', $this->role);

        try {
            $changed = $writeCampusRole->restore($this->role, current_school(), auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->followCopy($changed, "$changed->name is offered again.");
    }

    public function give(AssignCampusRole $assignCampusRole): void
    {
        Gate::authorize('assign', $this->role);

        $this->validate([
            'personId' => ['required', 'integer', Rule::in(User::ofSchool()->pluck('users.id')->all())],
        ], [
            'personId.in' => 'That person does not work at this campus.',
        ], ['personId' => 'person']);

        try {
            $assignCampusRole->give(User::findOrFail((int) $this->personId), $this->role, current_school(), auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('personId', $exception->getMessage());

            return;
        }

        $this->reset('personId');
        $this->notify("{$this->role->name} was given.");
    }

    public function take(int $userId, AssignCampusRole $assignCampusRole, RoleAuthority $roleAuthority): void
    {
        Gate::authorize('assign', $this->role);

        $holder = $roleAuthority->holdersAt($this->role, current_school())->findOrFail($userId);

        try {
            $assignCampusRole->take($holder, $this->role, current_school(), auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify("{$this->role->name} was taken from $holder->name.");
    }

    public function duplicate(WriteCampusRole $writeCampusRole): void
    {
        Gate::authorize('create', CampusRole::class);
        Gate::authorize('assign', $this->role);

        $this->copyName = trim($this->copyName);

        $this->validate(['copyName' => ['required', 'string', 'max:100']], [], ['copyName' => 'name of the copy']);

        try {
            $copy = $writeCampusRole->duplicate($this->role, current_school(), $this->copyName, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('copyName', $exception->getMessage());

            return;
        }

        session()->flash('success', "$copy->name is a copy of {$this->role->name}.");
        $this->redirectRoute('roles.edit', $copy->id);
    }

    public function render(RoleAuthority $roleAuthority): View
    {
        $this->role->refresh()->load('permissions:id,name');
        $holders = $roleAuthority->holdersAt($this->role, current_school())->orderBy('name')->get(['users.id', 'users.name', 'users.email']);

        return view('livewire.campus-role-record', [
            'canWrite' => Gate::allows('update', $this->role),
            'canCopy' => Gate::allows('create', CampusRole::class),
            'isShared' => $this->role->school_id === null,
            'grantable' => $roleAuthority->grantableBy(auth()->user(), current_school()),
            'holders' => $holders,
            'people' => User::ofSchool()
                ->whereNotIn('users.id', $holders->pluck('id'))
                ->whereDoesntHave('studentRecords', fn (Builder $enrollments): Builder => $enrollments->whereIn('status', EnrollmentStatus::enrolled()))
                ->orderBy('name')
                ->get(['users.id', 'users.name', 'users.email']),
        ]);
    }

    /**
     * Stay on the role, or move to this campus's own copy when one was made.
     */
    private function followCopy(CampusRole $changed, string $message): void
    {
        if ($changed->id === $this->role->id) {
            $this->notify($message);

            return;
        }

        session()->flash('success', "$message It is now this campus's own, so other campuses keep theirs.");
        $this->redirectRoute('roles.edit', $changed->id);
    }
}
