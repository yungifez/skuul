<?php

namespace App\Livewire;

use App\Actions\Timetable\SaveCustomTimetableItem;
use App\Exceptions\InvalidValueException;
use App\Models\CustomTimetableItem;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Name something a timetable can hold that is not a lesson.
 */
class CreateCustomTimetableItemForm extends Component
{
    public string $name = '';

    public function mount(): void
    {
        Gate::authorize('create', CustomTimetableItem::class);
    }

    public function save(SaveCustomTimetableItem $saveCustomTimetableItem): void
    {
        Gate::authorize('create', CustomTimetableItem::class);

        $this->name = trim($this->name);
        $this->validate(['name' => ['required', 'string', 'max:255']]);

        try {
            $item = $saveCustomTimetableItem->create($this->name);
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        session()->flash('success', "{$item->name} can now go on a timetable.");
        $this->redirectRoute('custom-timetable-items.index');
    }

    public function render(): View
    {
        return view('livewire.create-custom-timetable-item-form');
    }
}
