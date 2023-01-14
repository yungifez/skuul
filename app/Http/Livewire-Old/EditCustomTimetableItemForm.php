<?php

namespace App\Http\Livewire;

use App\Models\CustomTimetableItem;
use Livewire\Component;

class EditCustomTimetableItemForm extends Component
{
    public CustomTimetableItem $customTimetableItem;

    public function render()
    {
        return view('livewire.edit-custom-timetable-item-form');
    }
}
