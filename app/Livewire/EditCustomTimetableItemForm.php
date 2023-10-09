<?php

namespace App\Livewire;

use App\Models\CustomTimetableItem;
use Livewire\Component;

class EditCustomTimetableItemForm extends Component
{
    public CustomTimetableItem $customTimetableItem;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-custom-timetable-item-form');
    }
}
