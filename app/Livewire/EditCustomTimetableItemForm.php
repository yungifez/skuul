<?php

namespace App\Livewire;

use App\Actions\Timetable\SaveCustomTimetableItem;
use App\Exceptions\InvalidValueException;
use App\Models\CustomTimetableItem;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Rename something a timetable holds that is not a lesson.
 */
class EditCustomTimetableItemForm extends Component
{
    #[Locked]
    public CustomTimetableItem $customTimetableItem;

    public string $name = '';

    public function mount(CustomTimetableItem $customTimetableItem): void
    {
        Gate::authorize('update', $customTimetableItem);

        $this->customTimetableItem = $customTimetableItem;
        $this->name = $customTimetableItem->name;
    }

    public function save(SaveCustomTimetableItem $saveCustomTimetableItem): void
    {
        Gate::authorize('update', $this->customTimetableItem);

        $this->name = trim($this->name);
        $this->validate(['name' => ['required', 'string', 'max:255']]);

        try {
            $saveCustomTimetableItem->rename($this->customTimetableItem, $this->name);
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The item was renamed. Every timetable that holds it shows the new name.');
        $this->redirectRoute('custom-timetable-items.index');
    }

    public function render(): View
    {
        return view('livewire.edit-custom-timetable-item-form');
    }
}
