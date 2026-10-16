<?php

namespace App\Livewire;

use App\Actions\Boarding\ManageBoardingHouse;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Dormitory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Open a boarding house, or change one.
 */
class DormitoryForm extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public ?Dormitory $dormitory = null;

    public string $name = '';

    public string $label = 'House';

    public string $notes = '';

    public string $rooms = '10';

    public string $bedsPerRoom = '4';

    public bool $isActive = true;

    public function mount(?Dormitory $dormitory = null): void
    {
        if ($dormitory?->exists !== true) {
            Gate::authorize('create', Dormitory::class);

            return;
        }

        Gate::authorize('update', $dormitory);

        $this->dormitory = $dormitory;
        $this->name = $dormitory->name;
        $this->label = (string) $dormitory->label;
        $this->notes = (string) $dormitory->notes;
        $this->isActive = $dormitory->is_active;
    }

    public function save(ManageBoardingHouse $manageBoardingHouse): void
    {
        $dormitory = $this->dormitory;

        if ($dormitory === null) {
            Gate::authorize('create', Dormitory::class);
        } else {
            Gate::authorize('update', $dormitory);
        }

        $this->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('dormitories', 'name')->where('school_id', current_school_id())->ignore($dormitory?->id)],
            'label' => ['required', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'rooms' => [Rule::requiredIf($dormitory === null), 'nullable', 'integer', 'min:1', 'max:200'],
            'bedsPerRoom' => [Rule::requiredIf($dormitory === null), 'nullable', 'integer', 'min:1', 'max:40'],
        ], [
            'name.unique' => 'This campus already has a house with that name.',
            'rooms.required' => 'Say how many rooms the house has.',
            'bedsPerRoom.required' => 'Say how many beds are in each room.',
        ], [
            'bedsPerRoom' => 'beds in each room',
        ]);

        $notes = trim($this->notes) === '' ? null : trim($this->notes);

        if ($dormitory === null) {
            $opened = $manageBoardingHouse->open(current_school_id(), trim($this->name), trim($this->label), $notes, (int) $this->rooms, (int) $this->bedsPerRoom);
            session()->flash('success', "{$opened->name} is open.");
            $this->redirectRoute('dormitories.show', $opened);

            return;
        }

        try {
            $changed = $manageBoardingHouse->change($dormitory, trim($this->name), trim($this->label), $notes, $this->isActive);
        } catch (InvalidValueException $exception) {
            $this->addError('isActive', $exception->getMessage());

            return;
        }

        session()->flash('success', "{$changed->name} was changed.");
        $this->redirectRoute('dormitories.show', $changed);
    }

    public function render(): View
    {
        return view('livewire.dormitory-form', [
            'hasBoarders' => $this->dormitory !== null && $this->dormitory->is_active && app(ManageBoardingHouse::class)->hasCurrentBoarders($this->dormitory),
        ]);
    }
}
